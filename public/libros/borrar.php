<?php

    session_start();
    require __DIR__."/../../vendor/autoload.php";
    use Src\Libros;
    
    //compruebo que se pase por post el idAutor y que este exista en mi db
    if(!isset($_POST['idLibro'])){
        header('Location:index.php');
        die();
    }


    $id_libro= $_POST['idLibro'];
    $ids= Libros::devolverIds();
    if(!in_array($id_libro, $ids)){
        header('Location:index.php');
        die();
    }

    //aparte de borrar el libro debo borrar la foto si esta no era la almacenada por defecto
    $libro= Libros::read($id_libro);
    if(basename($libro->portada)!='default.png'){
        unlink('.'.$libro->portada);
    }
    //si no hay ningún error borro el libro, creo la variable session y me redirijo al index
    (new Libros)->delete($id_libro);
    
    $_SESSION['mensaje']="Libro borrado exitosamente";
    header('Location:index.php');
