<?php

    session_start();
    $error=false;

    require __DIR__."/../../vendor/autoload.php";
    use Src\{Autores, Libros};

    $autores= Autores::read();

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

    function mostrarError($error){
        if(isset($_SESSION[$error])){
            echo "<p class='text-danger' style='font-size:0.75rem'>{$_SESSION[$error]}</p>";
            unset($_SESSION[$error]); //SESSION FLASH
        }
    } 

    if(isset($_POST['enviarForm'])){

         //recojo los campos
         $titulo=trim(ucfirst($_POST['titulo']));
         $isbn=(int)(trim($_POST['isbn']));  //TODO LO QUE RECOJO POR POST ES UN STRING
         $autor=$_POST['autor'];
 
         //valido campos
         if(strlen($titulo)<3){
             $error=true;
             $_SESSION['error_nombre']="*** ERROR: el titulo debe tener al menos 3 carácteres!!";
         }
 
         // unicamente voy a comprobar que el isbn tenga longitud 13 y que no exista previamente
         if(strlen($isbn)!=13){
             $error=true;
             $_SESSION['error_isbn']="*** ERROR: el isbn debe estar compuesto por 13 números (1742754365288)";
         }
 
         if(Libros::existeLibro($isbn, $id_libro)){
             $error=true;
             $_SESSION['error_isbn']="*** ERROR: el isbn introducido ya existe";
         }

        $idsExistentes= Autores::devolverIds(); //debo comprobar que el id del seleccionado existe (POSTMAN)
        $control=false;
        foreach($idsExistentes as $id){
            if($id==$autor) $control=true;
        }

        if(!$control){
            $error=true;
            $_SESSION['error_autor']="*** ERROR: el id del autor no existe";
        }

        if($error){
            header("Location:{$_SERVER['PHP_SELF']}?idLibro=$id_libro");
            die();
        }

        //si hay algún error recargo mi página para mostrarlos. EOC paso a procesar la imagen
        $nombreUnico=$libro->portada;
        $txt= '';
        if($_FILES['logo']['error']==0){

            $tiposMime= ['image/gif', 'image/png', 'image/jpeg', 'image/bmp', 'image/webp'];
            if(!in_array($_FILES['logo']['type'], $tiposMime)){
                $_SESSION['error_logo']="*** ERROR: el archivo debe tener un tipo mime válido (de imagen)!!";
                header("Location:{$_SERVER['PHP_SELF']}?idLibro=$id_libro");
                die();
            }

            //si no 'error'==0 y el tipo mime es válido paso a mover el archivo desde la carpeta temporal hasta el nombreUnico
            $nombreUnico= "/img/".uniqid()."_".$_FILES['logo']['name']; 

            if(!move_uploaded_file($_FILES['logo']['tmp_name'], ".".$nombreUnico)){ //debo indicar la ruta para llegar a 'nombreunico'
                $nombreUnico=$libro->portada;
                $txt='pero no se pudo guardar la imagen';
            }else{
                //si la imagen nueva logra guardarse bien y la antigua no es la almacenada por defecto la borro
                if(basename($libro->portada)!='default.png'){
                    unlink('.'.$libro->portada);
                }
            }

            (new Libros)->setTitulo($titulo)
            ->setIsbn($isbn)
            ->setAutor($autor)
            ->setPortada($nombreUnico)
            ->update($id_libro);
    
            $_SESSION['mensaje']="Libro actualizado exitosamente $txt";
            header('Location:index.php'); //actualizo el libro y me redirijo a la página principal
            die();

        }

        $_SESSION['error_logo']="*** ERROR: el archivo no se ha podido subir. Código de error=>".$_FILES['logo']['error'];
        header("Location:{$_SERVER['PHP_SELF']}?idLibro=$id_libro");

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
    <title>Actualizar Libro</title>
</head>

<body style="background-color:lemonchiffon">

    <h5 class="text-center mt-4">Actualizar Libro</h5> 
        <div class="container">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']."?idLibro=$id_libro" ?>" class="mx-auto bg-secondary px-4 py-4 rounded" style="width:40rem" enctype="multipart/form-data"> <!--NO OLVIDAR EL ENCTYPE-->

                <div class="mb-4">
                    <label for="t" class="form-label">Titulo</label>
                    <input type="text" id="t" class="form-control" name="titulo" value="<?php echo $libro->titulo ?>" required />
                    <?php mostrarError('error_titulo') ?>
                </div>

                <div class="mb-4">
                    <label for="i" class="form-label">Isbn</label>
                    <input type="text" id="i" class="form-control" name="isbn" value="<?php echo $libro->isbn ?>" required />
                    <?php mostrarError('error_isbn') ?>
                </div>

                <div class="mb-4">
                    <label for="i" class="form-label">Autor</label> <!--muestro una lista de autores pero envío su id (que es la fk y pk que me relacinan ambas tablas)-->
                    <select class="form-select" name='autor'>
                        <?php
                            foreach($autores as $autor){
                                $seleccionado= ($libro->autor==$autor->id_autor) ? 'selected' : '';
                                echo "<option $seleccionado value='{$autor->id_autor}'>{$autor->nombre}</option>";
                            }
                        ?>
                    </select>
                    <?php mostrarError('error_autor') ?>
                </div>


                <!--portada-->
                <div class="mb-4">
                        <div class="input-group">
                            <input type="file" class="form-control" id="file" name="logo"  accept="image/*" />
                        </div>
                        <?php mostrarError('error_logo') ?>
                </div>

                <div class="mb-4">
                    <div class="my-4 text-center">
                            <img class="img-thumbnail" src=".<?php echo $libro->portada?>" id="image" style="width:8rem; height:8rem" />
                    </div> <!--a la hora de crear un nuevo libro quiero que salga la imagen por defecto y que luego se modifique-->
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
        <script>
            document.getElementById("file").addEventListener('change', cambiarImagen);

            function cambiarImagen(event) {
                var file = event.target.files[0];
                var reader = new FileReader();
                reader.onload = (event) => {
                    document.getElementById("image").setAttribute('src', event.target.result)
                };
                reader.readAsDataURL(file);
            }
        </script>

</body>

</html>
<?php } ?>