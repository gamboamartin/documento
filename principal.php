<?php /** @var stdClass $data */?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/svg+xml" href="img/favicon/favicon.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo " Administrador "; ?></title>
<link rel="stylesheet" href="node_modules/jquery-ui-dist/jquery-ui.css">
<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap-grid.css">
<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap-reboot.css">
<link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="node_modules/bootstrap-select/dist/css/bootstrap-select.css">

<link rel="stylesheet" href="frontend/base.css">
<link rel="stylesheet" href="frontend/fonts.css">
<link rel="stylesheet" href="frontend/colores.css">
<link rel="stylesheet" href="frontend/menu.css">
<link rel="stylesheet" href="frontend/menu_mobile.css">
<link rel="stylesheet" href="frontend/lineamiento.css">
<link rel="stylesheet" href="frontend/resumen.css">

<script src="node_modules/jquery/dist/jquery.js"></script>
<script src="node_modules/jquery-ui-dist/jquery-ui.js"></script>
<script src="node_modules/popper.js/dist/umd/popper.js" ></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.js"></script>
<script src="node_modules/bootstrap-select/dist/js/bootstrap-select.js"></script>
<script src='node_modules/html5-qrcode/minified/html5-qrcode.min.js'></script>
<script type="text/javascript" src="node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="node_modules/google-charts/dist/loader.js"></script>
<script type="text/javascript" src="https://cdn.zingchart.com/zingchart.min.js"></script>

    <?php
echo $data->css_custom; ?>
<?php echo $data->js_view; ?>

</head>
<body>
    <?php  include($data->include_action); ?>
    <?php //include "templates/perfilador_formulario.php"; ?>
    <?php //include "templates/perfilador.php"; ?>
    <?php //include "templates/lineamiento.php"; ?>
    <?php //include "templates/proyecto.php"; ?>
    <?php //include "templates/comentarios.php"; ?>
    <?php //include "templates/menu_resumen.php"; ?>
    <?php //include "templates/tabla_resumen.php"; ?>
    <?php //include "templates/resumen.php"; ?>
    <?php //include "templates/hipoteca.php"; ?>
    <?php //include "templates/grafica.php"; ?>
</body>
</html>
