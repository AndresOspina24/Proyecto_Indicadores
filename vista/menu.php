<?php 
  session_start();
  if($_SESSION['email']==null)header('Location: ../index.php');
?>
<?php include "../vista/base_ini_head.html" ?>
<?php include "../vista/base_ini_body.html" ?>
<!-- código html adicional -->
<div class="container">         
  <img src="../vista/img/Fondo2.jpg" class="rounded-circle" alt="Medellín" width="100%" height="500">      
</div>
<?php include "../vista/basePie.html" ?>