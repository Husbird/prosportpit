<?php
/** 
Класс соединения с базой данных 
*/
class MsDBConnect
{
    public $dbСharset = 'utf8'; //кодировка (по умолчанию)
    public static $mysqli = null; //метка соединения
    public static $mysqli_2 = null; //метка соединения c БД №2 (если требуется)

    protected static $_instance;  //экземпляр объекта
    
    //используется!
    public static function getInstance()
    { // получить экземпляр данного класса 
        if (self::$_instance === null) { // если экземпляр данного класса  не создан
            self::$_instance = new self;  // создаем экземпляр данного класса 
        } 
        return self::$_instance; // возвращаем экземпляр данного класса
    }

    function __construct(){
        
        $GLOBALS['mss_monitor'][] = '<hr><span style="color: red;">'.__METHOD__.':<br>подключаюсь к базе данных_1 <b>"'.MSS::app()->config->db_name.'"</b>
        <br> хост: "'.MSS::app()->config->db_host.'"...</span>';
        //подключаем БД_1
        $mysqli = new mysqli(
            MSS::app()->config->db_host,
            MSS::app()->config->db_user,
            MSS::app()->config->db_pass,
            MSS::app()->config->db_name
        );
        
        $mysqli->set_charset($this->dbСharset);
        
        if ($mysqli->connect_error){
            die('<p style="color:red;"><b>Ошибка подключения базе данных_1:</b></p> (' . $mysqli->connect_errno . ') '. $mysqli->connect_error); //реализовать запись в лог!
        }
        self::$mysqli = $mysqli; //инициализация объектa, представляющего подключение к серверу MySQL (метка соединения).
        if(self::$mysqli){
            $GLOBALS['mss_monitor'][] = '<span style="color: red;">'.__METHOD__.': подключение к БД_1: <b>'.MSS::app()->config->db_name.'</b> ...ok!</span><hr>';
        }
        /* закрываем соединение */
        //$mysqli->close();
        //var_dump($mysqli);
        //подключение к БД 2
        /**
 * $GLOBALS['mss_monitor'][] = '<hr><span style="color: red;">'.__METHOD__.':<br>подключаюсь к базе данных_2 <b>"'.MSS::app()->config->db_name_2.'"</b>
 *         <br> хост: "'.MSS::app()->config->db_host.'"...</span>';
 *         //подключаем БД_2
 *         $mysqli_2 = new mysqli(
 *             MSS::app()->config->db_host_2,
 *             MSS::app()->config->db_user_2,
 *             MSS::app()->config->db_pass_2,
 *             MSS::app()->config->db_name_2
 *         );
 *         
 *         $mysqli_2->set_charset($this->dbСharset);
 *         
 *         if ($mysqli->connect_error){
 *             die('<p style="color:red;"><b>Ошибка подключения базе данных_2:</b></p> (' . $mysqli->connect_errno . ') '. $mysqli->connect_error); //реализовать запись в лог!
 *         }
 *         self::$mysqli_2 = $mysqli_2; //инициализация объектa, представляющего подключение к серверу MySQL (метка соединения).
 *         if(self::$mysqli_2){
 *             $GLOBALS['mss_monitor'][] = '<span style="color: red;">'.__METHOD__.': подключение к БД_2: <b>'.MSS::app()->config->db_name_2.'</b> ...ok!</span><hr>';
 *         }
 */
    }
    
    //возвращает оъект соединения, доступно в любом месте кода
    // получить метку соединения (return obj)
    public static function getMysqli(){
        return self::$mysqli; // возвращаем объект
    }
    
    //возвращает оъект соединения, доступно в любом месте кода
    // получить метку соединения (return obj)
    public static function getMysqli_2(){
        //var_dump(debug_backtrace());die;
        //die('OKKKKKKKKKKKKKKKKKKK db connect');
        echo __METHOD__.': включил базу 2';
        return self::$mysqli_2; // возвращаем объект
    }
}
?>