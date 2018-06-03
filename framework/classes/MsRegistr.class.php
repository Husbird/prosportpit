<?php
// ООП +++
//Регистрация пользователей
class MsRegistr /*extends MsDBConnect*/
{
    public $sitePath = 'http://thesis.lyusiena.in.ua'; //текущий адрес сайта по умолчанию
    public $id; //id авторизованного пользователя
    public $name = false;
    public $lastname = false;
    public $patronymic = false;
    public $email = false;
    public $pass = false;
    
    private $mysqli = null;
    
	public $inserted_user_id; //полученный id строки с данными добавленного пользователя
    
	function __construct($params=false){
        $_SESSION['mss_monitor'][] = '<b>Запускаю '.__METHOD__.' !</b><br>';
            //parent:: __construct(); info@
        $this->sitePath = MSS::app()->config->site_path;//инициализация текущего адреса сайта - настраивается в файле конфигурации!!!!
        //var_dump($this->sitePath);die;
		try{
            $this->mysqli = MsDBConnect::getInstance()->getMysqli();
    		  // если получен массив (из формы регистрации):
    		if(is_array($params)){
    		  //var_dump($this->link);
    		$name = self::checkStr($params['name']);
            $patronymic = self::checkStr($params['patronymic']);
    		$lastname = self::checkStr($params['lastname']);
            $email = strtolower(self::checkStr($params['email']));
            
            //проверка наличия в базе принимаемого email
            $cheсkDubble = $this->cheсkDubbleEmail($email);
            if(!$cheсkDubble){
                //возвращаем на страницу регистрации
                header("location:/Registration");
                exit();
            }
            
            //echo $email;exit();
    		$pass = self::checkStr($params['pass']);
            $hash = self::hashGen($email); //генерируем случайную строку для отправки на имэил
            //пишем проверочный hash  и остальные данные в куку
            //unset($_COOKIE['hash']);
            setcookie("hash", $hash, time()+3600*24*2);
            setcookie("name", $name, time()+3600*24*2);
            setcookie("patronymic", $patronymic, time()+3600*24*2);
            setcookie("lastname", $lastname, time()+3600*24*2);
            //setcookie("birthday", $birthday, time()+3600*24*2);
            setcookie("email", $email, time()+3600*24*2);
            setcookie("bible", $pass, time()+3600*24*2);
            $sendCheckMail = self::sendCheckMail($email, $name, $hash);//отправляем письмо с инструкциями
                if($sendCheckMail){
                    //сохраняем аву во временную папку:
                    $structure = './assets/media/images/user/temp';// Желаемая структура папок
                    //$structure = './assets/media/images/user/'.$date_reg.$id_last;// Желаемая структура папок
                    if(is_dir('assets/media/images/user/temp')){
                        $save_path = "assets/media/images/user/temp/".$hash.".jpg";
                        $MsIMGProcess = new MsIMGProcess;
                        $MsIMGProcess->cut_and_save_img_mss(200,5,$save_path);
                        $file_log = 'framework/log/reg_log.mss';
                        $log_title = 'email: '.$email.' | Ава пользователя сохранена '.'путь: '.$save_path. "\n";
                        $file_put = file_put_contents($file_log,$log_title,FILE_APPEND);
                    }else{
                        if (!mkdir($structure, 0777, true)) { //если директорию создать не удалось пишем ошибку в журнал
                         $_SESSION['mss_monitor'][] = 'Не удалось записать аву пользователя во временную папку';
                         $file_log = 'framework/log/error_log.mss';
                         $log_title = 'id: '.$id_last.' | '.$date_reg. 'Не удалось записать аву пользователя во временную папку'. "\n";
                         $file_put = file_put_contents($file_log,$log_title,FILE_APPEND);
                        }else{ //если удалось создать папку пишем аву во временную папку
                            $save_path = "assets/media/images/user/temp/".$hash.".jpg";
                            $MsIMGProcess = new MsIMGProcess;
                            $MsIMGProcess->cut_and_save_img_mss(200,5,$save_path);
                            $file_log = 'framework/log/reg_log.mss';
                            $log_title = 'email: '.$email.' | Ава пользователя сохранена '.'путь: '.$save_path. "\n";
                            $file_put = file_put_contents($file_log,$log_title,FILE_APPEND);    
                        }    
                    }
                    header("location:$this->sitePath/checkYourMail");
                    exit();
                }else{
                    new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                'Ошибка отправки письма подтверждения регистрации! die',__METHOD__);
                    die('Error: Ошибка отправки письма подтверждения регистрации!');
                }
            //если получен НЕ массив (а строка Хеш из письма активации)
            }elseif (!is_array($params)) {
                $checkHash = self::checkHash($params);//сверяем полученный хеш
                //если хеш успешно проверен:
                if($checkHash == true){
                   // var_dump($checkHash); die();
                   $saveUserData = self::saveUser();//пишем данные пользователя в БД
                   //если данные пользователя успешно записаны в БД:
                   if($saveUserData == true){
                        $autoAuth = self::autoAuthUser();//авторизируем нового пользователя
                        //если пользователь успешно авторизован:
                        if($autoAuth == true){
                            $sendMail = self::sendRegDataMail($this->email, $this->pass, $this->name, $this->lastname);//отправляем рег.данные пользователю
                            if($sendMail){
                                header("location:$this->sitePath/congratulations");//отправляем на главную страницу
                                exit(); 
                            }
                        }else{
                                new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                'Ошибка авторизации, возможно у вас выключены "cooke" в браузере... die',__METHOD__);
                            die('Error: Ошибка авторизации, возможно у вас выключены "cooke" в браузере...');
                        }
                   }
                }else{
                    header("location:$this->sitePath/regError");//отправляем на главную страницу regError
                    exit();
                }
                //var_dump($checkHash); die();
            }
		}catch(Exception $e){
			//$e->getMessage();
			echo $e;
		}#try
		
	}
    
    public function checkHash($hash){
        $_SESSION['mss_monitor'][] = 'Сверяю полученный хеш...<br>';
        if(!$hash){
            new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
            'Хеш не получен!... die',__METHOD__);
            die('Error: Хеш не получен!');
        }
        //var_dump($hash);
        //echo "<br>";
        //var_dump($_COOKIE['hash']);die;
	   //echo '<br>Хеш получен:'.$hash;die;
        $hashFromMail = self::checkStr($hash);
        $cookieHash = self::checkStr($_COOKIE['hash']);
        if($hashFromMail == $cookieHash){
            $_SESSION['mss_monitor'][] = 'Хеш - совпал!!!...<br>';
            return true;
        }else{
            $_SESSION['mss_monitor'][] = 'Хеш - <b>НЕ</b> совпал!!!...<br>';//die('Хеш не совпал!');
             new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                'Хеш - <b>НЕ</b> совпал!!!',__METHOD__);
            return false;
        }
    }
    
    private function saveUser(){
        $_SESSION['mss_monitor'][] = 'Обрабатываю полученные данные из формы...';//die('dasdasdasd');
		//получаем из $_COOKIE данные и подготавливаем их к записи в БД
        $this->name = self::checkStr($_COOKIE['name']);
        $this->lastname = self::checkStr($_COOKIE['lastname']);
        $this->patronymic = self::checkStr($_COOKIE['patronymic']);
        $email = self::checkStr($_COOKIE['email']);
        
        $this->pass = self::checkStr($_COOKIE['bible']);
		$passMd5 = md5(md5($this->pass));//шифруем пароль для записи в БД
        
		$email_check = self::checkEmail($email);
		if($email_check == true){
			$this->email = $email;
		}else{
            new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                    'некорректный имэил: '.self::checkStr($email).' ',__METHOD__);
			$this->email = 'некорректный имэил: '.self::checkStr($email);
		}
		//$age = self::clearInt($age);
		$date_reg = time();//дата записи
		//$ip_reg = self::GetRealIp();//$ipTrue = MSS::app()->getRealIp();
        $ip_reg = MSS::app()->getRealIp();
		$adm_mss = 0;//уровень прав пользователя

		//работает !!!!
		try{
            $_SESSION['mss_monitor'][] = 'Начинаю запись данных пользователя в БД...<br>';//die('dasdasdasd');
            /* создаем подготавливаемый запрос */
            $sql = "INSERT INTO user (pass, name, patronymic, lastname, email, date_reg, ip_reg, adm_mss)
							VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
            //анализ запроса и подготовка к исполнению
            if ($stmt = $this->mysqli->prepare($sql)){
                 /* связываем параметры с метками */
                $stmt->bind_param("sssssisi", $passMd5, $this->name, $this->patronymic, $this->lastname, $this->email, $date_reg, $ip_reg, $adm_mss);
                /* запускаем запрос */
                $query = $stmt->execute();
                /* закрываем запрос */
                $stmt->close();            // ВНИМАНИЕ!!! Закрывать обязательно!!!
                if($query){
                    $_SESSION['mss_monitor'][] = 'SQL запрос INSERT прошел успешно...';//die('dasdasdasd');
                    $id_last = mysqli_insert_id($this->mysqli);//возвращает ID сгенерированный при последней операции
                    //копируем аву из временной папки в папку пользователя
                    $MsFileProcess = new MsFileProcess;
                    $MsFileProcess->rename_one_file("assets/media/images/user/temp/".$_COOKIE['hash'].".jpg", 
                        "assets/media/images/user/".$id_last, "assets/media/images/user/".$id_last."/ava.jpg");
                }
            }else{
                new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                    'ошибка при подготовке SQL запроса INSERT...',__METHOD__);
                $_SESSION['mss_monitor'][] = '<p style="color:red; font-weight:bold;">ошибка при подготовке SQL запроса INSERT</p>';//die('dasdasdasd');
            }	  

            //если запись не удалась - выводим ошибку
			if(!$query){
			     new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                    'Данные о пользователе не занесены в БД!',__METHOD__);
				throw new Exception("<p style='color:red; font-weight:bold;'>Ошибка: Данные о пользователе не занесены в БД!<br> $error</p>");
			}else{
			     //если данные успешно записаны в БД:
                 new MsLogWrite('registration',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                    'Данные пользователя успешно записаны в БД...',__METHOD__);
				$this->inserted_user_id = mysqli_insert_id($this->mysqli);//пишем в свойство присвоеный зписи id
                $_SESSION['mss_monitor'][] = 'Данные пользователя успешно записаны в БД...<br>';
                //удаляем куки: ОБЯЗАТЕЛЬНО!
                $_SESSION['mss_monitor'][] = 'Удаляю cookie...<br>';
                setcookie("hash", "",time()-100,"/");
                setcookie("name", "",time()-100,"/");
                setcookie("lastname", "",time()-100,"/");
                setcookie("patronymic", "",time()-100,"/");
                setcookie("email", "",time()-100,"/");
                setcookie("bible", "",time()-100,"/");
                $_SESSION['mss_monitor'][] = 'Сookie удалены...<br>';
				return true; ///!!!!!!!!!!!!!!!! не забывать возвращать true в случае удачи в безответных запросах
			}
		
		}catch(Exception $e){
			//$e->getMessage();
			echo $e;
			return false;
		}	
	}
    
    //автоматическая авторизация пользователя (использовать только сразу после регистрации (saveUser))
	private function autoAuthUser(){
	    $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> Запускаю автоматическую авторизацию пользователя...<br>';
		$hash = md5(self::generateCode(10));//генерируем новый хеш
		$id = $this->inserted_user_id;//получаем присвоенный id только что зарегистрированного пользователя
        //$ip = self::GetRealIp();
        $ip = MSS::app()->getRealIp();
		//var_dump($id);exit;
        //echo "<hr>Записанный в БД Хеш: $hash <hr>";
		
		try{
            //Обновляю хеш и ip пользователя в БД
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.': </b> Обновляю Хеш и ip пользователя в БД...<br>';
			$sql = "UPDATE user SET hash = '$hash', ip = '$ip' WHERE id = '$id'";
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>сформирован sql запрос: '.$sql.'<br>';
			$res = $this->mysqli->query($sql); //обновляем хеш пользователя в БД
			echo mysqli_error($this->mysqli);
			//echo $sql;
			//$res = $mysqli->query($sql);
			if(!$res){
			      new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                    'ошибка обновления данных пользователя ',__METHOD__);
				throw new Exception("<p style='color:red; font-weight:bold;'>Ошибка: первая автоавторизация не прошла!</p>");die('Error: ошибка обновления данных пользователя');
			}else{
			     new MsLogWrite('registration',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                    'Обновление Хеш и ip прошло успешно... Ставлю cookie на 2 часа...',__METHOD__);
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> Обновление Хеш и ip прошло успешно...<br>';
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> Ставлю cookie на 2 часа...<br>';
                //unset($_COOKIE);
                //setcookie("bible", "",time()-100,"/");
                $_SESSION['auth'] = true; //ставим метку в сессии о успешной авторизации
                setcookie("id", $id, time()+3600*2,"/");//пишем в cooke id пользователя
                setcookie("hash", $hash, time()+3600*2,"/");//пишем в cooke новый хеш
                setcookie("ip", $ip, time()+3600*2,"/");//пишем в cooke текущий ip пользователя
                
                //setcookie("hash", $hash, time()+3600*24*30);
                //echo "<hr>Записанный в куку Хеш: $hash <hr>";
                //var_dump($_COOKIE); die('user data updated!');
				return true; ///!!!!!!!!!!!!!!!! не забывать возвращать true в случае удачи в безответных запросах
			}
		}catch(Exception $e){
			//$e->getMessage();
			echo $e;
			return false;
		}
	}
    
    private function hashGen($string) {
        $salt = rand(1000, 1000000);
        $word = $salt.$string;
        // получение хэша
        $hash = md5($word);
        return $hash;//md5($hash);
    }
    //обработка строковых данных получяемых из форм ввода
    private function checkStr($string){
        $str = mysqli_real_escape_string($this->mysqli,strip_tags(trim($string)));
        return ($str);
    }
    
   	//проверка имэила на синтаксис
	private function checkEmail($email){
		//создаём массив ошибок
    	$error = array(); 
		if (isset ($email))
			{
				$email = strip_tags(trim($email));
				if ($email != "")
					{
						$regV = '/^[a-zA-Z0-9\-\_\.]{1,25}\@[a-zA-Z0-9\-\_]{2,15}\.[a-zA-Z0-9]{2,4}$/';
						$rez = preg_match($regV, $email);
						if (!$rez)
							{
								$error[] = "<font color='#00CC00' size='-2'>некорректный E-mail (не будет сохранён)</font>";
								if (strlen($email) > 46) $error[] = "Больше 46 символов";
								
							}
					}
				else
					$error[] = "<font color='#00CC00' size='-2'>E-mail не введён!</font>";
			}

		if (count($error) == 0)
			{
				
				return true;
			}
		 else
			return false;
	}
    //проверка введённого в форме регистрации email на наличие в БД 
    //(если дубликат найден cheсkDubbleEmail возвращает false, если нет то true)
    private function cheсkDubbleEmail($email = false){
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> проверяем на наличие в БД переданного email';
        $email = strtolower(mysqli_real_escape_string($this->mysqli,strip_tags(trim($email))));
        $sql = "SELECT id FROM user WHERE email = ?";
        if ($stmt = $this->mysqli->prepare($sql)){
             /* связываем параметры с метками */
            $stmt->bind_param("s", $email);
            /* запускаем запрос */
            $query = $stmt->execute();
            if($query){
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> SQL запрос SELECT прошел успешно...';
                
                /* связываем переменные с результатами запроса */
                $stmt->bind_result($id);
                /* получаем значения */
                $dubble = $stmt->fetch(); //возвращает true или false
                //var_dump($x);die;
                if($dubble){
                    new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                        'ошибка: пользователь с таким Email уже зарегистрирован! ',__METHOD__);
                    $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> ошибка: пользователь с таким Email уже зарегистрирован!...';
                    $stmt->close();            // ВНИМАНИЕ!!! Закрывать обязательно!!!
                    //пишем в сессию сообщение об ошибке и другие данные для передачи в форму регистрации
                    $_SESSION['registration_error'] = '<div class="alert alert-danger" role="alert"><b>Ошибка!</b> 
                                                        Пользователь с Email (<b>'.$email.'</b>) уже зарегистрирован!
                                                    </div>';
                    $_SESSION['registration_form_data']['email'] = $email;
                    $_SESSION['registration_form_data']['reg_form_status_email'] = 'has-error'; //выделяем поле с ошибкой
                    
                    //пишем в массив данные для возврата в форму регистрации
                    $_SESSION['registration_form_data']['name'] = $name;
                    $_SESSION['registration_form_data']['reg_form_status_name'] = 'has-success';//выделяем поле как успешное
                    
                    $_SESSION['registration_form_data']['patronymic'] = $patronymic;
                    $_SESSION['registration_form_data']['reg_form_status_patronymic'] = 'has-success';//выделяем поле как успешное
                    
                    $_SESSION['registration_form_data']['lastname'] = $lastname;
                    $_SESSION['registration_form_data']['reg_form_status_lastname'] = 'has-success';//выделяем поле как успешное
                    
                    $_SESSION['registration_form_data']['reg_form_status_pass'] = 'has-success';//выделяем поле как успешное
                    
                     return false;
                }else{
                    new MsLogWrite('registration',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                        'Ok... введённый Email ('.$email.') используется впервые! ',__METHOD__);
                    $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: Ok... введённый Email используется впервые!';
                    return true;
                }
            }
        }else{
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> ошибка при подготовке SQL запроса SELECT';
        }
    }
    
    private function sendCheckMail($email,$name,$hash){
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> Отправляю письмо с инструкцией по регистрации...<br>';
        //Отправляем пользователю ссылку для регистрации
        if(!$this->sitePath){
            new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                'не указан адрес сайта! ... die ',__METHOD__);
            die('<br>Error: не указан адрес сайта!<br>');
        }
        $fakeHash = md5($hash);
        //смена кодировки с utf8 на 1251
        //$name = iconv('UTF-8', 'windows-1251', $name);
        /* получатели */
		$to  = "user <".$email.">," ; //обратите внимание на запятую
		$to .= "ms <ms-projects@mail.ru>";
		
		
		/* тема\subject */
		$subject = "Регистрация на сайте $this->sitePath";
		
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
		<h4>Здравствуйте ".$name." !</h4> <h5>Вы регистрируетесь на сайте $this->sitePath ...</h5>
		</center>
		<tr>
			<td><b>Для завершения Вашей регистрации, <br>перейдите пожалуйста по следующей ссылке:</b>
            <span style='color:#333'><a href='".$this->sitePath."/activate/".$hash."'>".$this->sitePath."/activate/".$fakeHash."</a></span>
			</td>
		 </tr>
		 <tr>
			<td>
				<i><span style='color:green'>телефоны для справок:</span></i><br>
				<i>+38 (066)357-99-57</i><br>
				<i>+38 (073)450-87-82</i><br>
                
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
		//$headers .= "Content-type: text/html; charset=windows-1251\r\n";
        $headers .= "Content-type: text/html; charset = utf-8 \r\n";
		
		/* дополнительные шапки */
		$headers .= "From: Prosportpit.com \r\n";
		/*$headers .= "Cc: birthdayarchive@example.com\r\n";
		$headers .= "Bcc: birthdaycheck@example.com\r\n";*/
		
		/* и теперь отправим из */ //mail('*@gmail.com', 'Messages from your site', $message, "Content-type:text/html; charset = utf-8");
		mail($to, $subject, $message, $headers);
        if(mail){
            return true;
        }else{
            new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                'Не удалось отправить письмо пользователю! ... die ',__METHOD__);
            die('<br>Error: Не удалось отправить письмо с инструкцией пользователю!');
        }
    }
	
        //отправка пользователю регистрационных данных
    public function sendRegDataMail($email, $pass, $name,$lastname){
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> Отправляю письмо с регистрационными данными...<br>';
        $sitePath = $this->sitePath;
        /* получатели */
        //смена кодировки с utf8 на 1251
        //$name = iconv('UTF-8', 'windows-1251', $name);
        //$lastname = iconv('UTF-8', 'windows-1251', $lastname);
        
		$to  = "user <".trim($email).">," ; //обратите внимание на запятую
		$to .= "ms <ms-projects@mail.ru>";
		
		
		/* тема\subject */
		$subject = "info@$this->sitePath";
		
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
		<h4>Уважаемый ".$name." ".$lastname." !</h4> <h5>Вы успешно зарегистрировались на сайте $this->sitePath</h5>
		</center>
		<tr>
			<td><b>Ваши регистрационные данные:</b><br>
            E-mail: <b>".$email."</b><br>
            Пароль: <b>".$pass."</b><br>
            <p>Рекомендуем сохранить эти данные в надёжном месте, и не передавать 3-м лицам.<br>
            <b><span style='color:red'>ПОМНИТЕ!</span></b> Администрация сайта никогда не будет спрашивать ваши регистрационные данные!</p>
            
            <center><span style='color:#333'><a href='".$sitePath."' title='Перейти на сайт'>Перейти на сайт!</a></span></center>
			</td>
		 </tr>
		 <tr>
			<td>
				<i><span style='color:green'>телефоны для справок:</span></i><br>
				<i>+38 (066)357-99-57</i><br>
				<i>+38 (073)450-87-82</i><br>
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
		$headers .= "From: Prosportpit.com \r\n";
		/*$headers .= "Cc: birthdayarchive@example.com\r\n";
		$headers .= "Bcc: birthdaycheck@example.com\r\n";*/
		
		/* и теперь отправим из */
		mail($to, $subject, $message, $headers);
        if(mail){
            return true;
        }else{
            new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                'Не удалось отправить письмо c регистрационными данными пользователю! ...',__METHOD__);
        }
    }
	
	
    public function logOut(){
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> автовыход ...';
        setcookie('id', '', time()-60*60*24*30, '/'); 
		setcookie('hash', '', time()-60*60*24*30, '/');
        new MsLogWrite('registration',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                'отработал автовыход! ...',__METHOD__);
    }

	
	//обработка числа перед записью в БД
	public function clearInt($data){
		return abs((int)$data);
	}
	
	//генерирование случайного числа
	public function generateCode($length=6) 
		  	{ 
				$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789"; 
				$code = ""; 
				$clen = strlen($chars) - 1;   
				while (strlen($code) < $length) 
					{ 
						$code .= $chars[mt_rand(0,$clen)];   
					} 
				return $code; 
		   }
	
	//получаем ip пользователя
	/**
 * public function GetRealIp(){
 *         
 *         $ip = $_SERVER['REMOTE_ADDR'];
 *         $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> определён ip '.$ip.'';
 *         return $ip;
 * 	}
 */
}
?>