<?php
class MsLogout{
    
/**
* public $model_name;//имя модели
*     public $action;//вызываемое действие
*     public $params = array();
*/
    
    function __construct(){
        unset($_SESSION['auth']);
        //очистиь даннные пользователя из cookie
        setcookie("id", "",time()-100,"/");
        setcookie("hash", "",time()-100,"/");
        setcookie("ip", "",time()-100,"/");
        header("location:/");
        new MsLogWrite('logout',MSS::app()->config->save_all,MSS::app()->config->log_files_write);//запись в журнал
        exit();
    }
}
?>