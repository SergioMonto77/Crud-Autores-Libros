<?php

    session_start();
    $error=false;

    require __DIR__."/../../vendor/autoload.php";
    use Src\Autores;

    //compruebo que se pasa el id por get y que este existe en mi db
    if(!isset($_GET['idAutor'])){
        header('Location:index.php');
        die();
    }

    $id_autor= $_GET['idAutor'];
    $ids= Autores::devolverIds();
    if(!in_array($id_autor, $ids)){
        header('Location:index.php');
        die();
    }

    //recojo el autor gracias a mi id
    $autor= Autores::read($id_autor);

    function mostrarError($error){
        if(isset($_SESSION[$error])){
            echo "<p class='text-danger' style='font-size:0.75rem'>{$_SESSION[$error]}</p>";
            unset($_SESSION[$error]); //SESSION FLASH
        }
    }

    if(isset($_POST['enviarForm'])){

        //recojo los campos
        $nombre=trim(ucfirst($_POST['nombre']));
        $ape=trim(ucfirst($_POST['apellidos'])); //a los apellidos le puedo hacer trim ya que este solo les quita los espacios en blanco de delante y detrás

        //valido campos
        if(strlen($nombre)<3){
            $error=true;
            $_SESSION['error_nombre']="*** ERROR: el nombre debe tener al menos 3 carácteres!!";
        }

        if(strlen($ape)<6){
            $error=true;
            $_SESSION['error_apellidos']="*** ERROR: los apellidos deben tener al menos al menos 3 carácteres CADA UNO!!";
        }

        if($error){
            header("Location:{$_SERVER['PHP_SELF']}?idAutor=$id_autor");
            die();
        }

        //si hay algún error recargo mi página para mostrarlos. EOC creo el autor
        (new Autores)->setNombre($nombre)
        ->setApellidos($ape)
        ->update($id_autor);

        $_SESSION['mensaje']="Autor actualizado exitosamente";
        header('Location:index.php'); //creo el usuario y me redirijo a la página principal

    }else{
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <!-- FONTAWESOME  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- sweetalert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Editar Autor</title>
</head>

<body style="background-color:lemonchiffon">

    <h5 class="text-center mt-4">Editar Autor</h5>
        <div class="container">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']."?idAutor=$id_autor" ?>" class="mx-auto bg-secondary px-4 py-4 rounded" style="width:40rem">

                <div class="mb-4">
                    <label for="nom" class="form-label">Nombre</label>
                    <input type="text" id="nom" class="form-control" name="nombre" value="<?php echo $autor->nombre ?>" required />
                    <?php mostrarError('error_nombre') ?>
                </div>

                <div class="mb-4">
                    <label for="ape" class="form-label">Apellidos</label>
                    <input type="text" id="ape" class="form-control" name="apellidos" value="<?php echo $autor->apellidos ?>" required />
                    <?php mostrarError('error_apellidos') ?>
                </div>

                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-backward"></i> Volver
                </a>
                <button type="submit" name="enviarForm" class="btn btn-info">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <button type="reset" name="btn" class="btn btn-warning">
                    <i class="fas fa-paintbrush"></i> Limpiar
                </button>
            </form>
        </div>

</body>

</html>
<?php } ?>