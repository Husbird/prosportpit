<?php
/**
 * отправка электронной почты
 */
class MsSendMail
{
    public $mysqli;
 /**
 *    function __construct(){
 *         
 *     }
 */
    //отправка письма на заданные emails
    //$emails - массив с эл.адресами; например: $array = array("client" => $params['email'], "admin" => "ms-projects@mail.ru");
    //$massageText - текст письма, 
    //$from - от кого письмо, 
    //$subject - тема письма
    public function sendMail($emails, $from, $subject, $massageText){
        
        ($from == false) ? $from = MSS::app()->config->site_path : $from = $from; //если не указано поле "от кого" - пишем адрес сайта
        
        //проверка входящих значений
        if(!is_array($emails)){
            die(''.__METHOD__.': Ошибка! Не уазано ни одного E-mail ');
        }
        
        if($from == ''){
            die(''.__METHOD__.': Ошибка! Попытка отправить письмо без указания отправителя');
        }
        
        if($massageText == ''){
            die(''.__METHOD__.': Ошибка! Попытка отправить пустое письмо ');
        }
        
        foreach($emails as $key => $value){
            $to = $to.''.$key.' <"'.$value.'">,';
        }
        
        $to = substr($to, 0, -1);//удаляем лишнюю запятую вконце
        
        /* Для отправки HTML-почты вы можете установить шапку Content-type. */
		$headers  = "MIME-Version: 1.0 \r\n";
		//устанавливаем кодировку
        $headers .= "Content-type: text/html; charset = utf-8 \r\n";
		
		/* дополнительные шапки */
		$headers .= "From: MSFrame \r\n";
        
        //отправляем письмо
        mail($to, $subject, $massageText, $headers);
        if(mail){
            return true;
        }else{
            new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                'Не удалось отправить письмо пользователю! ... die ',__METHOD__);
            //die('<br><b>Error:</b> Не удалось отправить письмо с инструкцией пользователю!');
            return false;
        }
        //var_dump($to);
    }

    
    //обработка строковых данных получяемых из форм ввода $mysqli link
    public function checkStr($string){
        $this->mysqli = MsDBConnect::getInstance()->getMysqli();
        $str = mysqli_real_escape_string($this->mysqli,strip_tags(trim($string)));
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: проверяю строку - '.$str;
        return ($str);
    }
    
    //проверка E-mail
    //в случае успеха возвращает true, в противном случае возвращает массив с найденными ошибками
    public function emailCheck($email){
        //создаём массив ошибок
    	$error = array(); 
		if(isset($email)){
    		$email = strip_tags(trim($email));
    		if($email != ""){
    			$regV = '/^[a-zA-Z0-9\-\_\.]{1,25}\@[a-zA-Z0-9\-\_]{2,15}\.[a-zA-Z0-9]{2,4}$/';
    			$rez = preg_match($regV, $email);
    			if (!$rez){
    				$error[] = "некорректный E-mail";
    				if (strlen($email) > 46) $error[] = "Больше 46 символов";
    			}
   			}else
    			$error[] = "E-mail не передан!";
		}

		if (count($error) == 0){
            return true;
		}else
			return $error;
    }
    
}
?>