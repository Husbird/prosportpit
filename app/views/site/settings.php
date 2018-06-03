<div class="row"><!-- content row-->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--content div-->
        <div class="ms_settings_content_div"><!--ms_content_forms_div-->
<style>
   .leftimg {
    float:left; /* Выравнивание по левому краю */
    margin: 7px 7px 7px 0; /* Отступы вокруг картинки */
   }
   .rightimg  {
    float: right; /* Выравнивание по правому краю  */ 
    margin: 7px 0 7px 7px; /* Отступы вокруг картинки */
   }
  </style>
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
$img_path = "assets/media/images/user/{$this->data->id}/ava.jpg";//путь к изображению
//проверяем наличие файла изображения
if (!file_exists($img_path)) {
	$img_path = 'assets/images/img/avatar_male.png';//указываем путь к "заглушке"
}
$ava_size_massiv = $imgProcess->img_out_size_mss($img_path, 140); //ресайз изображения
$h_view = $ava_size_massiv[0]; //полученная высота
$w_view = $ava_size_massiv[1]; //полученная длинна
//вывод ключевых слов\меток в формах ввода\редактирования
//$keyWords - готовый массив с ключевыми словами
//var_dump($parser); authorsData
?>

<?php
echo '<hr>';
?>
<form method="post" action="/" enctype="multipart/form-data"  role="form">
    <?php echo "<img src='/$img_path' height='$h_view' width='$w_view'class='leftimg'/>";
        echo '<p>'.$this->data->lastname.'</p>';
        echo '<p>'.$this->data->name.'</p>';
        echo '<p>'.$this->data->patronymic.'</p>';
        echo '<p class="small">Ваш статус: <b>'.MSS::$user_role.'</b></p>';
        echo '<hr>';
    ?>
    
    <div class="form-group">
        <label for="image">Сменить картинку:</label>
            <input type="file" name="image" class="btn btn-link"/>
            <i>(выберите изображение максимальным объёмом - 1 мегабайт)</i>
    </div>
    <hr/>
    <?php
        echo '<p class="small">Текущая ОС: '.MSS::$userOS.'</p>';
        echo '<p class="small">Дата регистрации: '.$timeProcess->dateFromTimestamp($this->data->date_reg).'</p>';
    ?>
    
    <input name="id" type="hidden" value="<? echo $this->data->id; ?>" />
    <input name="back_url" type="hidden" value="/Settings" />
    <input name="table_name" type="hidden" value="user" />
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
            <button name="user_settings_update" type="submit" class="btn btn-success btn-sm btn-block">Сохранить изменения</button>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <a href="javascript:history.go(-1)" mce_href="javascript:history.go(-1)" class="a_decoration_off_ms">
            <button type="button" class="btn btn-danger btn-sm btn-block">Отмена</button></a>
        </div>
    </div>
	</form>
        </div><!--.ms_content_forms_div-->
    </div><!--.content div-->
</div><!-- .content row-->