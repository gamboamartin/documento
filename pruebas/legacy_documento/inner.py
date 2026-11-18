import os,sys,json,pathlib,subprocess,time,hashlib,signal,xml.etree.ElementTree as ET
report={'estado':'BLOQUEADO','etapa':'aislamiento','pruebas_ejecutadas':False}
server=None
instance_id=str(int.from_bytes(os.urandom(4),'big')%999999998+1)
def detener(signum, frame):
    raise KeyboardInterrupt()
signal.signal(signal.SIGTERM, detener)
try:
    assert os.getuid()==1000
    assert len(os.sched_getaffinity(0))<=2
    assert os.readlink('/proc/self/ns/net') != os.environ['QA_OUTER_NET']
    assert not any(pathlib.Path('/home').iterdir()) and not pathlib.Path('/etc/mysql').exists()
    assert len(pathlib.Path('/proc/net/route').read_text().splitlines()) <= 1
    assert 'CapEff:\t0000000000000000' in pathlib.Path('/proc/self/status').read_text()
    mounts={line.split()[4]:line for line in pathlib.Path('/proc/self/mountinfo').read_text().splitlines()}
    for path in ['/var/lib/mysql','/run/mysqld','/var/log/mysql','/tmp','/var/www/html/documento/archivos']:
        assert ' - tmpfs tmpfs ' in mounts[path]
    for path in ['/','/dev','/var/www/html/documento/config','/var/www/html/documento/vendor']:
        assert 'ro' in mounts[path].split()[5].split(',')
    assert all(not list(pathlib.Path(p).iterdir()) for p in ['/var/lib/mysql','/run/mysqld','/var/log/mysql','/tmp'])
    for p in ['/var/lib/mysql','/run/mysqld','/var/log/mysql']:
        os.chmod(p,0o700)
    pathlib.Path('/tmp/mysql-temp').mkdir(mode=0o700)
    pathlib.Path('/var/lib/mysql/disabled-export').mkdir(mode=0o000)
    root=pathlib.Path('/var/www/html/documento')
    fixture_dest=root/'tests/files'
    assert not list(fixture_dest.iterdir())
    for source in pathlib.Path('/qa-fixtures-originales').iterdir():
        assert source.is_file() and not source.is_symlink()
        (fixture_dest/source.name).write_bytes(source.read_bytes())
    report['fixtures_copia_tmpfs']=True
    for name,digest in json.loads(pathlib.Path('/qa/tests-vigentes.json').read_text()).items():
        assert hashlib.sha256((root/name).read_bytes()).hexdigest()==digest
    report['aislamiento_verificado']=True
    report['netns']=os.readlink('/proc/self/ns/net')
    common=['/usr/sbin/mysqld','--no-defaults','--datadir=/var/lib/mysql','--socket=/run/mysqld/mysqld.sock','--pid-file=/run/mysqld/mysqld.pid','--tmpdir=/tmp/mysql-temp','--skip-networking','--skip-log-bin','--event-scheduler=OFF','--performance-schema=OFF','--local-infile=OFF','--secure-file-priv=/var/lib/mysql/disabled-export/','--innodb-use-native-aio=OFF','--max-connections=20','--open-files-limit=256','--table-open-cache=32','--table-open-cache-instances=1','--innodb-open-files=64','--innodb-buffer-pool-size=67108864','--key-buffer-size=16777216','--aria-pagecache-buffer-size=16777216','--innodb-read-io-threads=1','--innodb-write-io-threads=2','--innodb-log-buffer-size=8388608','--innodb-log-file-size=67108864','--innodb-temp-data-file-path=ibtmp1:12M:autoextend:max:64M','--basedir=/usr','--lc-messages-dir=/usr/share/mysql','--plugin-dir=/usr/lib/mysql/plugin','--server-id='+instance_id]
    sql='CREATE DATABASE mysql;\nUSE mysql;\nSET @auth_root_socket=NULL;\n'+'\n'.join(pathlib.Path('/usr/share/mysql/'+p).read_text() for p in ['mysql_system_tables.sql','mysql_performance_tables.sql','mysql_system_tables_data.sql','fill_help_tables.sql','maria_add_gis_sp_bootstrap.sql','mysql_sys_schema.sql'])
    report['etapa']='inicializar_motor'
    engine_env={'PATH':'/usr/bin:/usr/sbin','HOME':'/tmp','LANG':'C','MALLOC_ARENA_MAX':'2'}
    init=subprocess.run(common+['--bootstrap'],input=sql,text=True,capture_output=True,timeout=75,env=engine_env)
    report['inicializador_exit']=init.returncode
    if init.returncode:
        report['diagnostico_inicializador']=init.stderr[-5000:]
        raise RuntimeError('Inicialización fallida')
    log=open('/var/log/mysql/qa.log','wb')
    server=subprocess.Popen(common,stdout=log,stderr=log,env=engine_env)
    client=['/usr/bin/mysql','--no-defaults','--protocol=SOCKET','--socket=/run/mysqld/mysqld.sock','--user=root','--batch','--skip-column-names']
    end=time.monotonic()+15
    while time.monotonic()<end:
        if server.poll() is not None: raise RuntimeError('Motor terminó')
        if pathlib.Path('/run/mysqld/mysqld.sock').exists():
            ping=subprocess.run(client+['-e','SELECT 1'],capture_output=True,text=True,timeout=2)
            if ping.returncode==0: break
        time.sleep(.2)
    else: raise RuntimeError('Socket no disponible')
    report['etapa']='base_nueva'
    q=subprocess.run(client+['-e',"CREATE DATABASE qa_documento CHARACTER SET utf8mb4; CREATE USER 'qa_documento'@'localhost' IDENTIFIED BY 'QA-EFIMERA-SIN-VALIDEZ'; GRANT ALL PRIVILEGES ON qa_documento.* TO 'qa_documento'@'localhost'; SELECT @@skip_networking,@@event_scheduler,@@local_infile; SHOW DATABASES;"],capture_output=True,text=True,timeout=5)
    assert q.returncode==0
    report['verificacion_base']=q.stdout.splitlines()
    report['etapa']='phpunit'
    report['estado_motor_antes']=[x for x in pathlib.Path('/proc/'+str(server.pid)+'/status').read_text().splitlines() if x.startswith(('VmSize:','VmRSS:','Threads:'))]
    php=['/usr/bin/php8.3','-n']
    for ext in ['mysqlnd','pdo','pdo_mysql','mbstring','dom','xml','xmlwriter','xmlreader','zip','iconv','fileinfo','tokenizer','ctype','simplexml']:
        php+=['-d','extension='+ext]
    php+=['-d','pdo_mysql.default_socket=/run/mysqld/mysqld.sock','-d','allow_url_fopen=0','-d','allow_url_include=0','-d','memory_limit=256M','-d','disable_functions=shell_exec,exec,system,passthru,proc_open,popen,fsockopen,pfsockopen,stream_socket_client,curl_exec,curl_multi_exec,socket_create,socket_connect,ftp_connect',str(root/'vendor/phpunit/phpunit/phpunit'),'--bootstrap','/qa/bootstrap.php','--do-not-cache-result','--fail-on-warning','--fail-on-risky','--fail-on-skipped','--fail-on-incomplete','--log-junit','/tmp/junit.xml','--display-warnings','--display-deprecations','--debug',str(root/'tests')]
    env={'PATH':'/usr/bin','HOME':'/tmp','QA_DOCUMENTO_NETNS':'1'}
    schema_cmd=php[:php.index(str(root/'vendor/phpunit/phpunit/phpunit'))]+['/qa/esquema.php']
    schema=subprocess.run(schema_cmd,cwd=root,env=env,capture_output=True,text=True,timeout=30)
    report['schema_exit']=schema.returncode
    report['schema_salida']=schema.stdout[-12000:]
    report['schema_stderr']=schema.stderr[-3000:]
    if schema.returncode != 0: raise RuntimeError('Preparación de esquema sintético bloqueada')
    test=subprocess.run(php,cwd=root,env=env,capture_output=True,text=True,timeout=60)
    report['phpunit_exit']=test.returncode
    tablas=subprocess.run(client+['qa_documento','-e','SHOW TABLES'],env=engine_env,capture_output=True,text=True,timeout=5)
    report['tablas_reales_finales']=tablas.stdout.splitlines()
    if server.poll() is None:
        report['estado_motor_despues']=[x for x in pathlib.Path('/proc/'+str(server.pid)+'/status').read_text().splitlines() if x.startswith(('VmSize:','VmRSS:','Threads:'))]
    report['salida_phpunit']=test.stdout
    report['stderr_phpunit']=test.stderr[-3000:]
    report['phpunit_lanzado']=True
    if pathlib.Path('/tmp/junit.xml').is_file():
        junit=ET.parse('/tmp/junit.xml').getroot()
        cases=junit.findall('.//testcase')
        issues=junit.findall('.//failure')+junit.findall('.//error')+junit.findall('.//skipped')
        report['casos_junit']=len(cases)
        report['pruebas_ejecutadas']=len(cases)>0
        report['incidencias_junit']=len(issues)
        report['estado']='PASS' if test.returncode==0 and len(cases)==13 and not issues else 'BLOQUEADO'
    else:
        report['estado']='BLOQUEADO'
        report['junit_ausente']=True
except Exception as e:
    report['error']=str(e)
finally:
    if server is not None:
        try:
            if server.poll() is None:
                assert os.readlink('/proc/'+str(server.pid)+'/ns/net') == os.readlink('/proc/self/ns/net')
                assert pathlib.Path('/run/mysqld/mysqld.pid').read_text().strip() == str(server.pid)
                expected=json.loads(pathlib.Path('/qa/engine-pins.json').read_text())['usr/sbin/mariadbd']
                assert hashlib.sha256(pathlib.Path('/proc/'+str(server.pid)+'/exe').read_bytes()).hexdigest()==expected
                shutdown_php="""$p=new PDO('mysql:unix_socket=/run/mysqld/mysqld.sock','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$r=$p->query('SELECT CAST(@@server_id AS CHAR),@@datadir,@@socket')->fetch(PDO::FETCH_NUM);
if($r!==[$argv[1],'/var/lib/mysql/','/run/mysqld/mysqld.sock']){exit(3);}
echo 'identidad_verificada'.PHP_EOL;
$p->exec('SHUTDOWN');
echo 'shutdown_enviado'.PHP_EOL;"""
                shutdown=subprocess.run(['/usr/bin/php8.3','-n','-d','extension=mysqlnd','-d','extension=pdo','-d','extension=pdo_mysql','-r',shutdown_php,instance_id],capture_output=True,text=True,timeout=15)
                report['shutdown_exit']=shutdown.returncode
                report['shutdown_identidad_verificada']='identidad_verificada' in shutdown.stdout
                report['shutdown_enviado']='shutdown_enviado' in shutdown.stdout
                assert shutdown.returncode==0 and report['shutdown_identidad_verificada'] and report['shutdown_enviado']
                server.wait(timeout=15)
            report['motor_detenido']=server.poll() is not None
            report['motor_exit']=server.returncode
        except Exception as e:
            report['estado']='BLOQUEADO'
            report['cleanup_error']=type(e).__name__
            report['motor_detenido']=server.poll() is not None
            # No señales al proceso confinado. La destrucción del namespace es la frontera final.
    if pathlib.Path('/var/log/mysql/qa.log').is_file():
        report['log_motor_final']=pathlib.Path('/var/log/mysql/qa.log').read_text(errors='replace')[-4000:]
    print(json.dumps(report,ensure_ascii=False))
    sys.exit(0 if report['estado']=='PASS' else 1)
