<!DOCTYPE html>
<html lang="es-eu" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title></title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="./webroot/css/styles.css">
        <link rel="icon" href="./webroot/images/favicon2.png">
        <script>
            function mostrarContrasena() {
                var tipo = document.getElementById("password");
                if (tipo.type == "password") {
                    tipo.type = "text";
                } else {
                    tipo.type = "password";
                }
            }
        </script>
    </head>
    <body>
        <?php
        /* ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL); */

        include_once './core/validacionFormularios.php';
        include_once './config/configDB.php';

        $errorMsgReg = '';

        $a_respuesta = [
            username => null,
            email => null,
            password => null
        ];

        $a_errores = [
            username => null,
            email => null,
            password => null
        ];

        $entradaOK = true;

        try {
            $miDB = new PDO(HOST_DB, USER, PASSWORD);
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (isset($_POST['registrarse'])) {

                $a_errores[username] = validacionFormularios::comprobarAlfabetico($_POST['username'], 70, 3, 1);
                $a_errores[email] = validacionFormularios::validarEmail($_POST['email'], 70, 3, 1);
                $a_errores[password] = validacionFormularios::comprobarAlfaNumerico($_POST['password'], 70, 3, 1);
                $aleatorio = uniqid();

                $busqueda = $_POST['email'];
                $password = hash('sha256', $_POST['password']);

                $registroDuplicado = $miDB->query("SELECT * FROM users WHERE email = '$busqueda'");

                if ($registroDuplicado->rowCount() != 0) {
                    $a_errores[email] = 'Ese email ya existe.';
                }

                foreach ($a_errores as $value => $key) {
                    if ($key != null) {
                        $entradaOK = false;
                        $_POST[$value] = "";
                    }
                }
            } else {
                $entradaOK = false;
            }

            if ($entradaOK) {
                $a_respuesta[username] = $_POST['username'];
                $a_respuesta[email] = $_POST['email'];
                $a_respuesta[password] = $password;

                $sql = "INSERT INTO users VALUES (:username, :password, :email, '$aleatorio', 0)";

                $consulta = $miDB->prepare($sql);
                $consulta->bindParam(':username', $a_respuesta[username]);
                $consulta->bindParam(':email', $a_respuesta[email]);
                $consulta->bindParam(':password', $a_respuesta[password]);
                $consulta->execute();

                $username = $_POST['username'];

                $asunto = 'Activación de cuenta en forobet.website';
                $mensaje = "Bienvenid@ $username, gracias por tu registro.\n";
                $mensaje .= "Recuerda que la página web forobet.website aún se encuentra en construcción y esto es un preregistro. Puedes acceder a nuestro blog para estar al tanto de las últimas noticias: http://forobet.website/view/blog.php\n";
                $mensaje .= "Activa tu cuenta yendo al siguiente enlace:\n\n";
                $mensaje .= "http://forobet.website/view/blog.php?codigo=$aleatorio\n\n";
                $mensaje .= "ATENCIÓN\n";
                $mensaje .= "No reenvies este enlace a nadie.\n\n";
                $mensaje .= "No respondas a este mensaje.";
                $cabeceras = 'From: asistenteforobet@forobet.website' . "\r\n" .
                        'Reply-To: asistenteforobet@forobet.website' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

                if (mail($_POST[email], $asunto, $mensaje, $cabeceras)) {
                    ?>
                    <script>
                        alert("Correo enviado correctamente.");
                    </script>
                    <?php
                } else {
                    ?>
                    <script>
                        alert("Correo NO ha podido ser enviado.");
                    </script>
                    <?php
                }

                header('Location: ./view/blog.php');
            } else {
                ?>
                <header>
                    <nav class="navbar navbar-default navbar-inverse navbar-fixed-top">
                        <div class="container-fluid">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                                <a class="navbar-brand" onclick="return false">Proyecto Empresa</a>
                            </div>

                            <!-- <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                <ul class="nav navbar-nav navbar-right">
                                    <li><a href="#home">Home</a></li>
                                    <li><a href="#SignUp">Sign Up</a></li>
                                </ul>
                            </div> -->
                        </div>
                    </nav>
                </header>
                <div id="carousel-example-generic" class="carousel slide">
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                        <!-- <li data-target="#carousel-example-generic" data-slide-to="2"></li> -->
                    </ol>

                    <div class="carousel-inner" role="listbox">
                        <div class="item active">
                            <div class="banner" style="background-image: url(./webroot/images/bg1.jpg);"></div>
                            <div class="carousel-caption">
                                <!--<h2>Sign <span>In</span></h2>
                                <a href="#">Press here</a>-->
                                <section style="position: absolute !important; left: 30% !important; top: -100% !important;" id="cover">
                                    <div id="cover-caption">
                                        <div id="container" class="container">
                                            <div class="row text-white">
                                                <div class="col-sm-4 offset-sm-4 text-center">
                                                    <div class="info-form">
                                                        <h2>Sign <span>Up</span></h2>
                                                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form-inlin justify-content-center">
                                                            <div class="form-group">
                                                                <label class="sr-only">Username</label>
                                                                <input name="username" type="text" class="form-control" placeholder="forobet" value="<?php echo $_POST['username']; ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="sr-only">Email</label>
                                                                <input name="email" type="email" class="form-control" placeholder="isragarcia97@gmail.com" value="<?php echo $_POST['email']; ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="sr-only">Password</label>
                                                                <input name="password" type="password" class="form-control" placeholder="Password" value="<?php echo $_POST['password']; ?>">
                                                            </div>
                                                            <button name="registrarse" type="submit" class="btn btn-success">Registrarse</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="margin-top: 15px; position :relative !important;" class="alert alert-info col-sm-5 float-right" role="alert">
                                        Este registro enviará un enlace de validación a tu correo.
                                    </div>
                                </section>
                            </div>
                        </div>
                        <div class="item">
                            <div class="banner" style="background-image: url(./webroot/images/bg2.jpg);"></div>
                            <div class="carousel-caption">
                                <h2>Accede al <span>blog</span></h2>
                                <a href="http://www.forobet.website/view/blog.php">Click Aquí</a>
                            </div>
                        </div>
                        <!-- <div class="item">
                            <div class="banner" style="background-image: url(./webroot/images/bg3.jpg);"></div>
                            <div class="carousel-caption">

                            </div>
                        </div> -->
                    </div>

                    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                <?php
            }
        } catch (PDOException $pdoe) {
            echo "Error(" . $pdoe->getMessage() . ")";
        } finally {
            unset($miBD);
        }
        ?>
    </body>
</html>