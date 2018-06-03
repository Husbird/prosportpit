<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--content div-->
    <div class="ms_push_div_to_center"><!--ms_content_forms_div-->
<?php
/**
 * @author Biblos
 * @copyright 2014
 * index.php (Author)
 */
// $pages = $this->data->pagesNav;
 //$data = $this->data->data;
 //$parser = $this->data->parser;
 $modelName = strtolower($this->data->parser['model']); //для формирования ссылки на view
 
 echo '<h1 style="font-size:22px;">'.$this->data->pageTitle.'</h1>';
 //var_dump($this->data);


$hfu = new MsHfu; //подключаем транслит кодер
$comments = new MsGbook;//создаём объект
//$imgProcess = new MsIMGProcess;//для использования метода ресайза изображения
$stringProcess = new MsStringProcess;
$timeProcess = new MsTimeProcess;

//готовим картинку
//$img_path = "assets/media/images/user/{$this->data->id}/ava.jpg";//путь к изображению
//проверяем наличие файла изображения
//if (!file_exists($img_path)) {
//	$img_path = 'assets/images/img/avatar_male.png';//указываем путь к "заглушке"
//}
//$ava_size_massiv = $imgProcess->img_out_size_mss($img_path, 140); //ресайз изображения
//$h_view = $ava_size_massiv[0]; //полученная высота
//$w_view = $ava_size_massiv[1]; //полученная длинна
//вывод ключевых слов\меток в формах ввода\редактирования
//$keyWords - готовый массив с ключевыми словами
//var_dump($parser); authorsData
echo $_SESSION['sendMailReport'];unset($_SESSION['sendMailReport']);
?>

<form method="post" action="/" enctype="multipart/form-data"  role="form" style="margin-top: 7%;">    
    <div class="input-group">
        <span class="input-group-addon">Ваше имя</span>
        <input type="text" name="client_name" value="<?php echo $_SESSION['contactFormClient_name']; unset($_SESSION['contactFormClient_name']); ?>" 
            required="required" placeholder="введите Ваше имя" class="form-control" />
    </div>
    <br/>
    <div class="input-group">
        <span class="input-group-addon">@</span>
        <input name="email" required="required" type="email" class="form-control" id="email" 
        value="<?php echo $_SESSION['contactFormEmail']; unset($_SESSION['contactFormEmail']); ?>" placeholder="Введите email" />
        <p style='color:red'><b><?php echo $_SESSION['emailCheckErrorMassage']; unset($_SESSION['emailCheckErrorMassage']) ?></b></p>
    </div>
        <p class="help-block">(на указанный email - мы отправим наш ответ)</p>
    
    <div class="form-group">
        <label for="user_massage">Текст Вашего вопроса\предложения:</label>
        <textarea name="user_massage" cols="50" rows="10" class="form-control"><?php echo $_SESSION['contactFormUser_massage']; unset($_SESSION['contactFormUser_massage']); ?></textarea>
        <p style='color:red'><b><?php echo $_SESSION['massageCheckErrorMassage']; unset($_SESSION['massageCheckErrorMassage']) ?></b></p>
    </div>
    
    <div class="form-group">
        <label for="cod">Введите код с картинки:</label>
        <?php
            $MsCaptcha = new MsCaptcha;
            $MsCaptcha->getSekretImg();
            //echo $MsCaptcha->cod;
            //var_dump($_SESSION['captcha_secpic']);
            //var_dump($_COOKIE["PHPSESSID"]);
        ?>
        <img src="/assets/temp/captcha/pic_<?php echo $_COOKIE["PHPSESSID"];?>.gif" />
        
        <input type="text" name="cod" size="6" 
            required="required" placeholder="код" class="form-control" />
            <p style='color:red'><b><?php echo $_SESSION['captchaCheckErrorMassage']; unset($_SESSION['captchaCheckErrorMassage']) ?></b></p>
    </div>
    
    <input name="id" type="hidden" value="<? echo $this->data->id; ?>" />
    <input name="back_url" type="hidden" value="/Contacts" />
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
            <button name="dispatch_massage" type="submit" class="btn btn-success btn-sm btn-block">Отправить сообщение</button>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <a href="javascript:history.go(-1)" mce_href="javascript:history.go(-1)" class="a_decoration_off_ms">
            <button type="button" class="btn btn-danger btn-sm btn-block">Отмена</button></a>
        </div>
    </div>
</form>
    </div><!--.ms_content_forms_div-->
</div><!--.content div-->