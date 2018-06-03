<?php
//ООП +++
class MsAuthoriz /*extends MsDBConnect*/{

    public $sitePath = '/Login'; //адрес страницы авторизации
/**
 *     public $id; //id авторизованного пользователя
 *     public $name = false;
 *     public $lastname = false;
 *     public $patronymic = false;
 *     public $email = false;
 *     public $pass = false;
 */
    private $mysqli = null;

	function __construct($params=false){
    //parent:: __construct();
            //session_start(); //включаем сессии!
            //var_dump(session_start()); die;
            $this->mysqli = MsDBConnect::getInstance()->getMysqli(); //получаем метку соединения
		  // если получен массив (из формы регистрации):          var_dump($params);
    		if(is_array($params)){
                 $_SESSION['mss_monitor'][] = '<b>Начинаю авторизацию...</b>';
                //var_dump($params);die('sdad');
      		    //получаем и проверяем e-mail
                $email = strtolower(self::checkStr($params['email']));
                $checkEmail = self::checkEmail($email);
                if ($checkEmail != true){//проверка имэйла такой же функцией как и при регистрации
                    //var_dump($checkEmail); die('email');
                    header("location:$this->sitePath");//отправляем на главную страницу regError
                    exit();
                    //return false;//если имэил не прошел проверку на валидность - дальше не проверяем
                    //$email = false;//если имэил не прошел проверку на валидность - дальше не проверяем
                }
                //var_dump($checkEmail); die('email');
                //получаем и проверяем пароль
                $_SESSION['mss_monitor'][] = 'получаю и проверяю пароль...';
        		$pass = self::checkStr($params['pass']);
                $pass = md5(md5($pass));
                
                //готовим новый хеш
                $hash = md5(self::generateCode(10));
                
                 ///получаем текущий ip
                $ip = MSS::app()->getRealIp();
                //$ip = self::GetRealIp(); //получаем текущий ip MSS::app()->getRealIp();
                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получен текущий ip пользователя <b>'.$ip.'</b>'; 
                
                    $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: запрашиваю данные пользователя...';
                    
                    //$sql = "SELECT * FROM user WHERE pass = '$pass' AND email = '$email'" or 
                                                            //die("Error: Ошибка получения данных пользователя!<br>" . mysqli_error($mysqli));
                    //$query = mysqli_query($this->mysqli,$sql);
                    $sql = "SELECT id, lastname FROM user WHERE pass = ? AND email = ?";
                    //var_dump($sql);die;
                    //анализ запроса и подготовка к исполнению
                    if ($stmt = $this->mysqli->prepare($sql)){
                         /* связываем параметры с метками */
                        $stmt->bind_param("ss", $pass, $email);
                        /* запускаем запрос */
                        $query = $stmt->execute();
                        if($query){
                            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: SQL запрос SELECT прошел успешно...'; //+
                            
                            /* связываем переменные с результатами запроса */
                            $stmt->bind_result($id,$lastname);
                            /* получаем значения */
                            $x = $stmt->fetch(); //возвращает true или false
                            if($x){
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: данные пользователя успешно извлечены...';
                                //пишем в массив полученные данные
                                $data = array(
                                'id' => $id,
                                'lastname' => $lastname,
                                );
                                 /* закрываем запрос */
                                 $stmt->close();            // ВНИМАНИЕ!!! Закрывать обязательно!!!
                            }else{
                                new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                    'неверный e-mail или пароль - 1, ошибка извлечения данных пользователя!',__METHOD__);
                                $_SESSION['authErr'] = '<div class="alert alert-danger" role="alert"><b>Ошибка!</b> неверный e-mail или пароль
                                                        </div>';
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ошибка извлечения данных пользователя!';
                            }
                            
                            //var_dump($data); die();
                            //$dataArray = mysqli_fetch_assoc($query);//массив со вссеми данными пользователя
                        }
                    }else{
                        new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                'ошибка при подготовке SQL запроса SELECT ('.$sql.') ',__METHOD__);
                        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ошибка при подготовке SQL запроса SELECT';
                    }	  
                    
                    
                    //$query = $this->mysqli->query($sql); //ООП запрос
                    //$dataArray = mysqli_fetch_assoc($query);//массив со вссеми данными пользователя 
                    //var_dump($dataArray); die('query!');
                    /** альтернативный вариант
                     * $res = $link->query($sql);
                     * $massiv = mysqli_fetch_assoc($res);
                     */
                    //если проверка логина и пароля пройдена успешно:
                    if($data['id'] > 0){      //если пользователь найден
                        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: обновляю данные пользователя в БД...';
                        $id = $data['id'];
                        //var_dump($id); die;
                        //$sql = "UPDATE user SET hash = '$hash', ip = '$ip' WHERE id = '$id'";//обновляем данные пользователя
                        $sql = "UPDATE user SET hash = ?, ip = ? WHERE id = ?";//подготовка запроса
                        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: анализ SQL запроса UPDATE и подготовка к исполнению...';
                        //var_dump($ip);
                        //$stmt = $this->mysqli->prepare($sql);
                        //printf("Ошибка: %s.\n", $this->mysqli->error);
                        //анализ запроса и подготовка к исполнению
                        if ($stmt = $this->mysqli->prepare($sql)){
                             /* связываем параметры с метками */
                            $stmt->bind_param("ssi", $hash, $ip, $id);
                            /* запускаем запрос */
                            $query = $stmt->execute();
                            //var_dump($query->error());
                            //printf("Ошибка: %s.\n", $stmt->error);
                            /* закрываем запрос */
                            $stmt->close();            // ВНИМАНИЕ!!! Закрывать обязательно!!!
                            if($query){
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: SQL запрос UPDATE записи с id: '.$id.'прошел успешно...';
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: в БД записан ip: '.$ip.' (пишу его в cookie ...)';
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: в БД записан хеш: '.$hash.' (пишу его в cookie...)';
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: данные пользователя успешно обновлены...';
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ставлю метку об авторизации в сессию...';
                                
                                $_SESSION['auth'] = true; //ставим метку в сессии о успешной авторизации
                                //$_SESSION['ip_current'] = $ip; //пишу в сессию для дальнейшей сверки в MsCheckRole
                                $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ip уже в сессии: ($_SESSION[ip_current]): '.$_SESSION['ip_current'].' (пишу его в cookie...)';
                                //если данные обновлены успешно и получена отметка 72 ставим куки на 3 дня
                                if($params['optionsRadios'] == 72){
                                    $_SESSION['mss_monitor'][] = 'ставлю куки на 72 часа...';
                                    new MsLogWrite('log_in',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                    'пользователь прошел авторизацию, его данные обновлены, ставлю cookie на 72 часа',__METHOD__);
                                 //die('кука на 72 часа');
                                     //ставим куки на 72 часа
                                    setcookie("id", $id, time()+3600*24*3);
                                    setcookie("hash", $hash, time()+3600*24*3);
                                    setcookie("ip", $ip, time()+3600*24*3);
                                //если выбрали чужой компьютер ставим куки на 2 часа
                                }elseif($params['optionsRadios'] == 2){
                                    $_SESSION['mss_monitor'][] = 'ставлю куки на 2 часа...';
                                    new MsLogWrite('log_in',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                    'пользователь прошел авторизацию, его данные обновлены, ставлю cookie на 2 часа',__METHOD__);
                                 //die('кука на 2 часа');
                                    //если чекбокс не отмечен ставим куки на 24 часа
                                    setcookie("id", $id, time()+3600*2);
                                    setcookie("hash", $hash, time()+3600*2);
                                    setcookie("ip", $ip, time()+3600*2);
                                }else{
                                    new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                    'Ошибка передачи данных из формы входа (чекбокс)!',__METHOD__);
                                    die('Ошибка передачи данных из формы входа (чекбокс)!');
                                }
                                $_SESSION['mss_monitor'][] = 'приветствую на главной...';
                                header("location:/Welcome");//отправляем на главную страницу (встречаем)
                                exit();
                            }else{
                                new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                'данные пользователя обновить не удалось...',__METHOD__);
                                echo $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: данные пользователя обновить не удалось...'.mysqli_error($this->mysqli);
                                //echo mysqli_error($this->mysqli);
                            }
                        }else{
                            new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                'ошибка при подготовке SQL запроса UPDATE ('.$sql.') ',__METHOD__);
                            echo $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: ошибка при подготовке SQL запроса <br/>';//die('dasdasdasd');
                        }	  
                        
            			//$res = $this->mysqli->query($sql);//ООП запрос
            			//echo mysqli_error($this->mysqli);
            			//echo $sql;
            			//если данные НЕ обновлены успешно:
                     //если пользователь не найден в БД: (id <=0 )  
                    }else{
                        new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                                'неверный e-mail или пароль',__METHOD__);
                        $_SESSION['authErr'] = '<div class="alert alert-danger" role="alert">
                                                    <b>Ошибка!</b> неверный e-mail или пароль
                                                </div>';
                         $_SESSION['mss_monitor'][] = 'данные пользователя получить НЕ удалось...';
                         header("location:$this->sitePath");//отправляем на главную страницу regError
                         exit();
                    }
	       }else{
	           new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                    'Ошибка приёма данных в процессе авторизации!',__METHOD__);
	           die('Ошибка приёма данных в процессе авторизации!');
	       }
    }
	
    //обработка строковых данных получяемых из форм ввода $mysqli  link
    public function checkStr($string){
        $str = mysqli_real_escape_string($this->mysqli,strip_tags(trim($string)));
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.':</b> проверяю строку: '.$str;
        return ($str);
    }
    
    //проверка имэила
	private function checkEmail($email){
	    $_SESSION['mss_monitor'][] = '<b>MsAuthoriz:</b> checkEmail(): проверяю синтаксис email...';
		//создаём массив ошибок
    	$error = array(); 
		if (isset ($email)){
				$email = strip_tags(trim($email));
				if ($email != ""){
					$regV = '/^[a-zA-Z0-9\-\_\.]{1,25}\@[a-zA-Z0-9\-\_]{2,15}\.[a-zA-Z0-9]{2,4}$/';
					$rez = preg_match($regV, $email);
					if (!$rez){
						$error[] = "<font color='#00CC00' size='-2'>некорректный E-mail (не будет сохранён)</font>";
						if (strlen($email) > 46) $error[] = "Больше 46 символов";
					}
				}else{
				    $error[] = "<font color='#00CC00' size='-2'>E-mail не введён!</font>";    
				}
			}

		if (count($error) == 0){
            $_SESSION['mss_monitor'][] = 'checkEmail(): email успешно прошел проверку!';
            return true;
		}else{
            $_SESSION['mss_monitor'][] = 'checkEmail(): Неверный синтаксис e-mail!';
            $_SESSION['authErr'] = '<div class="alert alert-danger" role="alert">
                                        Ошибка! Неверный синтаксис e-mail: <b>'.$email.'</b>
                                    </div>';
            return false;
		}
	}

	//обработка числа перед записью в БД
	public function clearInt($data){
        $_SESSION['mss_monitor'][] = '<b>MsAuthoriz:</b> clearInt(): привожу к числу- '.$data;
        $_SESSION['mss_monitor'][] = '...ok';
		return abs((int)$data);
	}
	
	//генерирование случайного числа
	public function generateCode($length=6){
        $_SESSION['mss_monitor'][] = '<b>MsAuthoriz:</b> generateCode(): готовлю новый хеш...';
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789"; 
        $code = ""; 
        $clen = strlen($chars) - 1;   
        while (strlen($code) < $length) 
        	{ 
        		$code .= $chars[mt_rand(0,$clen)];   
        	}
        $_SESSION['mss_monitor'][] = '...ok';
        return $code; 
    }
	
	//получаем ip пользователя
	/**
 * public function GetRealIp(){
 *          $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получаю текущий ip пользователя...</span>';

 * 		   $ip=$_SERVER['REMOTE_ADDR'];
 *            $ipFrom = '$_SERVER[REMOTE_ADDR]';
 *            $ipServer = $_SERVER['SERVER_ADDR'];
 * 		 
 *          $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получен IP: <b>'.$ip.'</b> из '.$ipFrom.'</span>';
 *          $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: (IP Сервера: <b>'.$ipServer.'</b> из $_SERVER[SERVER_ADDR]</span>';
 *          self::get_all_ip();
 * 		 return $ip;
 * 	}
 */
    //получаем все возможные ip
    /**
 *  public function get_all_ip() {
 *         $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: получаю все возможные ip из заголовков HTTP...</span>';
 *         $ip_pattern="#(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)#";
 *         $ret="";
 *         foreach ($_SERVER as $k => $v) {
 *             if (substr($k,0,5)=="HTTP_" AND preg_match($ip_pattern,$v)) $ret.=$k.": ".$v."\n";
 *         }
 *         $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: найдены ip: '.$ret.'</span>';
 *         return $ret;
 *      }
 */
}
?>