<?php
// ООП +++
//Восстановление пароля
class MsPassRestore extends MsAuthoriz
{
    public $sitePath = false; //текущий адрес сайта
    public $id = false; //id с которым ассоциирован полученный из формы email
    public $name = false; //id с которым ассоциирован полученный из формы email
    public $email = false;

    private $mysqli = null;
    
	function __construct(){
	   
	   $_SESSION['mss_monitor'][] = '<b>Запускаю MsPassRestore !</b><br>';
	   //die("Let's go!");
       $this->mysqli = MsDBConnect::getInstance()->getMysqli(); //получаем метку соединения
       $this->sitePath = MSS::app()->config->site_path;
	}
    
    //отсылаем письмо с инструкциями в случае нахождения введённого email в БД
    public function checkAndSend($params=false) {
        $_SESSION['mss_monitor'][] = '<b>работает checkAndSend !</b><br>';
        $email = strtolower(trim($params['email']));
       //var_dump($this->sitePath);die;
       
       $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: ищу в БД email идентичный переданному  ...';
       //проверяем есть ли в БД такой Email:
       $sql = "SELECT id, name FROM user WHERE email = ?";
        //анализ запроса и подготовка к исполнению
        if ($stmt = $this->mysqli->prepare($sql)){
             /* связываем параметры с метками */
            $stmt->bind_param("s", $email);
            /* запускаем запрос */
            $query = $stmt->execute();
            if($query){
                $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: SQL запрос SELECT прошел успешно...';
                
                /* связываем переменные с результатами запроса */
                $stmt->bind_result($id, $name);
                /* получаем значения */
                $x = $stmt->fetch(); //возвращает true или false
                if($x){
                    //если введённый в форме email найден в БД
                    $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: данные пользователя успешно извлечены...';
                    $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: искомый email - найден!';
                    //инициализация свойств
                    $this->id = $id;
                    $this->name = $name;
                    $this->email = $email;
                     /* закрываем запрос */
                     $stmt->close();            // ВНИМАНИЕ!!! Закрывать обязательно!!!
                }else{
                    //пишем ошибку в сессию для вывода на странице востановления пароля (передаётся сначала в парсер GET)
                    $_SESSION['email_not_find'] = '<div class="alert alert-danger" role="alert"><b>Ошибка!</b>
                                                         введённый Вами e-mail на сайте не зарегистрирован.<br>
                                                         Восстановление пароля с использованием электронного адреса: <b>'.$email.'</b> 
                                                         невозможно! :(                                                     
                                                       </div>';
                    $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: искомый email - НЕ найден!';
                    header("location:/PassRestore");//отправляем на главную страницу (встречаем)
                    exit();
                }
            }
        }else{
            $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: ошибка при подготовке SQL запроса SELECT';
        }
        //в случае если пользователь идентифицирован - высылаем ему на указанный email ссылку на востановление пароля 
        if($this->id > 0){
            $hash = $this->generateCode(40);
            $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: ставлю куку "changePassHash" на 2 часа...:<br/><b>'.$hash.'</b>';
            setcookie("changePassHash", $hash, time()+3600*2);
            setcookie("changePassId", $this->id, time()+3600*2);
            $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: отправляю письмо...';
            $sendMail = $this->sendCheckMail($hash);
            if($sendMail){
                $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: письмо отправлено!';
                header("location:/checkYourMail");//отправляем на главную страницу (встречаем)
                exit();
            }
            //var_dump($fakeHesh);die;
            
        }
    }
    //сверка хеша из письмя с хешем в куке. В случае совпадения - обновляем пароль пользователю
    public function chengePass($hash,$cookieHash,$cookieId){
        //var_dump($cookieId);die;
        //$this->mysqli = MsDBConnect::getInstance()->getMysqli(); //получаем метку соединения
        $hash = $this->checkStr($hash);
        $cookieHash = $this->checkStr($cookieHash);
        $id = $this->clearInt($cookieId);
        if($hash == $cookieHash){
            //var_dump($cookieId);die;
            $newPass = $this->generateCode(6);
            $passMd5 = md5(md5($newPass));//шифруем пароль для записи в БД
            
            $sql = "UPDATE user SET pass = ? WHERE id = ?";//подготовка запроса
            //анализ запроса и подготовка к исполнению
            if ($stmt = $this->mysqli->prepare($sql)){
                 /* связываем параметры с метками */
                $stmt->bind_param("si",$passMd5, $id);
                /* запускаем запрос */
                $query = $stmt->execute();
                /* закрываем запрос */
                $stmt->close();            // ВНИМАНИЕ!!! Закрывать обязательно!!!
                if($query){
                    $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: SQL запрос UPDATE прошел успешно...';
                    $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: пароль пользователя успешно обновлен...';
                    $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b> отправляю письмо с новым паролем';
                    //получаем данные пользователя для письма
                    $sql = "SELECT * FROM user WHERE id = $id";//подготовка запроса
                    $query = $this->mysqli->query($sql); // ООП запрос
                    $rez[] = mysqli_fetch_assoc($query);
                    $userData = $rez[0];
                    //отправляем письмо
                    $sendRegDataMail = $this->sendRegDataMail($userData['email'],$newPass,$userData['name'],$userData['patronymic']);
                    if($sendRegDataMail){
                        $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: чищу куки с временным хешем и id пользователя...';
                        //очистиь даннные пользователя из cookie
                        setcookie("changePassHash", "",time()-100,"/");
                        setcookie("changePassId", "",time()-100,"/");
                        $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: ставлю cookie "userEmail" чтобы передать email в сообщение об успешном восстановлении пароля (на 20 секунд!)...';
                        //ставим куку чтобы передать email в сообщение об успешном восстановлении пароля (на 20 секунд!)
                        $_SESSION['userEmail'] = $userData['email']; 
                        //var_dump($_COOKIE['userEmail']); die;
                        
                        
                        header("location:/PassRestored");//отправляем на главную страницу (встречаем)
                        exit();    
                    }
                }else{
                    $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: пароль пользователя обновить не удалось...'.mysqli_error($this->mysqli);
                    //echo mysqli_error($this->mysqli);
                }
            }else{
                $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: ошибка при подготовке SQL запроса UPDATE';//die('dasdasdasd');
            }
        }else{
            $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>: Хеш не совпал!!!';
            header("location:/PassRestoreError");//отправляем на главную страницу (выводим ошибку)
            exit();
        }
    }
    
    //обработка строковых данных получяемых из форм ввода $mysqli  link
    public function checkStr($string){
        $str = mysqli_real_escape_string($this->mysqli,strip_tags(trim($string)));
        $_SESSION['mss_monitor'][] = '<b>MsAuthoriz:</b> checkStr(): проверяю строку: '.$str;
        return ($str);
    }
    
    private function sendCheckMail($hash){
        //Отправляем пользователю ссылку для регистрации
        if(!$this->sitePath){die('<b>MsPassRestore: sendCheckMail</b>: Ошибка: не указан адрес сайта!');}
        $_SESSION['mss_monitor'][] = '<b>MsPassRestore</b>:sendCheckMail: получил хеш: <b>'.$hash.'</b>';
        //$hash = $this->generateCode(40);
        $fakeHash = md5($hash);
        //смена кодировки с utf8 на 1251
        //$name = iconv('UTF-8', 'windows-1251', $this->name);
        /* получатели */
		$to  = "user <".$this->email.">," ; //обратите внимание на запятую
		$to .= "ms <ms-projects@mail.ru>";
		
		
		/* тема\subject */
		$subject = "Восстановление пароля $this->sitePath";
		
		/* сообщение */
		$message = "
		<html>
		<head>
            <meta charset='windows-1251' />
            <meta http-equiv='Content-Type' content='text/html; charset=windows-1251' />
		</head>
		<body>
		<table>
		<center>
		<h4>Здравствуйте ".$this->name." !</h4> <h5>Вы восстанавливаете доступ к сайту $this->sitePath !</h5>
		</center>
		<tr>
			<td><b>Для для завершения процедуры восстановления доступа, <br>перейдите пожалуйста по следующей ссылке:</b>
            <span style='color:#333'><a href='".$this->sitePath."/СhangePass/".$hash."'>".$this->sitePath."/СhangePass/".$fakeHash."</a></span>
			</td>
		 </tr>
		 <tr>
			<td>
				<i><span style='color:green'>телефоны для справок:</span></i><br>
				<i>+375 29 ХХХ-96-73 (МТС Беларусь)</i><br>
				<i>+375 25 ХХХ-66-61 (Life Беларусь)</i><br>
			</td>
		</tr>
		 <tr>
			<td><i><span style='margin-left:300px'>С уважением, администрация $this->sitePath</i></td>
		 </tr>
		 <tr>
			<td><span style='color:red'>P.S. если это письмо попало к вам по ошибке - просто удалите его</span></td>
		 </tr>
		</table>
		</body>
		</html>
		";
		
		/* Для отправки HTML-почты вы можете установить шапку Content-type. */
		$headers  = "MIME-Version: 1.0 \r\n";
		$headers .= "Content-type: text/html; charset = utf-8 \r\n";
		
		/* дополнительные шапки */
		$headers .= "From: MSFrame \r\n";
		/*$headers .= "Cc: birthdayarchive@example.com\r\n";
		$headers .= "Bcc: birthdaycheck@example.com\r\n";*/
		
		/* и теперь отправим из */
		mail($to, $subject, $message, $headers);
        if(mail){
            return true;
        }else{
            die('<b>MsPassRestore: sendCheckMail</b> Не удалось отправить письмо!');
        }
    }
    
    //отправка пользователю регистрационных данных
    public function sendRegDataMail($email, $pass, $name,$patronymic){
        $_SESSION['mss_monitor'][] = 'Отправляю письмо с регистрационными данными...<br>';
        //$sitePath = $this->sitePath;
        /* получатели */
        //смена кодировки с utf8 на 1251
        //$name = iconv('UTF-8', 'windows-1251', $name);
        //$patronymic = iconv('UTF-8', 'windows-1251', $patronymic);
        
		$to  = "user <".trim($email).">," ; //обратите внимание на запятую
		$to .= "ms <ms-projects@mail.ru>";
		
		
		/* тема\subject */
		$subject = "$this->sitePath";
		
		/* сообщение */
		$message = "
		<html>
		<head>
            <meta charset='windows-1251' />
            <meta http-equiv='Content-Type' content='text/html; windows-1251' />
		</head>
		<body>
		<table>
		<center>
		<h4>Уважаемый ".$name." ".$patronymic." !</h4> <h5>Вы успешно восстановили доступ к сайту $this->sitePath</h5>
		</center>
		<tr>
			<td><b>Ваши НОВЫЕ регистрационные данные:</b><br>
            E-mail: <b>".$email."</b><br>
            Пароль: <b>".$pass."</b><br>
            <p>Рекомендуем сохранить эти данные в надёжном месте, и не передавать 3-м лицам.<br>
            <b><span style='color:red'>ПОМНИТЕ!</span></b> Администрация сайта никогда не будет спрашивать ваши регистрационные данные!</p>
            
            <center><span style='color:#333'><a href='".$this->sitePath."' title='Перейти на сайт'>Перейти на сайт!</a></span></center>
			</td>
		 </tr>
		 <tr>
			<td>
				<i><span style='color:green'>телефоны для справок:</span></i><br>
				<i>+375 29 ХХХ-96-73 (МТС Беларусь)</i><br>
				<i>+375 25 ХХХ-66-61 (Life Беларусь)</i><br><br>
			</td>
		</tr>
		 <tr>
			<td><i><span style='margin-left:300px'>С уважением администрация $this->sitePath</i></td>
		 </tr>
		 <tr>
			<td><span style='color:red'>P.S. если это письмо попало к вам по ошибке - просто удалите его</span></td>
		 </tr>
		</table>
		</body>
		</html>
		";
		
		/* Для отправки HTML-почты вы можете установить шапку Content-type. */
		$headers  = "MIME-Version: 1.0 \r\n";
		$headers .= "Content-type: text/html; charset = utf-8 \r\n";
		
		/* дополнительные шапки */
		$headers .= "From: MSFrame \r\n";
		/*$headers .= "Cc: birthdayarchive@example.com\r\n";
		$headers .= "Bcc: birthdaycheck@example.com\r\n";*/
		
		/* и теперь отправим из */
		mail($to, $subject, $message, $headers);
        if(mail){
            return true;
        }else{
            return false;
        }
    }
}
?>