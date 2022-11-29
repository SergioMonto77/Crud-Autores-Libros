<?php

    namespace Src;

    use \PDO;
    use \PDOException;

    class Libros extends Conexion{

        private int $id_libro;
        private string $titulo;
        private string $isbn;
        private int $autor;
        private ? string $portada;

        public function __construct()
        {
            parent::__construct();
        }

        //___________________________________________METODOS CRUD_______________________________________________
        public function create(){
            $q="insert into libros(titulo, isbn, autor, portada) values(:t, :i, :a, :p)";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute([ //El id no se lo debo poner al crear ya que al ser auto_increment se incrementa y asigna solo
                    ':t'=>$this->titulo,
                    ':i'=>$this->isbn,
                    ':a'=>$this->autor,
                    ':p'=>$this->portada
                ]);
            }catch(PDOException $ex){
                die("Error en create()-libros".$ex->getMessage());
            }

            parent::$conexion=null;
        }

        public static function read($id_libro){
            parent::crearConexion();
            $q="select libros.*, nombre, apellidos from libros, autores where id_autor=autor and id_libro=:i";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute([':i'=>$id_libro]);
            }catch(PDOException $ex){
                die("Error en read()-libros".$ex->getMessage());
            }

            parent::$conexion=null;
            return $stmt->fetch(PDO::FETCH_OBJ);  //devuelvo una única fila
        }

        public function update($id_libro){
            $q="update libros set titulo=:t, isbn=:i, autor=:a, portada=:p where id_libro=:il";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute([ //El id no se lo debo poner al update ya que al ser auto_increment se incrementa y asigna solo
                    ':t'=>$this->titulo,
                    ':i'=>$this->isbn,
                    ':a'=>$this->autor,
                    ':p'=>$this->portada,
                    ':il'=>$id_libro
                ]);
            }catch(PDOException $ex){
                die("Error en update()-libros: ".$ex->getMessage());
            }

            parent::$conexion=null;
        }

        public function delete($id_libro){
            $q="delete from libros where id_libro=:i";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute([ 
                    ':i'=>$id_libro
                ]);
            }catch(PDOException $ex){
                die("Error en delete()-libros: ".$ex->getMessage());
            }

            parent::$conexion=null;
        }

        public static function readAll(){
            parent::crearConexion();
            $q="select libros.*, nombre, apellidos from libros, autores where id_autor=autor";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute();
            }catch(PDOException $ex){
                die("Error en readAll()-libros".$ex->getMessage());
            }

            parent::$conexion=null;
            return $stmt->fetchAll(PDO::FETCH_OBJ);  //devuelvo más de una fila
        }

        //___________________________________________OTROS MÉTODOS_______________________________________________
        public static function crearLibros($cant){

            if(self::hayLibros()) return;

            $faker = \Faker\Factory::create('fr_FR'); //como la clase no se encuentra en src sino en vendor le pongo '\'
            $ids= Autores::devolverIds(); //no hace falta hacer require aquí, sino donde se ejecute

            for($i=0; $i<$cant; $i++){
                (new Libros)->setTitulo($faker->sentence($faker->numberBetween(1,3))) //el titulo será una frase de 1 a 3 palabras
                ->setIsbn($faker->unique()->isbn13())
                ->setAutor($faker->randomElement($ids))
                ->setPortada('/img/default.png') //le asignaré a todos los libros una misma imagen por defecto
                ->create();
            }
        }

        private static function hayLibros():bool{
            parent::crearConexion();
            $q="select id_libro from libros";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute();
            }catch(PDOException $ex){
                die("Error en hayLibros()-libros: ".$ex->getMessage());
            }

            parent::$conexion=null;
            return $stmt->rowCount();
        }

        public static function devolverIds():array{
            parent::crearConexion();
            $q="select id_libro from libros";
            $stmt=parent::$conexion->prepare($q);

            try{
                $stmt->execute();
            }catch(PDOException $ex){
                die("Error en devolverIds()-libros: ".$ex->getMessage());
            }

            parent::$conexion=null;
            return $stmt->fetchAll(PDO::FETCH_COLUMN); //devuelvo un array con todos los ids de libros existentes en mi db
        }

        public static function existeLibro($isbn, ?int $id_libro=null):bool{

            parent::crearConexion();
            $q= ($id_libro==null) ? "select id_libro from libros where isbn=:i" : "select id_libro from libros where isbn=:i and id_libro!=:il";
            $stmt=parent::$conexion->prepare($q);
            $param= ($id_libro==null) ? [':i'=>$isbn] : [':i'=>$isbn, ':il'=>$id_libro];

            try{
                $stmt->execute($param);
            }catch(PDOException $ex){
                die("Error en existeLibro()-libros: ".$ex->getMessage());
            }

            parent::$conexion=null;
            return $stmt->rowCount();
        }


        //___________________________________________SETTERS_______________________________________________
        public function setTitulo($titulo)
        {
                $this->titulo = $titulo;

                return $this;
        }

        public function setIsbn($isbn)
        {
                $this->isbn = $isbn;

                return $this;
        }

        public function setAutor($autor)
        {
                $this->autor = $autor;

                return $this;
        }

        public function setPortada($portada)
        {
                $this->portada = $portada;

                return $this;
        }
    }