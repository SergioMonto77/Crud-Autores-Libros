<?php

    namespace Src;

    use \PDO;
    use \PDOException;

   class Conexion{

        protected static $conexion;

        public function __construct()
        {
            self::crearConexion();
        }

        public static function crearConexion(){

            if(self::$conexion!=null) return;

            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__."/../"); //al no ser una clase que se encuentre en src (si no en vendor) le pongo \ y tras el DIR le indico donde está el .env
            $dotenv->load();

            $user=$_ENV['USER'];
            $pass=$_ENV['PASS'];
            $host=$_ENV['HOST'];
            $db=$_ENV['DATABASE'];

            //1-Creamos el dsn(descriptor de nombres de servicio)
            $dsn= "mysql:host=$host;dbname=$db;charset=utf8mb4";


            //2-Inicializamos la conexion
            try{
                self::$conexion= new PDO($dsn, $user, $pass);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $ex){
                die("Error al inicializar la conexión: ".$ex->getMessage());
            }
        }

   } 