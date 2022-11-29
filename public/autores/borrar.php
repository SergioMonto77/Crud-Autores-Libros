<?php

    session_start();
    require __DIR__."/../../vendor/autoload.php";
    use Src\Autores;
    
    //compruebo que se pase por post el idAutor y que este exista en mi db
    if(!isset($_POST['idAutor'])){
        header('Location:index.php');
        die();
    }


    $id_autor= $_POST['idAutor'];
    $ids= Autores::devolverIds();
    if(!in_array($id_autor, $ids)){
        header('Location:index.php');
        die();
    }

    //si no hay ningún error borro el autor, creo la variable session y me redirijo al index
    (new Autores)->delete($id_autor);
    $_SESSION['mensaje']="Usuario borrado exitosamente";
    header('Location:index.php');
