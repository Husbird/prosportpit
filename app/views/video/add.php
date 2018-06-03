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
?>

<form method="post" action="/" enctype="multipart/form-data"  role="form">

    <div class="form-group">
        <label for="category_id">Выберите категорию:</label>
        <select class="form-control" name="category_id">
        <?php
        foreach ($this->data->video_catData as $key => $value){
                echo "<option value=".$value['id']."> ".$value['title']."</option>";
        }
        ?>
        </select>
    </div>

    <div class="form-group">
        <label for="video_name">Название видеозаписи:</label>
        <input type="text" name="video_name" value=""
            placeholder="Название видеозаписи" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="file_adress">Ссылка на видеозапись:</label>
        <textarea name="file_adress" cols="50" rows="3" class="form-control" placeholder="Введите ссылку или html код видео" ></textarea>
    </div>
    
    <div class="form-group">
        <label for="video_comment">Комментарий к видеозаписи:</label>
        <textarea name="video_comment" cols="50" rows="5" class="form-control"></textarea>
    </div>
    
    <div class="form-group">
        <label for="image">Загрузить скриншот:</label>
        <input type="file" name="image" class="btn btn-link"/>
    </div>
    
    <div class="form-group">
        <p><b>Контент для ограниченного доступа:</b></p>
        <p><input type="radio" name="access_level" value="closed" checked>Да</p>
        <p><input type="radio" name="access_level" value="" checked>Нет</p>';
    </div>
    
    <div class="form-group">
        <label for="video_keywords">Ключевые слова:</label>
        <input type="text" name="video_keywords" value=""
           required placeholder="Ключевые слова (для поиска) через запятую" class="form-control" />
    </div>
        <p>Рекомендуемые ключевые слова:</p>
<?php
        //вывожу перечень ключевых слов
        $stringProcess->echoKeyWords($this->data->allKeyWords);
?>
    <input name="admin_info" type="hidden" value="<? echo $this->data->admin_info; ?>">
    <input name="date_add" type="hidden" value="<? echo $this->data->date_add; ?>">
    <input name="back_url" type="hidden" value="<? echo $_SERVER['HTTP_REFERER']; ?>">
    <input name="table_name" type="hidden" value="<? echo $this->data->parser['table_name']; ?>">
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
            <button name="add" type="submit" class="btn btn-success btn-lg btn-block">Сохранить</button>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <a href="javascript:history.go(-1)" mce_href="javascript:history.go(-1)" class="a_decoration_off_ms">
            <button type="button" class="btn btn-danger btn-lg btn-block">Отмена</button></a>
        </div>
    </div>
	</form>

    <div class="alert alert-warning" role="alert"style="margin-top: 5%; padding-bottom: 6%;">
        <p>Внимание!</p>
        <p>- Для корректного вывода видео на странице, ссылка на видеофаил должна быть строго определённого типа.
        Пример: <i>https://youtu.be/LHEBO29wNfA</i>;</p>
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