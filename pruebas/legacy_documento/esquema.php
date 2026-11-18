<?php
declare(strict_types=1);
require '/qa/bootstrap.php';
use gamboamartin\errores\errores;
$db = new config\database();
$link = new PDO('mysql:unix_socket=/run/mysqld/mysqld.sock;dbname=qa_documento', $db->db_user, $db->db_password,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$link->exec("SET sql_mode = ''");
$instaladores = [
    gamboamartin\administrador\instalacion\instalacion::class => [
        '_add_adm_grupo', '_add_adm_menu', '_add_adm_namespace', '_add_adm_seccion',
        '_add_adm_accion', '_add_adm_accion_grupo', '_add_adm_usuario'
    ],
    gamboamartin\proceso\instalacion\instalacion::class => [
        '_add_pr_tipo_proceso', '_add_pr_proceso', '_add_pr_etapa', '_add_pr_etapa_proceso'
    ],
    gamboamartin\documento\instalacion\instalacion::class => [
        '_add_doc_tipo_documento', '_add_doc_extension', '_add_doc_acl_tipo_documento',
        '_add_doc_extension_permitido', '_add_doc_documento', 'doc_version',
        '_add_doc_documento_etapa', '_add_doc_conf_tipo_documento_seccion'
    ]
];
$completados = [];
foreach ($instaladores as $clase => $metodos) {
    $objeto = new $clase();
    foreach ($metodos as $metodo) {
        $resultado = (new ReflectionMethod($objeto, $metodo))->invoke($objeto, $link);
        if (errores::$error) {
            echo json_encode(['estado' => 'BLOQUEADO', 'metodo' => $metodo, 'error' => $resultado], JSON_UNESCAPED_SLASHES);
            exit(1);
        }
        $completados[] = $metodo;
    }
}
// Grupos ficticios mínimos que las pruebas y el instalador documental referencian.
foreach ([1, 2] as $id) {
    $sql = 'INSERT INTO adm_grupo (id,codigo,descripcion,descripcion_select,codigo_bis,alias,root,solo_mi_info) VALUES (?,?,?,?,?,?,?,?)';
    $link->prepare($sql)->execute([$id,'QA-'.$id,'GRUPO FICTICIO '.$id,'GRUPO FICTICIO '.$id,'QA-'.$id,'QA-'.$id,'inactivo','inactivo']);
}
echo json_encode(['estado' => 'PASS', 'metodos' => $completados, 'grupos_ficticios' => [1,2]], JSON_UNESCAPED_SLASHES), PHP_EOL;
