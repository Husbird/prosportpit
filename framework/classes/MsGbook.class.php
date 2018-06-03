<?php

/**
 * @author Biblos
 * @copyright 2014
#########################################
#	Гостевая книга						#
#	фаил - класс гостевой книги			#
#	MsGbook.class.php  		    	    #
#										#
#	DESIGNED BY M & S 					#
#	2016 год                            #
#######################################*/

class MsGbook 
{
	//настройки:
	public $tableName = 'gbook_ms';//указываем имя таблицы модуля gbook_ms (где хранятся комментарии)

    //надпись удалить в режиме админа
    public $massageBlockDelTitle = 'удалить';
    //надпись бана в режиме админа
    public $massageBlockBanTitle = 'блокировать ip и скрыть';
    //надпись разблокировать в режиме админа
    public $massageBlockNoBanTitle = 'разблокировать';

    //надпись вместо кнопки если пользователь не зарегистрирован
    public $massageForNotLoggined = '<p>Оставлять комментарии могут только зарегистрированные пользователи!</p>';
    //сообщение о бане по ip (вместо кнопки формы)
    public $banMassage = 'ВНИМАНИЕ: Ваш IP-адрес заблокирован, вы не можете оставлять сообщения';
    //сообщение об отсутствии комментариев
    public $noCommentsMassage = '
            <div class="alert alert-success" role="alert">
                <p>Станьте первым комментатором на этой странице!!!</p>
            </div>
        ';
    //режим отладки:
    public $debugMode = false; //true - включён; false - выключен
    

    public $mysqli; //метка соединения
    public $postArray = false;
    
    //для getSekretImg() свойства по умолчанию
    public $width = 160;//Ширина изображения
    public $height = 90;//Высота изображения
    public $font_size = 16;//Размер шрифта
    public $let_amount = 4;//Количество символов, которые нужно набрать
    public $fon_let_amount = 30;//Количество символов на фоне
    public $font = "framework/components/modules/gbook/fonts/europe_normal.ttf";   //Путь к шрифту (относительно корня сайта)
    public $cod;//код с картинки
    
    public $selectedComments;//массив и выбранными комментариями
    public $selectedCommentsCount;//кол-во выбранных комментариев

    function __construct(){
        $this->mysqli = MsDBConnect::getInstance()->getMysqli();
        //var_dump($this->link);
    }

    public function getSekretImg(){
        $width = $this->width;//Ширина изображения
        $height = $this->height;//Высота изображения
        $font_size = $this->font_size;//Размер шрифта
        $let_amount = $this->let_amount;//Количество символов, которые нужно набрать
        $fon_let_amount = $this->fon_let_amount;//Количество символов на фоне
        $font = $this->font;//Путь к шрифту (относительно корня сайта)

        $letters = array("2","b","e","m","s","h","u","n"); //набор символов
        $colors = array("90","110","130","150","170","190","210"); //цвета

        $src = imagecreatetruecolor($width,$height);    //создаем изображение       
        $fon = imagecolorallocate($src,255,255,255);    //создаем фон
        imagefill($src,0,0,$fon);                       //заливаем изображение фоном

        for($i=0;$i < $fon_let_amount;$i++) {         //добавляем на фон буковки
            $color = imagecolorallocatealpha($src,rand(0,255),rand(0,255),rand(0,255),100);//случайный цвет
            $letter = $letters[rand(0,sizeof($letters)-1)];//случайный символ                           
            $size = rand($font_size-2,$font_size+2);//случайный размер                                           
            imagettftext($src,$size,rand(0,45),
            rand($width*0.1,$width-$width*0.1),
            rand($height*0.2,$height),$color,$font,$letter);
        }
        //то же самое для основных букв
        for($i=0;$i < $let_amount;$i++)  {
            $color = imagecolorallocatealpha($src,$colors[rand(0,sizeof($colors)-1)],
            $colors[rand(0,sizeof($colors)-1)],
            $colors[rand(0,sizeof($colors)-1)],rand(20,40));
            $letter = $letters[rand(0,sizeof($letters)-1)];
            $size = rand($font_size*2-2,$font_size*2+2);
            $x = ($i+1)*$font_size + rand(1,5);//даем каждому символу случайное смещение
            $y = (($height*2)/3) + rand(0,5);                           
            $cod[] = $letter;//запоминаем код
            imagettftext($src,$size,rand(0,15),$x,$y,$color,$font,$letter);
        }
    imagegif($src,'assets/images/ms_gbook/cap_pic.gif');//пишем картинку в папку
    $cod = implode("",$cod);  //переводим код в строку
    $this->cod = $cod;
    $_SESSION['secpic'] = $cod; //пишем в сессию код картинки
    //return $cod = implode("",$cod);  //переводим код в строку
    }
    
    //принимаем переданные из формы данные 
    public function catchFormData($postArray = false){
        //var_dump($_SESSION['secpic']);die;
        if($postArray == false){
            die('<b>Gbook:</b>Данные из формы комментариев не переданы!');
        }
        
        //если временно забанен (неправильно ввёл картинку или обновил более 6 раз) (стоит кука бана)
        if($_COOKIE['pictureRefreshDeny']){
            //направляем на страницу ввода комментария
            $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: Пользователь забанен!';
            unset($_SESSION["pictureRefreshCount"]);//сбрасываем счётчик
            $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: счётчик обновлений сброшен...';
            $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: чишу сессию от сообщений об ошибке ...';
            unset($_SESSION['codError']);//сброс сообщения об ошибке
            $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: направляею на страницу ввода комментария...';
             header('location:'.$_SERVER['HTTP_REFERER'].'');
             exit();
        }/**
 * elseif($postArray["pictureRefresh"] == 'true'){
 *             //отмечаем факт обновления картинки
 *             $_SESSION["pictureRefreshCount"] = $_SESSION["pictureRefreshCount"] + 1;
 *              $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: Пользователь обновил картинку <b>'.$_SESSION["pictureRefreshCount"].' раз!</b>';
 *             //если картинку обновили более 6 раз подряд
 *             if($_SESSION["pictureRefreshCount"] > 6){
 *                 $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: Пользователь обновил картинку более 6 раз!';
 *                 unset($_SESSION["pictureRefreshCount"]);//сбрасываем счётчик
 *                 $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: счётчик обновлений сброшен, ставлю куку на 2 мин...';
 *                 setcookie("pictureRefreshDeny", time()+60*2, time()+60*2); //ставим куку на 2 минуты
 *             }
 *             $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: направляею на страницу ввода комментария...';
 *             //направляем на страницу ввода комментария
 *              header('location:'.$_SERVER['HTTP_REFERER'].'');
 *              exit();
 *         }
 */
            // принимаем комментарий
            //var_dump($postArray['cod']);die;
            $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: принял комментарий, обрабатываю...';
            $cod = self::checkStr($postArray['cod']);//проверяем
            //echo $cod;die;
            $cod = mb_strtolower($cod);//приводим к нижнему регистру
            //echo 'Введён код: '.$cod.'<br/>';
            //var_dump($_SESSION['secpic']);die;
            $_SESSION['message'] = self::checkStr($postArray['message']);//пишем сообщение для возможного возврата в форму в случае ошибки с кодом картинки
            $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: сверяю проверочный код...';
            
            //Если проверочный код совпал
            if($cod === $_SESSION['secpic']){
                $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: проверочный код совпал!';
                $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: сбрасываем счётчик обновлений картинки и неправильных вводов кода!';
                unset($_SESSION["pictureRefreshCount"]);//сбрасываем счётчик обновлений картинки
                //добавляем в массив пост поля которые не передаются формой (для корректной работы метода вставки)
                //var_dump(MSS::$userData);die; link
                
                /**
 * array(18) { ["id"]=> string(2) "72" ["login"]=> string(0) "" ["pass"]=> string(32) "bb8f3e82dd24423ea6358540d3651621" 
 *                 ["name"]=> string(12) "Сергей" ["patronymic"]=> string(18) "Сергеевич" ["lastname"]=> string(20) "Москаленко" 
 *                 ["phone"]=> string(0) "" ["email"]=> string(19) "ms-projects@mail.ru" ["birthday"]=> string(1) "0" ["sex"]=> string(0) "" 
 *                 ["key_user"]=> string(0) "" ["ip_reg"]=> string(9) "127.0.0.1" ["ip"]=> string(9) "127.0.0.1" ["date_reg"]=> string(10) "1454763785" 
 *                 ["date_last"]=> string(1) "0" ["activity"]=> string(4) "1127" ["hash"]=> string(32) "76dad452824335c6d9664b9e6a7297a5" 
 *                 ["adm_mss"]=> string(1) "2" }
 */
                
                $postArray['id_user'] = MSS::$userData['id'];//id комментатора
                $postArray['ip_user'] = MSS::$userData['ip'];//ip комментатора
                $postArray['datetime'] = time();//текущее время (время комментария)
                $postArray['name_src_table'] = $_SESSION['tableName'];//таблица к которой относится комментарий
                //var_dump($postArray);die;
                //$returnURI = $_SERVER['HTTP_REFERER'];//$_SESSION['returnURI'];
                //пишем комментарий в БД:
                //$comment = new gbook_ms;
                //$comment->link = $link; //метка соединения
                $this->postArray = $postArray;
                $insert = self::universalInsertDB();//универсальное свойство добавления данных
                //var_dump($insert);die;
                if (intval($insert) > 0){
                    $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: комментарий успешно записан в БД!';
                   // var_dump($_SESSION['secpic']);
                   $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: чишу сессию от проверочного кода...';
                    unset($_SESSION['secpic']);
                    //var_dump($_SESSION['secpic']);
                    
                    //var_dump($_SESSION['tableName']);
                    $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: чишу сессию от названия таблицы...';
                    unset($_SESSION['tableName']);
                    //var_dump($_SESSION['tableName']);
                     $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: чишу сессию от введённого комментария ...';
                    unset($_SESSION['message']);
                    $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: чишу сессию от сообщений об ошибке ...';
                    unset($_SESSION['codError']);//сброс сообщения об ошибке
                    
                     $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: отправляю на '.$_SERVER['HTTP_REFERER']. '...';
                    header('location:'.$_SERVER['HTTP_REFERER'].'');
                       //header('location:/'.$hfu->hfu_gen($menu_one_titles[3]).'/1/'.$menu_one_markers[3].'');
                    exit();
                }else {
                    $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: <span style="color:red;">ошибка записи комментария в БД!</span>';
                    $_SESSION["gBook_monitor"][]= '<b>catchFormData</b>: <span style="color:red;">отправляю на исходную страницу!</span>';
                     header('location:'.$_SERVER['HTTP_REFERER'].'');
                }
            }else{
                    //проверочный код не совпал!
                    $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: проверочный код <span style="color:red;">НЕ совпал!</span>';
                    //отмечаем факт обновления картинки
                    $_SESSION["pictureRefreshCount"] = $_SESSION["pictureRefreshCount"] + 1;
                    $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: Пользователь неправильно ввёл картинку + 1 = <b>'.$_SESSION["pictureRefreshCount"].' раз!</b>';
                    //если картинку обновили/ввели неправильно более 6 раз подряд
                    if($_SESSION["pictureRefreshCount"] > 6){
                        $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: Пользователь обновил\неправильно ввёл картинку более 6 раз!';
                        unset($_SESSION["pictureRefreshCount"]);//сбрасываем счётчик
                        $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: счётчик обновлений сброшен, ставлю куку на 2 мин...';
                        //ставлю БАН
                        setcookie("pictureRefreshDeny", time()+60*2, time()+60*2); //ставим куку бана на 2 минуты
                        $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: чишу сессию от сообщений об ошибке ...';
                        unset($_SESSION['codError']);//сброс сообщения об ошибке
                        $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: отправляю на '.$_SERVER['HTTP_REFERER']. '...';
                        header('location:'.$_SERVER['HTTP_REFERER'].'');
                    }else{
                        //если ещё есть попытки (т.е. нет бана)
                        $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: бана нет - считаю остаток неправильных попыток...';
                        $tryCount = 7 - $_SESSION["pictureRefreshCount"];
                        $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: осталось <b>'.$tryCount.'</b> попыток!';
                        $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: готовлю сообщение пользователю о неверном вводе кода и остатке попыток...';
                        $_SESSION['codError'] = '
                                            <div class="alert alert-warning" role="alert">
                                                <p>Неверно введён код с картинки!</p>
                                                <p>Осталось <b>'.$tryCount.'</b> попыток...</p>
                                            </div>
                        ';
                        $_SESSION['codErrorField'] = 'has-error';
                        $_SESSION["gBook_monitor"][] = '<b>catchFormData</b>: отправляю на '.$_SERVER['HTTP_REFERER']. '...';
                        header('location:'.$_SERVER['HTTP_REFERER'].'');
                        exit();
                    }
            }
    }
    
    
    public function openForm($tableName=false, $src_id=false){
            //                                             ВНИМАНИЕ !!!!!!!!!!!!!!!!!!!!!!!!!
     //                                 ОБЯЗАТЕЛЬНО ДОЛЖНЫ БЫТЬ ВЫПОЛНЕНЫ СЛЕДУЮЩИЕ УСЛОВИЯ:
     //"выше" в материнском файле подключен класс gbook_ms.class.php;
     //"выше" в материнском файле определён массив с данными пользователя $userInfo
     //а также id сущности (например статьи или товара) под которой пишем и выводим комменты ($id);
     //переменная $tableName - должна содержать название таблицы в БД к которой будет относиться коммент
     //require_once($gbookClassPath);//подключаю класс модуля
     
     $secretImg = new MsGbook;//создаём объект класса gbook_ms
     //если нет временного бана:
     if(!$_COOKIE["pictureRefreshDeny"]){
        self::getSekretImg();//методом getSekretImg() рисуем картинку с кодом; пишем ее в папку; присваеваем свойству cod - код картинки;  
     }
    //$_SESSION['secpic'] = $this->cod; //пишем в сессию код картинки
    //var_dump($_SESSION);
    $_SESSION['tableName'] = $tableName; //пишем в сессию название таблицы к которой будет принадлежать коммент
    //unset($_SESSION['tableName']);
    //echo $_SESSION['tableName'];
    if($_SESSION['tableName'] == ''){
        echo '<p><b>gbook_ms</b>: <span style="color:red;">ошибка: не задано имя таблицы ресурса, материал которой комментируется!</span></p>';die;
        
    }
    //$_SESSION['returnURI'] = $_SERVER['HTTP_REFERER']; //пишем в сессию 
    $codError = $_SESSION['codError'];
    unset($_SESSION['codError']);
    $commentsData = self::selectComments($src_id,$_SESSION['tableName']); //получаем массив данных комментариев, относящихся к текущей сущности(статьи,товара и т.п.) и имени таблицы в которой содержится сущность
    //var_dump($commentsData);//die;
    $countComments = count($commentsData);
    //var_dump($countComments);

//выводим сообщение:
echo <<< HTML
<div class="row comment-form" id="respond" style="background-color: #fff; border: 0px solid #E8E8E6; border-radius:5px;  margin: 0 auto;">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border:0px solid red; padding-bottom:15px;"> <!--общий блок с комментариями-->
<h3 style="margin-bottom:5%;">Комментарии</h3>
HTML;
//unset($_SESSION['message']);//сразу чистим сессию от сообщения (после его возврата в форму)
    if(is_array($commentsData)){
        foreach($commentsData as $key=>$value){
         //var_dump($value)  ;die;
        $id_msg =  $value['id'];
        $name =  $value['name'];
        $patronymic =  $value['patronymic'];
        $email =  $value['email'];
        $message =  nl2br($value['message']);
        $id_user_comment = $value['id_user'];
        $ip_user =  $value['ip_user'];
        $ip_ban =  $value['ip_ban'];
        $datetime = self::dateFromTimestamp($value['datetime']);
        $name_src_table =  $value['name_src_table'];
        $id_src =  $value['id_src'];
        
        $ava_img_path = "/assets/media/images/user/".$id_user_comment."/ava.jpg"; //путь к аватарке пользователя
        //проверяем есть ли у пользователя аватарка
        if(!file_exists("assets/media/images/user/".$id_user_comment."/ava.jpg")){
            $ava_img_path = "/assets/images/ms_gbook/avatar_male.png"; //путь к заглушке
        }
echo <<< HTML
<div class="row comment-form" id="respond" style="border: 0px solid #E8E8E6; margin-bottom:5%; margin-top:0%;"><!-- общий блок комментария подписи и авы-->
<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" style="border:0px solid gray;"> <!--ава-->
    <p><img src="$ava_img_path" class="img-responsive img-rounded"/></p>
</div><!--ава-->
<div class="col-lg-10 col-md-10 col-sm-8 col-xs-8" style="border:0px solid black;"> <!--блок комментария и подписи-->
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 small text-muted" style="border:0px solid blue; margin-bottom:12px;"> <!--подпись-->
        <a href='mailto:$email' target='_blank' class='small'>$name $patronymic</a> <span class='small' style='color:#999;'>@ $datetime</span>
    </div><!--подпись end-->
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <!--комментарий-->
	<p><big>$message</big></p>
    </div><!--комментарий end-->
</div><!--блок комментария и подписи end-->
</div><!-- общий блок комментария подписи и авы end-->
HTML;
                //если включен режим админа small text-muted
                if ($root == 3) {
                	//если забанен
                	if($ip_ban != ''){
                		$ipStyle = 'color:red; font-weight:bold;';
                		//ссылка разбанить
                		$actionForIdTitle = "<span class='gbook_ms_ip' style='$ipStyle'>ip: $ip_user </span><br><a href='/c_gbook_ms.php?reban_ms_gbook=$id_msg'>$massageBlockNoBanTitle</a><br>";
                	//если нет
                	}else{
                		unset($ipStyle);
                		//ссылка забанить
                		$actionForIdTitle = "<span class='gbook_ms_ip' style='$ipStyle'>ip: $ip_user </span><br><a href='/c_gbook_ms.php?ban_ms_gbook=$id_msg'>$massageBlockBanTitle</a><br>";
                	}
echo <<< HTML
<p align='right'>
	$actionForIdTitle
	<a href='/c_gbook_ms.php?del_ms_gbook=$id_msg'>$massageBlockDelTitle</a>
</p>
HTML;
                    }
        } //foreach end cap_pic
    }else{
        //выводим сообщение об отсутствии комментариев
        echo $this->noCommentsMassage;
    } //if end
echo '</div ><!--общий блок с комментариями end-->';
echo '</div ><!--row comment-form end-->';
   
    if($this->debugMode){
        echo "<h3>Редим отладки MsGbook включён:</h3>";
        echo '<p>Текущий код картинки: <b>'.$_SESSION['secpic'].'</b></p>';
        //var_dump($_COOKIE['pictureRefreshDeny']);
        echo '<p><b>Результат работы gBook_monitor: (сессия)</b></p>';
            echo '<pre>';
            print_r($_SESSION['gBook_monitor']);
            unset($_SESSION['gBook_monitor']);
            echo '</pre>';    
    }

      
    //ставим значение Action форм по умолчанию
    //$formAction = '/';
    //если жива кука с запретом обновления картинки
    if($_COOKIE["pictureRefreshDeny"]){
        $commentDeny = true;//запрещаем вывод кнопок отправки комментариев
        $bth_class = 'disabled';
        $miniBanTime = $_COOKIE["pictureRefreshDeny"] - time();//считаем сколько времени до возобновления возможности комментировать
        $miniBanMassage = '
            <div class="alert alert-danger" role="alert">
                <p>Внимание!</p>
                <p>Проверочный код был обновлён более 6 раз подряд, что может являться поведением хакерской программы...</p>
                <p>В связи с этим, возможность отправлять комментарии заблокирована на 2 минуты.</p>
                <p>Осталось:<b> '.$miniBanTime.'</b> секунд...</p>
            </div>
        ';
    }
    if($_SESSION['codErrorField']){
        $redCodeField = $_SESSION['codErrorField'];
        unset($_SESSION['codErrorField']);
    }


?>
<div class="row" style="background-color: #fff; border: 1px solid #E8E8E6; border-radius:5px; width:80%;margin: 0 auto;"><!-- gbook-->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3>Оставьте комментарий!</h3>
        </div>
        
         <?php echo $miniBanMassage; ?>   
        
     <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 text-center" style="margin: 0 auto; border: 0px solid maroon;"><!--Выводим картинку с проверочным кодом и кнопку обновления картинки -->
        <p>Проверочный код</p>
        <img src="/assets/images/ms_gbook/cap_pic.gif"/>
        <div>
            
            <script type='text/javascript'>
            //функция обновления текущей страницы
            function reloadPage(){
            window.location.reload()
            }
            </script>
            <button type="button" onclick="reloadPage()" class="btn btn-link btn-sm">Обновить картинку</button>
        </div>
        <!--кнопка обновления картинки
        <form method="POST" action="/" role="form">
            <div class="form-group">
                <input name="pictureRefresh" required type="hidden" class="form-control" value="true" />
            </div>
            <button name="gbook_add_comment" type="submit" class="btn btn-link btn-sm <?php echo $bth_class ?>">Обновить картинку</button>
        </form>
        <!--кнопка обновления картинки End-->
    </div><!--Выводим картинку с проверочным кодом и кнопку обновления картинки -->   
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center" style="padding-top: 5%;">
    <form method="POST" action="/" role="form">
        <div class="form-group">
            <label for="cod" class="h4 <?php echo $redCodeField; ?>"> </label>
            <input name="cod" class="form-control" id="cod" placeholder="Введите код с картинки" size="6" required="required" />
            <div class="help-block with-errors"><?php echo $codError; ?></div>
        </div>
    </div>
    
    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 15px;"><!--текстовое поле-->    
        <div class="form-group">
            <label for="message" class="h4">Ваш комментарий</label>
            <textarea cols="45" class="form-control" rows="8" name="message" placeholder="Введите комментарий..." required ><?= $_SESSION['message'] ?></textarea><br />
            <?= $error[2]?>
            
            <input type="hidden" name="id_src" value="<?= $src_id ?>" />
            <input type="hidden" name="name" value="<?= MSS::$userData['name']; ?>" />
            <input type="hidden" name="patronymic" value="<?= MSS::$userData['patronymic']; ?>" />
            <input type="hidden" name="email" value="<?= MSS::$userData['email']; ?>" />
        </div>
        
        <?php
            if(MSS::$user_role != 'Guest'){
                if($commentDeny == true){$bth_class = 'disabled';} //если временный бан - делаем кнопку не активной   btn-lg pull-right
                
                echo "<button name='gbook_add_comment' type='submit' 
                    class='btn btn-success btn-md pull-right $bth_class'>Готово</button>";
            }else{
                echo $this->massageForNotLoggined;
            }
        echo "
        </form>
    </div><!--текстовое поле End-->  
 </div><!-- gbook-->
            ";
    }
    
    //получаем в массиве название всех полей указанной таблицы (ИСПОЛЬЗУЕТСЯ в универсальных методах insert,update)
    public function tableColnames(){
        $tableName = $this->tableName;
        $query = mysqli_query($this->mysqli,"SHOW COLUMNS FROM $tableName") or die("mysql error");
        $count = mysqli_num_rows($query);
        $x = 0; 
        while ($x < $count){ 
            $colname = mysqli_fetch_row($query); 
             //var_dump($colname);
            $massiv[] = $colname[0]; 
            $x++; 
        } 
        return $massiv;
    }
    //добавление данных в БД
    public function universalInsertDB(){
        $tableName = $this->tableName;//получаем имя таблицы
        $colNames = self::tableColnames(); //выбираем названия всех полей таблицы
        //$id = $this->id;
        //$this->postArray = $postArray;
        foreach ($colNames as $key=>$value){
            //если элемент массива POST ключ которого соответствуюет текущему(в цикле) названию поля
            // не пуст - берём его значение и добавляем в строку запроса
            if(isset($this->postArray[$value])){
                $fields = $fields.$value.", ";
                $values = $values."'".$this->postArray[$value]."', ";
            }
        }
        $fields = substr($fields, 0, -2); //убираем запятую и пробел в конце строки
        $values = substr($values, 0, -2); //убираем запятую и пробел в конце строки
        //$set = substr($set, 0, -2); //убираем запятую и пробел в конце строки
        $sql = "INSERT INTO $tableName ($fields) VALUES ($values)";
        //var_dump($sql);die;
        $query = mysqli_query($this->mysqli,$sql);//true
        $id_last = mysqli_insert_id($this->mysqli);//возвращает ID сгенерированный при последней операции
        if($query){
            return $id_last;
        }else{
            return false;
        }
    }
    
    //получаем массив данных таблицы по $id_src ($id_src - id строки к которой привязан комментарий)
    public function selectComments($id_src, $name_src_table){
        //var_dump($name_src_table);die;
        //$misqli = $this->mysqli;
        //var_dump($this->link);
        if(!$this->mysqli){
            die("Отсутствует метка соединения с БД. Ошибка метода 127.");
        }
        $tableName = $this->tableName;//определяем имя таблицы с комментариями по умолчанию gbook_ms
        //считаем кол-во всех авторов
        $sql = "SELECT * FROM `$tableName` WHERE id_src = '$id_src' AND name_src_table = '$name_src_table'";
        //var_dump($sql);
        //$query = mysqli_query($link,$sql);//true
         $query = $this->mysqli->query($sql); // ООП запрос
        //получаем массив
		while($selectedComments = mysqli_fetch_assoc($query)){
			//данные каждой строки таблицы попадают в отдельный массив
			$massiv[] = $selectedComments;
		}
        //результат работы метода - инициализация нижеуказанных свойств и возвращение массива
        $this->selectedComments = $massiv; //массив
        $this->selectedCommentsCount = count($massiv);
        return $massiv;
    }
    
    //удаление данных по id ИСП! (ООП)
    public function dropDataToID($id){
        $id = (int)$id;
        $tableName = $this->tableName;
        $sql = "DELETE FROM $tableName WHERE id='$id'";
        $query = $this->mysqli->query($sql);//true
        if($query){
            return true;
        }else{
            return false;
        }
    }
    
    //удаление данных по id и имени таблицы связанной с комментарием ИСП! (ООП)
    //используется после удаления из таблицы строки к которой принадлежат коммениарии,
    //с целью удалить, связанные с удалённой строкой комментарии.
    public function dropDataToSorceID($id_src,$name_src_table){
        $id = (int)$id;
        $tableName = $this->tableName;
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b> считаю связанные комментарии ... ';
        $sql = "SELECT id FROM `$tableName` WHERE id_src='$id_src' AND name_src_table='$name_src_table'";
        $query = $this->mysqli->query($sql);//true OOП
        $positions = mysqli_num_rows($query); //кол-во всех записей
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b> всего найдено - '.$positions.' ';
        
        if($positions > 0){
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b> удаляю связанные с '.$name_src_table.' id: '.$id_src.' ...';
            $sql = "DELETE FROM $tableName WHERE id_src='$id_src' AND name_src_table='$name_src_table'";
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b> сформирован SQL запрос: '.$sql;
            $query = $this->mysqli->query($sql);//true
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b> результат: '.$query.'';
            if($query){
                return true;
            }else{
                return false;
            }
        }else{
            $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b> связанные комментарии не обнаружены!';
            return true;
        }
    }
    
    //обработка строковых данных получяемых из форм ввода $mysqli  link
    public function checkStr($string){
        $str = mysqli_real_escape_string($this->mysqli,strip_tags(trim($string)));
        $_SESSION['mss_monitor'][] = '<b>MsAuthoriz:</b> checkStr(): проверяю строку: '.$str;
        return ($str);
    }
    
    //форматируем timestamp в дату формата: 7 Февраля 2016 г.
    public function dateFromTimestamp($timestamp){
        if (!$timestamp or $timestamp == '') {
            return 'нет данных'; die('Ошибка: dateFromTimestamp()');
        }
        $prevDate = date("j, n, Y, H:i:s",$timestamp);
        $dateMassiv = explode(",", $prevDate);
        $date = $dateMassiv[0]; //дата
        $prevMonth = (int)$dateMassiv[1]; //порядковый номер месяца
        $month = array ("нулевой","Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", 
                        "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
        $prevYear = $dateMassiv[2];
        $time = $dateMassiv[3];
        $normalDate = $date." ".$month[$prevMonth]." ".$prevYear." г. ".$time;
        return $normalDate;
    }
}
?>