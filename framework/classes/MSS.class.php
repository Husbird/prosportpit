<?php
class MSS {

	private static
        $instance = null;
 
    public static function app()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;  // возвращаем экземпляр объекта MSS
    }

    public $config; // свойство класса, который будет хранить настройки нашего приложения
    public $frontController;//объект фронт контроллера
    public static $user_role = 'Guest'; //Guest - псевдоним категории прав пользователей "-1"
    public static $root = -1;// категория прав пользователя
    public static $userData = false; //все данные инициализированного пользователя ПРИМЕР исп.: MSS::$userData['name'];
    public static $modelData = null;// данные текущей модели
    public static $userDevice = null;// данные об устройстве пользователя
    public static $userOS = null; //ОС пользователя
    public static $userIp = null; //ip текушего пользователя
    
    public function controllerSet($request){
        
        if(is_array($request)){
            $frontController = new MsController;
            $this->frontController = $frontController;
            $GLOBALS['mss_monitor'][] = 'MSS: Контроллер создан успешно';
        }else{
            echo '<hr>Ошибка инициализации контроллера! Передан параметр:'.var_dump($request).'<hr>';
        }
    }

    private function __clone() {}  // запрещаем использование магических методов, для безопасности и не только
    
    private function __construct() {
        //echo '<br>MSS __construct(): Загружаю фаил конфигурации ...<br>';
    $GLOBALS['mss_monitor'][] = '<br>'.__METHOD__.': Загружаю фаил конфигурации ...<br>';
    $this->config = (object)require_once("app/config/main.php");  //присваиваем свойству $config массив настроек из нашего файла настроек.
    //var_dump($this->config);
    }
    //устанавливаем права:
    public static function setRole($root){
        switch($root){
            case 0:
            self::$user_role = 'User';//User - псевдоним категории прав пользователей "0"
            break;
            
            case 1:
            self::$user_role = 'Moderator';//Moderator - псевдоним категории прав пользователей "1"
            break;
            
            case 2:
            self::$user_role = 'SuperUser';//SuperUser - псевдоним прав категории пользователей "2"
            break;
            
            case 3:
            self::$user_role = 'Суперчеловек ;)';//Moderator - псевдоним прав категории пользователей "3"
            break;
            
            case 4:
            self::$user_role = 'Admin';//Admin - псевдоним категории прав пользователей "4"
            break;
            
            default:
            self::$user_role = 'Guest';//Guest - псевдоним категории прав пользователей "-1"
            break;
        }
    }
    
    public static function setUserData($data = false){
        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: пишу данные пользователя в приложение ...';
        if(is_array($data)){
            self::$userData = $data;
        }
        if(self::$userData){
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: запись данных пользователя ... ok!';
        }
    }
    
    //проверка на соответствие правам. Передаётся параметр, в котором указаны категории прав допуск для которых открыть
    //далее метод сравнивает указанные категории с текущей категорией и запускает соответствующие сценарии
    public static function accessCheck($accessRole){
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: проверяю права доступа пользователя ...';
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: доступ разрешен пользователям с правами: '.$accessRole;
        $array = explode(',',$accessRole);
        $i=0;
        foreach($array as $value){
             //var_dump($value);die;
            if($value == self::$user_role){
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: совпадение с текущими правами: <b>'.self::$user_role.'</b>';
                $i++;
            }
        }
        if($i > 0){
             $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>:... доступ <b><span style="color:green;">РАЗРЕШЕН!</span></b>';
            return true;
        }else{
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>:совпадений с текущими правами не найдено...</span></b>';
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>:... доступ <b><span style="color:red;">ЗАПРЕЩЁН!</span></b>';
            return false;
            //header('location:/AccessDenied');
            //exit();
        }
    }
    
    //далее метод сравнивает указанные категории с текущей категорией и запускает соответствующие сценарии
    public static function delButtonRun($id = false, $table_name = false, $file_path = false, $dir_path = false, $back_url = false){
        
        if(!$id){
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: не задан обязательный параметр для кнопки id!!!';
            return '<b>'.__METHOD__.'</b>: не задан обязательный параметр для кнопки id!!!';
        }
        if(!$table_name){
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: не задан обязательный параметр для кнопки table_name!!!';
            return '<b>'.__METHOD__.'</b>: не задан обязательный параметр для кнопки table_name!!!';
        }
        //если массив не пуст - кодируем его (для отправки в форме)
        //если пуст - присваиваем нуль
        if(!empty($file_path)){
            $file_path = serialize($file_path);
            $file_path = base64_encode($file_path);
        }else{
            $file_path = 0;
        }
        //если массив не пуст - кодируем его (для отправки в форме)
        //если пуст - присваиваем нуль
        if(!empty($dir_path)){
            $dir_path = serialize($dir_path);
            $dir_path = base64_encode($dir_path);
        }else{
            $dir_path = 0;
        }
        //если указан $back_url - оставляем его, если нет - присваиваем $_SERVER['HTTP_REFERER']
        if(!$back_url){
            $back_url = $_SERVER['HTTP_REFERER'];
        }
        return '
        <form method="post" action="/" role="form">
            <input name="id" type="hidden" value="'.$id.'" />
            <input name="table_name" type="hidden" value="'.$table_name.'" />
            <input name="file_path" type="hidden" value="'.$file_path.'" />
            <input name="dir_path" type="hidden" value="'.$dir_path.'" />
            <input name="back_url" type="hidden" value="'.$back_url.'" />
            <button name="del" type="submit" class="btn btn-danger btn-sm">Удалить</button>
        </form>';
    }
    
    public static function getRealIp(){
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получаю реальный текущий ip ...';
        $ip = $_SERVER['REMOTE_ADDR'];
           $ipFrom = '$_SERVER[REMOTE_ADDR]';
           //$ipServer = $_SERVER['SERVER_ADDR'];
		 //}
         $ip2 = substr($_SERVER['HTTP_FORWARDED'],4);//вырезаем "for="
         $ip2From2 = '$_SERVER[HTTP_FORWARDED]';
         
         $device = $_SERVER['HTTP_USER_AGENT'];//вырезаем "for="
         $deviceIdRequest = '$_SERVER[HTTP_USER_AGENT]';
         self::$userDevice = $device;
         
         //проверка для компенсации бага :
         //при авторизации и моб телефона в классе MsAuthoriz определяется правильный текущий ip средством $_SERVER[REMOTE_ADDR]
         //при этом HTTP_FORWARDED - пуст.
         //после установки кук и редиректе на главную страницу в классе MsCheckRole текущий ip определённый средством $_SERVER[REMOTE_ADDR]
         //уже не совпадает с предидущим результатом определения тем же средством (в MsAuthoriz) (записанным в куке), но 
         //при этом HTTP_FORWARDED уже не пуст и в нём появляется "правильное" значение ip которое до редиректа было определено в
         // классе MsAuthoriz средством $_SERVER[REMOTE_ADDR], поэтому - костыль: Если определили что Android - делаем подмену ip для
         //корректной работы MsCheckRole:
         //если используют гаджет с Аndroid и определён $_SERVER['HTTP_FORWARDED']
         if(stristr($device, 'Android')){
            if($_SERVER['HTTP_FORWARDED']){
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: пришли с Android HTTP_FORWARDED установлен
                , меняю ip <b>'.$ip.'</b> из '.$ipFrom.' на ip <b>'.$ip2.'</b> из '.$ipFrom2.'</span>';
                $ip = $ip2;
                self::$userOS = 'Android';//присваиваем данные об операционной системе пользователя свойству  $userOS  
            }//elseif(stristr($device, 'Windows NT 10')){self::$userOS = 'Windows 10';}
            
         }
         
         $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получен IP: <b>'.$ip.'</b> из '.$ipFrom.'</span>';
         $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получен IP2: <b>'.$ip2.'</b> из '.$ip2From.'</span>';
         $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: данные об устройстве пользователя : <b>'.$device.'</b> из '.$deviceIdRequest.'</span>';
         //$_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: (IP Сервера: <b>'.$ipServer.'</b> из $_SERVER[SERVER_ADDR]</span>';
         self::get_all_ip();//получаем все возможные ip
         self::$userIp = $ip;
         return $ip;
        
    }
    
    //получаем все возможные ip
     private static function get_all_ip() {
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получаю все возможные ip из заголовков HTTP...</span>';
        $ip_pattern="#(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)#";
        $ret="";
        foreach ($_SERVER as $k => $v) {
            if (substr($k,0,5)=="HTTP_" AND preg_match($ip_pattern,$v)) $ret.=$k.": ".$v."\n";
        }
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: найдены ip: '.$ret.'</span>';
        return $ret;
     }
     
     public static function get_OS(){
        /**
 * if(stristr($_SERVER["HTTP_USER_AGENT"], 'Android')){
 *             self::$userOS = 'Android';//присваиваем данные об операционной системе пользователя свойству  $userOS  
 *         }
 *         elseif(stristr($device, 'Windows NT 10')){self::$userOS = 'Windows 10';}
 *         elseif(stristr($device, 'Windows 7')){self::$userOS = 'Windows 7';}
 *         elseif(stristr($device, 'Windows XP')){self::$userOS = 'Windows XP';}
 *         elseif(stristr($device, 'Windows 7')){self::$userOS = 'Windows 7';}
 *         elseif(stristr($device, 'Windows 7')){self::$userOS = 'Windows 7';}
 * 
 */    
        $userAgent = $_SERVER["HTTP_USER_AGENT"];
        $oses = array (
                    // Mircrosoft Windows Operating Systems
                    'Windows 3.11' => '(Win16)',
                    'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
                    'Windows 98' => '(Windows 98)|(Win98)',
                    'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
                    'Windows 2000 Service Pack 1' => '(Windows NT 5.01)',
                    'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
                    'Windows Server 2003' => '(Windows NT 5.2)',
                    'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
                    'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
                    'Windows 8' => '(Windows NT 6.2)|(Windows 8)',
                    'Windows 10 x64' => '(Windows NT 10.0; WOW64)',
                    'Windows 10' => '(Windows NT 10)',
                    'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
                    'Windows ME' => '(Windows ME)|(Windows 98; Win 9x 4.90 )',
                    'Windows CE' => '(Windows CE)',
                    'Windows Phone 8.1' => '(Windows Phone 8.1)',
                    'Windows v.xxx' => '(Windows)',
                    
                    
                    // Mobile Devices
                    'Android 4.4.2' => '(Android 4.4.2)',
                    'Android' => '(Android)',
                    'iPod' => '(iPod)',
                    'iPhone' => '(iPhone)',
                    'iPad' => '(iPad)',
                    
                    // UNIX Like Operating Systems
                    'Mac OS X Kodiak (beta)' => '(Mac OS X beta)',
                    'Mac OS X Cheetah' => '(Mac OS X 10.0)',
                    'Mac OS X Puma' => '(Mac OS X 10.1)',
                    'Mac OS X Jaguar' => '(Mac OS X 10.2)',
                    'Mac OS X Panther' => '(Mac OS X 10.3)',
                    'Mac OS X Tiger' => '(Mac OS X 10.4)',
                    'Mac OS X Leopard' => '(Mac OS X 10.5)',
                    'Mac OS X Snow Leopard' => '(Mac OS X 10.6)',
                    'Mac OS X Lion' => '(Mac OS X 10.7)',
                    'Mac OS X' => '(Mac OS X)',
                    'Mac OS' => '(Mac_PowerPC)|(PowerPC)|(Macintosh)',
                    'Open BSD' => '(OpenBSD)',
                    'SunOS' => '(SunOS)',
                    'Solaris 11' => '(Solaris/11)|(Solaris11)',
                    'Solaris 10' => '((Solaris/10)|(Solaris10))',
                    'Solaris 9' => '((Solaris/9)|(Solaris9))',
                    'CentOS' => '(CentOS)',
                    'QNX' => '(QNX)',
                    
                    // Kernels
                    'UNIX' => '(UNIX)',
                    
                    // Linux Operating Systems
                    'Ubuntu 12.10' => '(Ubuntu/12.10)|(Ubuntu 12.10)',
                    'Ubuntu 12.04 LTS' => '(Ubuntu/12.04)|(Ubuntu 12.04)',
                    'Ubuntu 11.10' => '(Ubuntu/11.10)|(Ubuntu 11.10)',
                    'Ubuntu 11.04' => '(Ubuntu/11.04)|(Ubuntu 11.04)',
                    'Ubuntu 10.10' => '(Ubuntu/10.10)|(Ubuntu 10.10)',
                    'Ubuntu 10.04 LTS' => '(Ubuntu/10.04)|(Ubuntu 10.04)',
                    'Ubuntu 9.10' => '(Ubuntu/9.10)|(Ubuntu 9.10)',
                    'Ubuntu 9.04' => '(Ubuntu/9.04)|(Ubuntu 9.04)',
                    'Ubuntu 8.10' => '(Ubuntu/8.10)|(Ubuntu 8.10)',
                    'Ubuntu 8.04 LTS' => '(Ubuntu/8.04)|(Ubuntu 8.04)',
                    'Ubuntu 6.06 LTS' => '(Ubuntu/6.06)|(Ubuntu 6.06)',
                    'Red Hat Linux' => '(Red Hat)',
                    'Red Hat Enterprise Linux' => '(Red Hat Enterprise)',
                    'Fedora 17' => '(Fedora/17)|(Fedora 17)',
                    'Fedora 16' => '(Fedora/16)|(Fedora 16)',
                    'Fedora 15' => '(Fedora/15)|(Fedora 15)',
                    'Fedora 14' => '(Fedora/14)|(Fedora 14)',
                    'Chromium OS' => '(ChromiumOS)',
                    'Google Chrome OS' => '(ChromeOS)',
                    // Kernel
                    'Linux' => '(Linux)|(X11)',
                    // BSD Operating Systems
                    'OpenBSD' => '(OpenBSD)',
                    'FreeBSD' => '(FreeBSD)',
                    'NetBSD' => '(NetBSD)',
                    
                    //DEC Operating Systems
                    'OS/8' => '(OS/8)|(OS8)',
                    'Older DEC OS' => '(DEC)|(RSTS)|(RSTS/E)',
                    'WPS-8' => '(WPS-8)|(WPS8)',
                    // BeOS Like Operating Systems
                    'BeOS' => '(BeOS)|(BeOS r5)',
                    'BeIA' => '(BeIA)',
                    // OS/2 Operating Systems
                    'OS/2 2.0' => '(OS/220)|(OS/2 2.0)',
                    'OS/2' => '(OS/2)|(OS2)',
                    // Search engines
                    'Search engine or robot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(msnbot)|(Ask Jeeves/Teoma)|(ia_archiver)'
                        );
                     
                        foreach($oses as $os=>$pattern){
                            if(preg_match("/$pattern/i", $userAgent)) {
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: определена OS: '.$os.'</span>';
                                self::$userOS = $os;
                                //new MsLogWrite('',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                  //  '<b>'.$os.'</b>',__METHOD__);
                                return $os;
                            }
                        }
 
                        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: Операционная система НЕ определена!</span>';
                        new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                    'Операционная система НЕ определена! ('.$userAgent.') АДМИН расширь базу устройств!',__METHOD__);
                        self::$userOS = 'Unknown';
                        return 'Unknown'; 
     }
     
     //собираем имеющиеся данные о пользователе и пишем в журнал
     public static function getUserData(){
        if(!MSS::app()->config->save_all){
            return ''.__METHOD__.': Режим ведения общего журнала - отключён!';
        }
        if(!self::$userOS){
            self::get_OS();
            $os = self::$userOS;    
        }else{
            $os = self::$userOS;
        }
        
        if(!self::$userIp){
            $ip = self::getRealIp();
        }else{
            $ip = self::$userIp;
        }
        
        if(self::$userData){
            //var_dump(self::$userData); die;
            $userData = '
            <hr>
            <b>'.date("j-n-Y | H:i:s",time()).'</b><br>
            id: '.self::$userData['id'].' (cookie:'.abs((int)$_COOKIE['id']).')<br>
            Имя: '.self::$userData['name'].'<br>
            Role: '.self::$user_role.'<br>
            ОС: '.$os.'<br>
            ip: '.$ip.'<br>
            System: '.self::$userDevice.'<br>
            Посетил: '.$_SERVER['REQUEST_URI'].'<br>
            Запись проведена из: '.__METHOD__.'<br>'
            
            ;
        }else{
            $userData = 'пользователь не иницализирован (возможно Guest)';
        }
        
        $file_log = 'framework/log/all_log.mss';
        $log_title = $userData. "\n";
        $file_put = file_put_contents($file_log,$log_title,FILE_APPEND);
        return $file_put;
        
     }
}
?>