<?php
/** @var controllers\controlador_session $controlador */
include $controlador->include_menu;
?>
<br>
<?php
if($controlador->existe_msj){
?>
<div class="alert alert-danger alert-dismissible fade show font_regular col-md-4 offset-4 pc info"  role="alert">
    <?php echo $controlador->mensaje_html; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="alert alert-danger alert-dismissible fade show font_regular col-md-12 mobile info" role="alert">
    <?php echo $controlador->mensaje_html; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php
}
?>
<br>
<br>
<div class="col-md-12 txt_centrado">
    <img src="./img/logo.webp" class="logo-centrado">
</div>
<br>
<br>
<div class="container">
    <form method="post" action="./index.php?seccion=session&accion=loguea">
        <div class="row justify-content-center crea-form">
            <div class="col-sm-3  negro_menu alto-medio"></div>
            <div class="col-md-12"></div>
            <div class="col-sm-3  form-template">
                <input type="text" class="form-control input-form" id="user" name ='user'
                       placeholder="Usuario" required>
            </div>
            <div class="col-md-12"></div>
            <div class="col-sm-3  form-template">
                <input type="password" class="form-control input-form" id="password" name ='password'
                       placeholder="Password" required>
            </div>
            <div class="col-md-12"></div>
            <br><br>
            <div class="row col-3">
                <div class="col-sm-12    align-h-center">
                    <button href="#" class="btn btn-dark btn-base" role="button" aria-pressed="true">Loguea</button>
                </div>
            </div>
        </div>
    </form>

</div>

