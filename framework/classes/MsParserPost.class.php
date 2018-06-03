<?php
//ООП +++
class MsParserPost{

    //результат работы парсера
    public $action;//действие
    public $model_name;//имя модели
    private $mysqli = null; //метка соединения с БД
    public $params = array(); //массив полученных параметров из запроса POST
    
    public $result = array();//общий результат работы парсера (массив данных)
    
    function __construct($post = false){
        //var_dump($post);echo '<hr>';
        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: запускаю парсер запроса POST...<br>';
        if(!$post){die('Запрос пуст!');}
        //var_dump($post);die;
        $this->mysqli = MsDBConnect::getInstance()->getMysqli(); //получаем метку соединения с БД
        //ОБРАБОТКА ЗАПРОСА
        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: обработка POST запроса ...<br>';
        $this->params = $this->filterPost($post); //входящий запрос обрабатываем фильтром и убиваем $_POST 
        //var_dump($this->params);die;
        //phpinfo();die;
        
        //получаем action
        $this->getAction($this->params); //присваиваем свойству action значение (выбераем последний элемент массива)    
        //var_dump($this->action);


        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: определено действие: <b>'.$this->action.'</b> ...<br>';
        //var_dump($this->action);die();
        switch($this->action){
            //если отправлено из формы регистрации
            case "registration":
            
            new MsLogWrite('registration',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'попытка регистрации',__METHOD__);//запись в журнал
            new MsRegistr($this->params);//регистрируем пользователя (пишем данные в БД и авторизуем)
            break;
            //если из формы входа
            case "log_in":
            
            $authorization = new MsAuthoriz($this->params);//регистрируем пользователя (пишем данные в БД и авторизуем)
            //var_dump($authorization); pass_restore
            break;
            
            //если ввели Email для восстановления пароля
            case "pass_restore":
            
            new MsLogWrite('pass_restore',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'попытка восстановления пароля',__METHOD__);//запись в журнал
            $passRestore = new MsPassRestore();//регистрируем пользователя (пишем данные в БД и авторизуем)
            $passRestore->checkAndSend($this->params);
            //var_dump($authorization); pass_restore
            break;
            
            //если ввели пользователь сохраняет изменения в личном кабинете
            case "user_settings_update":
            //проверка на соответствие прав
            if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),SuperUser,Moderator,User')){
                header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
                exit();
            };
            //проверяем есть ли директория для сохранения аватарки. Если нет создаём её.
            $MsFileProcess = new MsFileProcess;
            $check_and_create_dir = $MsFileProcess->check_and_create_dir("assets/media/images/{$this->params['table_name']}/{$this->params['id']}");
            if($check_and_create_dir){
                new MsLogWrite('update',MSS::app()->config->save_all,MSS::app()->config->log_files_write,
                    'директория для записи аватарки - существует...',__METHOD__);
            }else{
                new MsLogWrite('error',MSS::app()->config->save_all,MSS::app()->config->log_files_write,
                'Ошибка записи аватарки пользователя. Директория отсутствует или ошибка при её создании.',__METHOD__);//запись в журнал
            }
            
            $save_path = "assets/media/images/".$this->params['table_name']."/".$this->params['id']."/ava.jpg"; //путь для сохранения аватарки
            $MsIMGProcess = new MsIMGProcess;
            $MsIMGProcess->cut_and_save_img_mss(200,1,$save_path);
            header('location:'.$this->params["back_url"].'');
            exit();
            break;
            
            //приём данных из формы обратной связи
            case "dispatch_massage":
            
            //данные, введённые пользователем для вставки в поля формы в случае ошибки
            $_SESSION['contactFormClient_name'] = trim($this->params['client_name']);
            $_SESSION['contactFormEmail'] = trim($this->params['email']);
            $_SESSION['contactFormUser_massage'] = trim($this->params['user_massage']);
            //проверка правильности ввода кода с картинки
            $MsCaptcha = new MsCaptcha;
            $captchaCodCheck = $MsCaptcha->captchaCodCheck($this->params['cod']);
            if(!$captchaCodCheck){
                $_SESSION['captchaCheckErrorMassage'] = "Попробуйте ввести код ещё раз!";
                $_SESSION['sendMailReport'] = "
                                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center; font-size:12px;'>
                                    <br/>
                                    <div class='alert alert-danger' role='alert'>
                                        <p>Неверно введён код с картинки</p>
                                        <p>Мы обновили код, попробуйте ввести его ещё раз</p>
                                    </div>
                                    <br/>
                                </div>
            ";
                header('location:/contacts');
                exit();
            }
            
            new MsLogWrite('dispatch_massage',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'попытка отправки сообщения с сайта',__METHOD__);//запись в журнал
            $MsSendMail = new MsSendMail;
            $timeProcess = new MsTimeProcess;
            $sitePath = MSS::app()->config->site_path;
            $massage_date = $timeProcess->dateFromTimestamp(time());
            $array = array("developer" => "ms-projects@mail.ru", "admin" => "prosportpit@mail.ru"); //кому отправляем
            $subject = "Сообщение пользователя сайта ".$sitePath." от ".$massage_date.""; //тема сообщения
            $text = "
                <html>
            		<head>
                        <meta charset='windows-1251' />
                        <meta http-equiv='Content-Type' content='text/html; charset=windows-1251' />
            		</head>
                    <body>
                		<table>
                		<center>
                		<h4>Здравствуйте!</h4>
                		</center>
                		<tr>
                			<td>
                                <p>".$massage_date." с сайта".$sitePath.", поступило сообщение <br>от пользователя: <b>".$this->params['client_name']."</b></p>
                                <p>E-mail пользователя: <b>".$this->params['email']."</b></p>
                                <p>Текст сообщения:</p>
                                <p><i>".$this->params['user_massage']."</i></p>
                                <span style='color:#333'><a href='".$sitePath."'>Перейти на сайт</a></span>
                			</td>
                		 </tr>
                         
                		 <tr>
                			<td>
                                <i><span style='margin-left:300px'>Удачи!</span></i><br>
                                <i><span style='margin-left:300px'>С уважением, $sitePath</span></i>
                                <i><span style='margin-left:300px'>г.Донецк</span></i>
                            </td>
                		 </tr>
                		 <tr>
                			<td><span style='color:red'> P.S. Письмо отправлено автоматически <br> если это письмо попало к вам по ошибке - просто удалите его</span></td>
                		 </tr>
                		</table>
            		</body>
          		</html>
            "; //полный текст сообщения (с тегами)
            
            //если пользователь отправляет пустое сообщение
            if(trim($this->params['user_massage']) == ""){
                $_SESSION['massageCheckErrorMassage'] = "Нельзя отправлять пустое сообщение!";
                $_SESSION['sendMailReport'] = "
                                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center; font-size:12px;'>
                                    <br/>
                                    <div class='alert alert-danger' role='alert'>
                                        <p>Ошибка!</p>
                                        <p>Вы пытаетесь отправить пустое сообщение!</p>
                                    </div>
                                    <br/>
                                </div>
            ";
                header('location:/contacts');
                exit();
            }
            //проверяем на корректность имэил:
            $emailCheck = $MsSendMail->emailCheck($this->params['email']);
            if($emailCheck === true){
                $x = $MsSendMail->sendMail($array,$from = false,$subject,$text);//отправка письма    
            }else{
                foreach($emailCheck as $key=>$errors){
                    $errors = $errors."<br/>".$errors;
                }
                new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                        'ошибка: введён некорректный email ('.$this->params['email'].')!<br>
                        Имя: '.$this->params['client_name'].'<br>
                        Сообщение: '.$this->params['user_massage'].'<br>
                        emailCheck = '.$emailCheck.'<br>
                        Найдены следующие ошибки:<br> '.$errors.'',__METHOD__);
                $x = false;
                $_SESSION['emailCheckErrorMassage'] = "Проверьте правильность введённого Вами адреса электронной почты <i>{$this->params['email']}</i>";
                header('location:/contacts'); //
                exit();
            }
            //формируем сообщение об отправке пользователю
            if($x){
                //данные, введённые пользователем для вставки в поля формы в случае ошибки
                unset($_SESSION['contactFormClient_name']);
                unset($_SESSION['contactFormEmail']);
                unset($_SESSION['contactFormUser_massage']);
                
                $_SESSION['sendMailReport'] = "
                                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center; font-size:12px;'>
                                    <br/>
                                    <div class='alert alert-success' role='alert'>
                                        <p><span class='glyphicon glyphicon-ok-circle'></span> Ваше сообщение успешно отправлено!</p>
                                        <p>Наш ответ будет выслан на указанный Вами e-mail:</p>
                                        <p><b>{$this->params['email']}</b>.</p>
                                        <p>С уважением, администрация $sitePath!</p>
                                    </div>
                                    <br/>
                                </div>
            ";
            }else{
                $_SESSION['sendMailReport'] = "
                                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center; font-size:12px;'>
                                    <br/>
                                    <div class='alert alert-danger' role='alert'>
                                        <p>К сожалению письмо отправить не удалось!</p>
                                        <p>Мы уже работаем над исправлением данной ошибки.</p>
                                        <p>Извините за неудобства.</p>
                                        <p>С уважением, администрация $sitePath!</p>
                                    </div>
                                    <br/>
                                </div>
            ";
            new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                        'ошибка: Пользователю не удалось отправить сообщение с сайта 
                        <br>Имя: '.$this->params['client_name'].'
                        <br>Email: '.$this->params['email'].'
                        <br>Сообщение: '.$this->params['user_massage'].'
                        <br>Другие ошибки: '.$errors.'',__METHOD__);
            }
            
            header('location:/contacts'); //
            exit();
            break;
            
            
            //если добавили комментарий
            case "gbook_add_comment":
            
            new MsLogWrite('gbook_add_comment',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'',__METHOD__);//запись в журнал
            //require_once(MSS::app()->config->framework_modules.'/gbook/Gbook_ms.class.php');//файл модуля гостевой книги
            $gBook = new MsGbook();//подключаем модуль гостевой книги
            $gBook->catchFormData($this->params);
            //$passRestore->checkAndSend($this->params);
            //var_dump($authorization); pass_restore
            break;
            
            //если обновили
            case "update":
            new MsLogWrite('update',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'попытка обновления данных...',__METHOD__);//запись в журнал
            //для пользователей, имеющих права переданные в accessCheck - доступ будет открыт, для остальных закрыт!
            if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),Moderator')){
                header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
                exit();
            };
            $MsDBProcess = new MsDBProcess;
            //var_dump($this->params['id']);die; table_name
            $update = $MsDBProcess->universalUpdateDB($this->params['table_name'],$this->params['id'],$this->params);//универсальное свойство обновления данных
            if($update){
                //обновляем картинку
                if($this->params['table_name'] == 'author'){
                    $save_path = "assets/media/images/".$this->params['table_name']."/_".$this->params['id']."/_ava.jpg";
                }else{
                    $save_path = "assets/media/images/".$this->params['table_name']."/".$this->params['id'].".jpg";
                }
                $MsIMGProcess = new MsIMGProcess;
                $MsIMGProcess->cut_and_save_img_mss(600,5,$save_path);
                //возврат на исходную страницу
                //$id = $this->params["id"];
                //die($this->params["back_url"]);
                new MsLogWrite('update',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'успешно обновлено...',__METHOD__);//запись в журнал
                header('location:'.$this->params["back_url"].'');
                exit();
            }else{
                new MsLogWrite('update',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'ошибка обновления данных...',__METHOD__);//запись в журнал
                header('location:'.$this->params["back_url"].'');
                exit();
            }
            break;
            
            //если добавили
            case "add":
            
            //для пользователей, имеющих права переданные в accessCheck - доступ будет открыт, для остальных закрыт!
            new MsLogWrite('add',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'попытка добавления данных...',__METHOD__);//запись в журнал
            if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),Moderator')){
                header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
                exit();
            };
            
            $MsDBProcess = new MsDBProcess;
            //var_dump($this->params['id']);die; table_name
            $add = $MsDBProcess->universalInsertDB($this->params['table_name'],$this->params);//универсальное свойство добавления данных
            if($add){
                //добавляем картинку
                if($this->params['table_name'] == 'author'){ //условие если добавляют автора
                    //Создаём папку для фото автора
                    $structure = './assets/media/images/'.$this->params['table_name'].'/_'.$add;// Желаемая структура папок
                    //var_dump($structure);die;
                    // Для создания вложенной структуры необходимо указать параметр
                    // $recursive в mkdir()
                    if (!mkdir($structure, 0777, true)) {
                        die('Не удалось создать директории...');
                    }
                    //$save_path = 'view/i/albums/_'.$insert.'/_ava.jpg';
                    $save_path = "assets/media/images/".$this->params['table_name']."/_".$add."/_ava.jpg";
                }else{
                    $save_path = "assets/media/images/".$this->params['table_name']."/".$add.".jpg";
                }
                $MsIMGProcess = new MsIMGProcess;
                $MsIMGProcess->cut_and_save_img_mss(600,5,$save_path);
                
                if($this->params['table_name'] == 'audio'){ //условие если добавляют аудио
                    //запись аудиофайла
                    $MsFileProcess = new MsFileProcess;
                    $save_path = "assets/media/".$this->params['table_name']."/".$add;
                    $saveFile = $MsFileProcess->save_audio($save_path,$insert);
                }
                new MsLogWrite('add',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'обновление данных прошло успешно...',__METHOD__);//запись в журнал
                //возврат на исходную страницу
                header('location:'.$this->params["back_url"].'');
                exit();
            }else{
                new MsLogWrite('add',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'ошибка добавления данных...',__METHOD__);//запись в журнал
                header('location:'.$this->params["back_url"].'');
                exit();
            }
            break;
            
            //если удаляют
            case "del":
            
            //для пользователей, имеющих права переданные в accessCheck - доступ будет открыт, для остальных закрыт!
            new MsLogWrite('del',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'попытка удаления данных...',__METHOD__);//запись в журнал
            if(!MSS::app()->accessCheck('Admin')){
                header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
                exit();
            };
            //var_dump($this->params['id']);die;
            if((int)$this->params['id'] > 0){
                //получаем и готовим переданные пути удаления файлов
                if($this->params['file_path']){
                    $file_path = base64_decode ($this->params['file_path']);
                    $file_path = unserialize($file_path);
                }else{
                    $file_path = false;
                }
                //получаем и готовим переданные пути удаления директорий
                if ($this->params['dir_path']){
                    $dir_path = base64_decode ($this->params['dir_path']);
                    $dir_path = unserialize($dir_path);
            	} else {
                    $dir_path = false;
                }
                
                $MsDBProcess = new MsDBProcess;
                //универсальное свойство удаления данных
                $del = $MsDBProcess->dropDataToID($this->params['id'],$this->params['table_name'],$file_path,$dir_path);
                if($del == true){
                    new MsLogWrite('del',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'данные успешно удалены... удаляю связанные комментарии',__METHOD__);//запись в журнал
                    //удаляем связанные комментарии
                    $MsGbook = new MsGbook;
                    $MsGbook->dropDataToSorceID($this->params['id'],$this->params['table_name']);
                    header('location:'.$this->params["back_url"].'');
                    exit(); 
                }
            }
            break;
            
            //подтверждение заказа
            case "order_confirm":
                $MsOrderProcess = new MsOrderProcess;
                $x = $MsOrderProcess->orderInsertToDB($this->params);
                if(!$x){
                    new MsLogWrite('error',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'Ошибка записи заказа пользователя',__METHOD__);//запись в журнал
                }
                header('location:'.$this->params["back_url"].'');
                exit();
            break;
            
            //перемещение заказа в "отменённые"
            case "updateorder":
                $MsOrderProcess = new MsOrderProcess;
                //var_dump($this->params['status']);die;
                //обновляем данные в таблице "заказов"
                $x = $MsOrderProcess->universalUpdateDB('orders',$this->params['id_for_update'],$this->params);
                //$x = $MsOrderProcess->universalInsertDB('aborted_order',$this->params);//универсальный метод добавления данных в БД($this->params);
                if(!$x){
                    new MsLogWrite('error',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'Ошибка обновления
                    данных заказа',__METHOD__);//запись в журнал
                }
                //инициализация сообщения о выполненном с заказом действии
                //$x = true или false
                
                $MsOrderProcess->orderInitMassage($this->params['status'], $x);
                header('location:'.$this->params["back_url"].'');
                exit();
            break;
            
            //если неудовлетворительное кол-во параметров - выводим сообщение об ошибке
            default:
            new MsLogWrite('error',MSS::app()->config->save_all,MSS::app()->config->log_files_write,'Неверное количество
             параметров в запросе или неизвестное действие!<br> Дальнейшая работа парсера невозможна.',__METHOD__);//запись в журнал
            die("<br><b>Error: ".__METHOD__.": Неверное количество параметров в запросе или неизвестное действие!</b><br> Дальнейшая работа парсера невозможна.");
        }
        
        //$action = MSS::app()->config->action[$params[3]];
        //var_dump($action);
    }
    
    private function getAction($params)
    {
        $a = array_keys($params); //извлекаем ключи массива запроса в отдельный массив
        $this->action = strtolower(array_pop($a));//action является имя последнего ключа массива (имя кнопки формы) присваиваем свойству action - значение
        array_pop($this->params); //удаляем последний элемент массива запроса т.к. он уже не понадобится (action - извлечён)
        //var_dump($this->incoming_request);
        $GLOBALS['mss_monitor'][] = __METHOD__.': определено действие: <b>'.$this->action.'</b> ...<br>';
        return $this->action;
    }
    
    private function filterPost($data)
    {
        $array = array(); 
        //var_dump($data); echo '<hr>';//die;
        foreach ($data as $key => $value){
            //echo 'Ключ:'.$key.' Значение: '.$value.'<br>';
            $value = mysqli_real_escape_string($this->mysqli,trim($value)); //ВНИМАНИЕ!!! Следить чтобы перед отправкой в БД повторно НЕ экранировать
            $array[$key] = $value;
            //echo 'Ключ:'.$key.' Значение: '.$value.'<br>';
        }
        unset($_POST);// уже не понадобится
        //var_dump($array);die;
        return $array;

    }
}
?>