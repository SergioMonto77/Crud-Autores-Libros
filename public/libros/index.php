<?php

    session_start();
    require __DIR__."/../../vendor/autoload.php";
    use Src\Libros;

    Libros::crearLibros(20);
    $libros= Libros::readAll();
    
?> 
<!DOCTYPE html>
<html lang="es"> 

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- LINK CSS +JS  DATATABLEB5 (no le añado el de bootstrap ya que lo tengo arriba) -->
    <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <!-- FONTAWESOME  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- sweetalert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Gestionar Libros</title>
</head>

<body style="background-color:lemonchiffon">
    <h5 class="text-center mt-4"><b>Libros</b></h5>
    <div class="container">
        <a href="crear.php" class="my-2 btn btn-primary">
            <i class="fas fa-add"></i> Crear Libro
        </a>
        <table class="table table-striped" id="miTabla">
            <thead>
                <tr>
                    <th scope="col">INFO</th>
                    <th scope="col">ID-LIBRO</th>
                    <th scope="col">TITULO</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">AUTOR</th>
                    <th scope="col">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($libros as $libro){
                        echo <<<CODE
                            <tr>
                                <th scope="row"><a href='info.php?idLibro={$libro->id_libro}' class="btn btn-primary" role="button"> i </a></th>
                                <td>{$libro->id_libro}</td>
                                <td>{$libro->titulo}</td>
                                <td>{$libro->isbn}</td>
                                <td>{$libro->nombre} {$libro->apellidos}</td>
                                <td>
                                    <form class="form form-inline" action="borrar.php" method="POST">
                                        <input type="hidden" name='idLibro' value='{$libro->id_libro}' /> <!--cuando se pulse el button type submit se enviará por post (como he indicado en la cabecera) el input type hidden (con el name y value especificados)-->
            
                                        <a href="update.php?idLibro={$libro->id_libro}" class="btn btn-warning"> <!--no puedo usar _ al pasar una variable por GET-->
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        CODE;
                    }
                ?>
            </tbody>
        </table>
    </div>
    <script> //como para el datatable uso código js lo meto dentro de etiquetas 'script'
        $(document).ready(function () {
            $('#miTabla').DataTable();
        });
    </script>
    <?php //el sweet alert es código js
        if(isset($_SESSION['mensaje'])){
            echo <<<CODE
                <script>
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: '{$_SESSION['mensaje']}',
                        showConfirmButton: false,
                        timer: 1500
                    })
                </script>
            CODE;
            unset($_SESSION['mensaje']); //SESSION FLASH
        }
    ?>
</body>

</html>