<?php

    namespace Src;

    use \PDO;
    use \PDOException;

    class Autores extends Conexion{

        private int $id_autor;
        private string $apellidos;
        private string $nombre;

        public function __construct()
        {
            parent::__construct();
        }

        //___________________________________________METODOS CRUD_______________________________________________
        public function create(){
            $q="insert into autores(apellidos, nombre) values(:a, :n)";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute([ //El id no se lo debo poner al crear ya que al ser auto_increment se incrementa y asigna solo
                    ':a'=>$this->apellidos,
                    ':n'=>$this->nombre
                ]);
            }catch(PDOException $ex){
                die("Error en create()-autores: ".$ex->getMessage());
            }

            parent::$conexion=null;
        }

        public static function read(?int $id=null){ //nombre, apellidos (para un autor en concreto o para todos)
            parent::crearConexion();
            $q=($id!=null) ? "select nombre, apellidos from autores where id_autor=:i" : "select id_autor, nombre, apellidos from autores";
            $stmt=parent::$conexion->prepare($q);
            $param= ($id!=null) ? [':i'=>$id] : []; //si tengo que usar un operador ternario para los parámetros dejo el corchete vacío
    
            try{
                $stmt->execute($param);
            }catch(PDOException $ex){
                die("Error en read()-autores: ".$ex->getMessage());
            }
    
            parent::$conexion=null;
            return ($id!=null) ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetchAll(PDO::FETCH_OBJ); //devuelo una única fila o todas (en función del parámetro)
        }

        public function update($id_autor){
            $q="update autores set nombre=:n, apellidos=:a where id_autor=:i";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute([ //El id no se lo debo poner al update ya que al ser auto_increment se incrementa y asigna solo
                    ':a'=>$this->apellidos,
                    ':n'=>$this->nombre,
                    ':i'=>$id_autor
                ]);
            }catch(PDOException $ex){
                die("Error en update()-autores: ".$ex->getMessage());
            }

            parent::$conexion=null;
        }

        public function delete($id_autor){
            $q="delete from autores where id_autor=:i";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute([ 
                    ':i'=>$id_autor
                ]);
            }catch(PDOException $ex){
                die("Error en delete()-autores: ".$ex->getMessage());
            }

            parent::$conexion=null;
        }

        public static function readAll(){
            parent::crearConexion();
            $q="select * from autores";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute();
            }catch(PDOException $ex){
                die("Error en readAll()-autores: ".$ex->getMessage());
            }

            parent::$conexion=null;
            return $stmt->fetchAll(PDO::FETCH_OBJ); //voy a devolver más de una fila (he indico que accederé con ->)
        }

        //___________________________________________OTROS MÉTODOS_______________________________________________
        public static function crearAutores($cant){

            if(self::hayAutores()) return;

            $faker = \Faker\Factory::create('fr_FR'); //como la clase no se encuentra en src sino en vendor le pongo '\'

            for($i=0; $i<$cant; $i++){
                (new Autores)->setNombre($faker->firstName())
                ->setApellidos($faker->lastName()." ".$faker->lastName()) //le concateno los dos apellidos
                ->create();
            }
        }

        private static function hayAutores():bool{
            parent::crearConexion();
            $q="select id_autor from autores";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute();
            }catch(PDOException $ex){
                die("Error en hayAutores()-autores: ".$ex->getMessage());
            }

            parent::$conexion=null;
            return $stmt->rowCount();
        }

        public static function devolverIds():array{
            parent::crearConexion();
            $q="select id_autor from autores";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute();
            }catch(PDOException $ex){
                die("Error en devolverIds()-autores: ".$ex->getMessage());
            }

            parent::$conexion=null;
            return $stmt->fetchAll(PDO::FETCH_COLUMN); //devuelvo un array con todos los ids de autores existentes en mi db
        }


        //___________________________________________SETTERS_______________________________________________
        public function setApellidos($apellidos)
        {
                $this->apellidos = $apellidos;

                return $this;
        }

        public function setNombre($nombre)
        {
                $this->nombre = $nombre;

                return $this;
        }
    }