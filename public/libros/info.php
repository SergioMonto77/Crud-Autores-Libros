<?php

    require __DIR__."/../../vendor/autoload.php";
    use Src\Libros;

    //compruebo que se pasa por parámetros el id y que este existe en mi db
    if(!isset($_GET['idLibro'])){
        header('Location:index.php');
        die();
    }

    $id_libro=$_GET['idLibro'];
    $ids=Libros::devolverIds();
    if(!in_array($id_libro, $ids)){
        header('Location:index.php');
        die();
    }

    $libro= Libros::read($id_libro);

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
    <title>Detalle Libro</title>
</head>

<body style="background-color:lemonchiffon">
    <h5 class="mt-2 text-center">Detalle</h5>
    <div class="container">
        <div class="card mx-auto" style="width: 20%;">
            <img src="<?php echo ".".$libro->portada?>" class="card-img-top">
            <div class="card-body" style="background-color:#e1dad8">
                <p class="card-text"><b>Id:</b> <?php echo $id_libro?></p>
                <p class="card-text"><b>Titulo:</b> <?php echo $libro->titulo?></p>
                <p class="card-text"><b>Isbn:</b> <?php echo $libro->isbn?></p>
                <p class="card-text"><b>Autor:</b> <?php echo $libro->nombre." ".$libro->apellidos?></p>
                <a href="update.php?idLibro=<?php echo $id_libro?>" class="btn btn-success"><i class="fas fa-edit"></i> Editar </a>&nbsp;
                <a href="index.php" class="btn btn-primary"><i class="fas fa-backward"></i> Volver</a>
            </div>
        </div>
    </div>

</body>

</html>