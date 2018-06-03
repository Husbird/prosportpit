<?php
//ООП +++
class MsCheckRole /**extends MsDBProcess*/ {
    
    /**
 * public $model_name;//имя модели
 *     public $action;//вызываемое действие
 *     public $params = array();
 */
    private $mysqli = null;
    public $checkRole = false;
    
    function __construct($cookie = false){
        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: включён!';
        $this->mysqli = MsDBConnect::getInstance()->getMysqli();
        //проверка авторизованного пользователя (если есть куки)
        if(isset($_COOKIE['hash']) AND isset($_COOKIE['id']) AND isset($_COOKIE['ip'])) {
            //die('tut!!!');
        	$id = abs((int)$_COOKIE['id']);
            $ip = self::checkStr($_COOKIE['ip']); //получаем ip из куки
            //$ipTrue = self::GetRealIp(); //получаем текущий ip
            //$ipTrue = $_SESSION['ip_current']; //получаем текущий ip
            $ipTrue = MSS::app()->getRealIp();
            $hesh = self::checkStr($_COOKIE['hash']); //получаем хеш из куки
            
            //проверяем есть ли пользователь с таким id
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: проверяем пользователя...';
        	$sql = "SELECT hash, adm_mss FROM user WHERE id = '$id'";
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: Sql запрос: '.$sql.'';
            $query = $this->mysqli->query($sql); // ООП

        	//$query = mysqli_query($this->mysqli,$sql);
            //если нет - выводим ошибку
            if(!$query){
                new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                'Ошибка идентификации пользователя с id <b>'.$id.'</b> die',__METHOD__);
                die('Ошибка идентификации пользователя');
            }else{
                $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: <span style="color:green;">успешно!</span>';
            }
            //если есть - получаем массив с его данными
        	$this->userData = mysqli_fetch_assoc($query); //основной массив с информацией о пользователе (доступен везде)userDataArray
            //mysqli_close ($link); //закрываем соединение
        	
        	//если хеш из БД совпадает с хешем из куки 
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: сверяю хеш... '.$this->userData['hash'].'(бд) и '.$_COOKIE['hash'].'($_COOKIE[hash])';
        	if($this->userData['hash'] === $_COOKIE['hash']){
        	   $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ...<span style="color:green;">хеш - ok</span>';
                //если хеш совпал - сверяем ip текущего устройства с ip из куки
                $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: сверяю ip...';
                $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: текущий ('.$ipTrue.')и из cookie ('.$ip.') ...';
                if($ipTrue === $ip){
                    $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>:<span style="color:green;"> ...ip - ok</span>';
                    //$userAuth = true;//статус авторизации
                    $root = $this->userData['adm_mss'];//привелегии пользователя
                    MSS::setRole($root);//присваиваем пользователю права! (в объект приложения)
                    //$GLOBALS['mss_monitor'][] = 'MsCheckRole: пользователь прошел проверку!';
                    //проверяю метку авторизации в сессии на случай если пользователь закрывал броузер
                    //данную метку использует фронт контроллер (MsController) в своей логике
                    $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: проверяю метку авторизации в сессии...!';
                    if(!$_SESSION['auth']){
                        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: метка <b>отсутствует</b>!';
                        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ставлю новую метку...';
                        $_SESSION['auth'] = true; //ставим метку успешной авторизации в сессию    
                    }else{
                        $GLOBALS['mss_monitor'][] = '<b>MsCheckRole</b>: метка есть ... ok!';
                    }
                    
                    $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: <span style="color:green;"><b>пользователь ПРОВЕРЕН!</b></span>';
                    //пишем в свойство об успешной проверке прав пользователя (влияет на последующую выборку
                    //данных пользователя в контроллере)
                    $this->checkRole = true;
                    //var_dump($root); die('это root!!!!!');
                }else{
                    unset($id);
                    unset($this->userData);
                    self::clearData();
                    new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                    'ip - НЕ СОВПАЛ !!!',__METHOD__);
                    $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ...<span style="color:red;">ip - НЕ СОВПАЛ!!!</span>'; //тут нужно отправлять письмо с ошибкой админу!!!! СДЕЛАТЬ 
                }
        	}else{//если хеш не совпадает - logout
        		unset($id);
                unset($this->userData);
                self::clearData();
                $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ...<span style="color:red;">хеш - НЕ СОВПАЛ!!!</span>'; //тут нужно отправлять письмо с ошибкой админу!!!! СДЕЛАТЬ
        	}
    	
        }else{
            //чистим метку авторизации в сессии если она есть
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: <span style="color:green;"><b>пользователь зашел как Гость!</b></span>';
            if($_SESSION['auth']){
                $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: чистим метку авторизации в сессии...</span>';
                unset($_SESSION['auth']);
            }
        }
    }
    
    //обработка строковых данных получяемых из форм ввода                               link
    private function checkStr($string){
        //var_dump($this->mysqli);die;
        $str = mysqli_real_escape_string($this->mysqli,strip_tags(trim($string)));
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: checkStr(): проверяю строку - '.$str;
        return ($str);
    }
    
    //очистиь даннные пользователя и куки
    private function clearData(){
        //unset($id);
        //unset($userDataArray);
        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: clearData(): чистим метку авторизации в сессии и cookie...</span>';
        unset($_SESSION['auth']);
        setcookie("id", "",time()-100,"/");
        setcookie("hash", "",time()-100,"/");
        setcookie("ip", "",time()-100,"/");
    }
   
   //получаем ip пользователя
/**
 * 	private function GetRealIp(){
 *         $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получаю текущий ip пользователя...</span>';

 * 		   $ip=$_SERVER['REMOTE_ADDR'];
 *            $ipFrom = '$_SERVER[REMOTE_ADDR]';
 *            $ipServer = $_SERVER['SERVER_ADDR'];
 * 		 
 *          $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получен IP: <b>'.$ip.'</b> из '.$ipFrom.'</span>';
 *          $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: (IP Сервера: <b>'.$ipServer.'</b> из $_SERVER[SERVER_ADDR]</span>';
 *          self::get_all_ip();
 * 		 return $ip;
 * 	}
 */
    
        //получаем все возможные ip
    /**
 *  private function get_all_ip() {
 *         $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получаю все возможные ip из заголовков HTTP...</span>';
 *         $ip_pattern="#(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)#";
 *         $ret="";
 *         foreach ($_SERVER as $k => $v) {
 *             if (substr($k,0,5)=="HTTP_" AND preg_match($ip_pattern,$v)) $ret.=$k.": ".$v."\n";
 *         }
 *         $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: найдены ip: '.$ret.'</span>';
 *         return $ret;
 *      }
 */
}
?>