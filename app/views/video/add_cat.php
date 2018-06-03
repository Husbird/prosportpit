<div class="row"><!-- content row-->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--content div-->
        <div class="ms_content_forms_div"><!--ms_content_forms_div-->
<?php
/**
 * @author Biblos
 * @copyright 2014
 * 
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
        <label for="title">Наименование категории:</label>
        <input type="text" name="title" value=""
            placeholder="Наименование категории" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="image">Загрузить картинку::</label>
        <input type="file" name="image" class="btn btn-link"/>
    </div>
    
    <div class="form-group">
        <label for="description">Описание категории:</label>
        <textarea name="description" cols="30" rows="8" class="form-control"></textarea>
    </div>
    
    <input name="table_name" type="hidden" value="<? echo 'video_cat';//$this->data->parser['table_name']; ?>">
    <input name="admin_info" type="hidden" value="<? echo $this->data->admin_info; ?>">
    <input name="date_add" type="hidden" value="<? echo $this->data->date_add; ?>">
    <input name="back_url" type="hidden" value="<? echo $_SERVER['HTTP_REFERER']; ?>">
    
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
        <p>- При отсутствии ссылки - оставляйте поле пустым;</p>
        <p>- Все введённые данные могут быть отредактированы позднее в данном разделе сайта.</p>
        <p style="color:gray; float: right;">... Moskaleny <a href="https://plus.google.com/u/0/112479966809654700772/about" target="_blank" 
        title="удачной работы с тезисами НА! =)"><img src="/assets/media/images/main/smailik_biznes.gif" height="25" width="28"/></a></p>
    </div>
        </div><!--.ms_content_forms_div-->
    </div><!--.content div-->
</div><!-- .content row-->