<?php
  ob_start();
?>
<?php
include_once 'controlador/configBd.php';
include_once 'controlador/ControlEntidad.php';
include_once 'controlador/ControlConexionPdo.php';
include_once 'modelo/Entidad.php';
session_start();
$email="";
$contrasena="";
$boton="";

if(isset($_POST['txtEmail']))$email=$_POST['txtEmail'];
if(isset($_POST['txtContrasena']))$contrasena=$_POST['txtContrasena'];
if(isset($_POST['btnLogin']))$boton=$_POST['btnLogin'];
if($boton=="Login"){
  $validar=false;
  $sql="SELECT * FROM usuario WHERE email=? AND contrasena=?";
  $objControlEntidad=new ControlEntidad('usuario');
  $objUsuario=$objControlEntidad->consultar($sql,[$email,$contrasena]);
  if($objUsuario){
    $_SESSION['email']=$email;
    //$datosUsuario = ['email' => $email, 'contrasena' => $contrasena];
		//$objUsuario = new Entidad($datosUsuario);
    $objControlRolUsuario = new ControlEntidad('rol_usuario');
    $sql = "SELECT rol.id as id, rol.nombre as nombre
        FROM rol_usuario INNER JOIN rol ON rol_usuario.fkidrol = rol.id
        WHERE fkemail = ?";
    $parametros = [$email];
    $listaRolesDelUsuario = $objControlRolUsuario->consultar($sql, $parametros);
    $_SESSION['listaRolesDelUsuario']=$listaRolesDelUsuario;
    //var_dump($listaRolesDelUsuario);
    header('Location: ./vista/menu.php');
  }
  else header('Location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Bootstrap 5 Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
      .divider:after,
      .divider:before {
      content: "";
      flex: 1;
      height: 1px;
      background: #eee;
      }
      .h-custom {
      height: calc(100% - 73px);
      }
      @media (max-width: 450px) {
      .h-custom {
      height: 100%;
      }
      }
  </style>
  </head>
  <body>
    <section class="vh-100">
    <div class="container-fluid h-custom">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-md-9 col-lg-6 col-xl-5">
          <img src="./vista/img/MECHAS BRAND.jpg"
            class="img-fluid" alt="Sample image">
        </div>
        <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
          <form method="post">
            <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
              <p class="lead fw-normal mb-0 me-3">Sign in with</p>
              <button type="button" class="btn btn-primary btn-floating mx-1">
                <i class="fab fa-facebook-f"></i>
              </button>

              <button type="button" class="btn btn-primary btn-floating mx-1">
                <i class="fab fa-twitter"></i>
              </button>

              <button type="button" class="btn btn-primary btn-floating mx-1">
                <i class="fab fa-linkedin-in"></i>
              </button>
            </div>

            <div class="divider d-flex align-items-center my-4">
              <p class="text-center fw-bold mx-3 mb-0">Or</p>
            </div>

            <!-- Email input -->
            <div class="form-outline mb-4">
              <input type="email" id="txtEmail" name="txtEmail" class="form-control form-control-lg"
                placeholder="Enter a valid email address" />
              <label class="form-label" for="txtEmail">Email address</label>
            </div>

            <!-- Password input -->
            <div class="form-outline mb-3">
              <input type="password" id="txtContrasena" name="txtContrasena"  class="form-control form-control-lg"
                placeholder="Enter password" />
              <label class="form-label" for="txtContrasena">Password</label>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <!-- Checkbox -->
              <div class="form-check mb-0">
                <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
                <label class="form-check-label" for="form2Example3">
                  Remember me
                </label>
              </div>
              <a href="#!" class="text-body">Forgot password?</a>
            </div>

            <div class="text-center text-lg-start mt-4 pt-2">
              <input type="submit" class="btn btn-primary btn-lg" id="btnLogin"
                style="padding-left: 2.5rem; padding-right: 2.5rem;" name="btnLogin" value="Login">
              <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account? <a href="#!"
                  class="link-danger">Register</a></p>
            </div>

          </form>
        </div>
      </div>
    </div>
    <div
      class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
      <!-- Copyright -->
      <div class="text-white mb-3 mb-md-0">
        Copyright © 2020. All rights reserved.
      </div>
      <!-- Copyright -->

      <!-- Right -->
      <div>
        <a href="#!" class="text-white me-4">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="#!" class="text-white me-4">
          <i class="fab fa-twitter"></i>
        </a>
        <a href="#!" class="text-white me-4">
          <i class="fab fa-google"></i>
        </a>
        <a href="#!" class="text-white">
          <i class="fab fa-linkedin-in"></i>
        </a>
      </div>
      <!-- Right -->
    </div>
  </section>
  </body>
</html>
<?php
  ob_end_flush();
?>