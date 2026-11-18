# Hook de escritura de alta documental

Cambio preparado para la versión v24.3.1. Añade únicamente el método
protegido `guardar_archivo_alta`; por defecto llama al mismo escritor anterior.
Permite a una subclase explícita de Facturación reservar documentos privados y
registrar propiedad para compensación. No cambia el escritor global de plugins,
permisos de documentos existentes ni ninguna base activa.

## Legacy

13 pruebas documentales aprobadas en MariaDB efímera sin red, 84 aserciones
exitosas, cero incidencias PHPUnit/JUnit. Se mantuvieron instaladores/modelos
reales, ACL activas y todos los casos. Las dos posiciones de código/descripción
se ajustaron con autorización de Martín al orden físico del instalador vigente;
el archivo original íntegro y su hash se conservaron.

Los fixtures de archivos versionados se copiaron a tmpfs con identidad de bytes,
porque los casos los eliminan. Sólo se crearon metadatos y grupos ficticios de
esta instancia. No se importaron registros ni credenciales reales.

Motor cerrado mediante shutdown SQL local tras comprobar identidad; wait confirmó
salida. No se observaron procesos restantes en el namespace. Avisos WSREP sin
red quedaron registrados separadamente. No se usaron cuentas administrativas
activas ni se modificó configuración de seguridad del host.

El ensayo incluye este hook y plugins v6.22.10 baseline; **no** certifica la
integración del candidato OpenSSL ni una alta CSD completa. Esas pruebas se
registran y coordinan por separado en Facturación.
