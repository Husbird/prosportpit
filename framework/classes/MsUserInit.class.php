<?php
//извлечение данных идентифицированного пользователя
class MsUserInit extends MsDBProcess
{
    public $mysqli;
	//protected $_db; - наследуется
	//public $login;
    public $userData = false; //id авторизованного пользователя
    //private $mysqli = null;//объект, представляющий подключение к серверу MySQL
    
	function __construct($id = false, $userTableName = false){
        $this->mysqli = MsDBConnect::getInstance()->getMysqli();
        //var_dump($this->mysqli);die;
        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: запускаю инициализацию пользователя...';
        //проверка id
        if($id){
            $id = $this->clearInt($id);    
        }else{
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: <span style="color:red;">ошибка</span>: id пользователя не установлен!';
        }
        //проверяем имя таблицы
        if($userTableName){
            $userTableName = $this->checkStr($userTableName);    
        }else{
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: <span style="color:red;">ошибка</span>: имя таблицы пользователей не установлено!';
        }
        
        MSS::app()->get_OS();//получаем и пишем в свойство приложения OS пользователя
        
        //извлекаем данные пользователя
        $this->userData = $this->selectDataOnID($id,$userTableName);
        //var_dump($this->userData);die;
        //проверяем полученный массив
        if(is_array($this->userData) AND (count($this->userData) >= 1)){
             $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: инициализация пользователя ... <span style="color:green;">ok</span>!';
        }else{
             $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: <span style="color:red;">ошибка</span> инициализации пользователя!';
        }
	}
    
    //обработка числа перед записью в БД
	public function clearInt($data){
		return abs((int)$data);
	}
	
    //обработка строковых данных получяемых из форм ввода $mysqli  link
    private function checkStr($string){
        $str = mysqli_real_escape_string($this->mysqli,strip_tags(trim($string)));
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: проверяю строку - '.$str;
        return ($str);
    }
}
?>