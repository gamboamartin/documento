# Regresión documental legacy en una base efímera

Ejecutor de las 13 pruebas reales registradas en `registro.json`. No contiene binarios, dependencias, datos reales ni credenciales reutilizables. La contraseña de configuración es exclusivamente ficticia para la instancia privada destruida al finalizar.

## Preparación y ejecución

Instalar las dependencias oficiales del checkout en `vendor` sin scripts ni plugins. Proveer un rootfs MariaDB 10.6.11 cuyo binario y archivos coincidan **exactamente** con `engine-pins.json`. El runner rechaza diferencias: no actualizar hashes para ocultar una incompatibilidad. Requiere Linux, Python 3, PHP 8.3 y extensiones indicadas en `inner.py`, bwrap, unshare, taskset, timeout y cliente mysql. No cambiar ajustes de seguridad del host para forzar ejecución.

Desde la raíz del repositorio:

```sh
python3 pruebas/legacy_documento/ejecutar.py --engine-root /ruta/al/rootfs-verificado > resultado-nuevo.jsonl 2> stderr-nuevo.txt
```

`--repo /ruta/al/checkout` es opcional. Usar un archivo nuevo para no sobrescribir evidencias. El runner verifica los hashes de tests/fixtures vigentes, mantiene el original recuperable y no modifica el checkout. Los fixtures que las pruebas eliminan se copian a tmpfs y verifican byte a byte antes de empezar.

## Controles

Red y PID aislados; dos CPU, límite AS 2 GiB, CPU 150 s, timeout 180/190 s y PHP 256 MiB. Motor sin TCP, eventos, binlog ni cargas de archivos externas. Todos los binds del host son readonly. Configuración sintética, schema `qa_documento`, cuenta limitada al schema, raíz sólo para bootstrap y shutdown de la instancia. DDL administrativos/proceso/documentales mediante instaladores oficiales; sólo grupos 1/2 ficticios. ACL activas (`aplica_seguridad=true`). No se usan configuraciones del host ni datos actuales.

Éxito exige exit 0, 13 casos JUnit y cero fallos/errores/omisiones, con flags que bloquean warnings/risky/incomplete. Un `die` con exit 0 y JUnit incompleto es BLOQUEADO. Antes de shutdown se comprueban server_id, datadir, socket, PID y ejecutable; se espera terminación real. Se informa por separado cualquier fallo de limpieza y limitación al inspeccionar /proc.

## Evidencia y cambio aprobado

Corrida local 08 (18-11-2026): 13 casos PASS, 84 eventos de aserción exitosa, cero incidencias PHPUnit/PHP. Shutdown SQL con identidad verificada y wait exit 0; sin procesos visibles del namespace. Avisos WSREP del motor por ausencia de dirección de red conservados, no confundidos con warnings PHPUnit. Evidencia completa local SHA256 cb31556d760f844ed1fc277a48cb27f16fc79ac54210179290ff63d790f3ea3b. El runner versionable también se ejecutó en corrida local 09: 13 casos, cero incidencias y shutdown verificado exit 0, sin cambiar expectativas. Se debe repetir antes de cualquier commit conforme a la regla del proyecto.

Sólo se intercambiaron las posiciones esperadas 1/2 del DESCRIBE, autorizado expresamente por Martín. `decision-orden.md`, `tests-originales.json` y `historial/instalacionTest-original.php` preservan decisión y original. No se cambió el esquema ni el orden semántico de importación ODS. Otras pruebas permanecen íntegras. La precreación DDL evita la caché de entidades incompleta del ORM durante instalación en el mismo proceso.

## Alcance

Pruebas ORM/controlador documentales reales con MariaDB sintética y hook opt-in candidato. No sustituyen alta integral CSD, validación criptográfica SAT, PAC, pruebas FPM ni despliegue. Corrida 08 usó plugins v6.22.10 instalado, no el candidato OpenSSL PHP. Ningún servidor activo ni servicio del host se modifica.
