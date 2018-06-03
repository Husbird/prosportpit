<div class="row"><!-- content row-->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--content div-->
        <div class="ms_content_forms_div"><!--ms_content_forms_div-->
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
 
 echo '<h1>'.$this->data->pageTitle.'</h1>';
 //var_dump($this->data);


$hfu = new MsHfu; //подключаем транслит кодер
$comments = new MsGbook;//создаём объект
$imgProcess = new MsIMGProcess;//для использования метода ресайза изображения
$stringProcess = new MsStringProcess;
$timeProcess = new MsTimeProcess;

//готовим картинку
$img_path = "assets/media/images/video/{$this->data->id}.jpg";//путь к изображению
//проверяем наличие файла изображения
if (!file_exists($img_path)) {
	$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
}
$ava_size_massiv = $imgProcess->img_out_size_mss($img_path, 140); //ресайз изображения
$h_view = $ava_size_massiv[0]; //полученная высота
$w_view = $ava_size_massiv[1]; //полученная длинна
//вывод ключевых слов\меток в формах ввода\редактирования
//$keyWords - готовый массив с ключевыми словами
//var_dump($parser); authorsData
?>

<form method="post" action="/" enctype="multipart/form-data"  role="form">
    <?php echo "<p><img src='/$img_path' height='$h_view' width='$w_view'/></p>";?>
    <div class="form-group">
        <label for="video_name">Название видеозаписи:</label>
        <input type="text" name="video_name" value="<?php echo $this->data->video_name; ?>"
            placeholder="Название видеозаписи" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="file_adress">Ссылка на видеозапись:</label>
        <textarea name="file_adress" cols="50" rows="3" class="form-control" ><?php echo $this->data->file_adress; ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="video_comment">Комментарий к видеозаписи:</label>
        <textarea name="video_comment" cols="50" rows="5" class="form-control"><?php echo $this->data->video_comment; ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="image">Сменить скриншот:</label>
        <input type="file" name="image" class="btn btn-link"/>
    </div>
    
    <div class="form-group">
        <p><b>Контент для ограниченного доступа:</b></p>
        <?php
            if($this->data->access_level == ''){
                echo '<p><input type="radio" name="access_level" value="closed">Да</p>
                <input type="radio" name="access_level" value="" checked>Нет</p>';
            }elseif ($this->data->access_level == 'closed'){
                echo '<p><input type="radio" name="access_level" value="closed" checked>Да</p>
                <input type="radio" name="access_level" value="">Нет</p>';
            }
        ?>
    </div>
   
    <div class="form-group">
        <label for="video_keywords">Ключевые слова:</label>
        <input type="text" name="video_keywords" value="<?php echo $this->data->video_keywords; ?>"
           required placeholder="Ключевые слова (для поиска) через запятую" class="form-control" />
    </div>
        <!--<input type="text" name="thesis_link" value="<?php// echo $this->data->thesis_link; ?>" placeholder="ссылка (если есть)" size="30">-->
        <!--<a href="<?php //echo $thesisDataMassiv[0]['thesis_link'] ?>" target="_blank" title="Переход по ссылке"><img src="/view/i/main/net.png" height="25" width="25"/></a>-->
        
        <!--<input type="text" name="thesis_keywords" value="<?php //echo $thesisDataMassiv[0]['thesis_keywords'] ?>" required placeholder="Ключевые слова (для поиска) через запятую" size="50">-->
        <p>Рекомендуемые ключевые слова:</p>
<?php
        //вывожу перечень ключевых слов
        $stringProcess->echoKeyWords($this->data->allKeyWords);
?>
    <input name="id" type="hidden" value="<? echo $this->data->id; ?>" />
    <input name="edit_info" type="hidden" value="<? echo $this->data->edit_info; ?>" />
    <input name="date_edit" type="hidden" value="<? echo $this->data->date_edit; ?>" />
    <input name="back_url" type="hidden" value="<? echo $_SERVER['HTTP_REFERER']; ?>" />
    <input name="table_name" type="hidden" value="<? echo $this->data->parser['table_name']; ?>" />
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
            <button name="update" type="submit" class="btn btn-success btn-lg btn-block">Сохранить</button>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <a href="javascript:history.go(-1)" mce_href="javascript:history.go(-1)" class="a_decoration_off_ms">
            <button type="button" class="btn btn-danger btn-lg btn-block">Отмена</button></a>
        </div>
    </div>
	</form>

    <div class="alert alert-warning" role="alert"style="margin-top: 5%; padding-bottom: 6%;">
        <p>Внимание!</p>
        <p>- При отсутствии ссылки - оставляйте поле пустым;</p>
        <p>- При вводе ключевых слов (меток) старайтесь выбирать их из уже имеющихся (рекомендуемых).
        Только в случае необходимости - добавляйте новую (свою) метку. Такой подход, будет способствовать повышению удобства использования
        функции поиска в текущем разделе;</p>
        <p>- Все введённые данные могут быть отредактированы позднее в данном разделе сайта.</p>
        <p style="color:gray; float: right;">... Moskaleny <a href="https://plus.google.com/u/0/112479966809654700772/about" target="_blank" 
        title="удачной работы с тезисами НА! =)"><img src="/assets/media/images/main/smailik_biznes.gif" height="25" width="28"/></a></p>
    </div>
        </div><!--.ms_content_forms_div-->
    </div><!--.content div-->
</div><!-- .content row-->