<div class="row"><!-- content row-->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--content div-->
        <div class="ms_add_content_div"><!--ms_content_forms_div-->
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
$img_path = "assets/media/images/production/{$this->data->id}.jpg";//путь к изображению
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
    <?php echo "<img src='/$img_path' height='$h_view' width='$w_view'/><br>";?>
    
    <div class="form-group">
        <label for="image">Сменить картинку:</label>
            <input type="file" name="image" class="btn btn-link"/>
    </div>
    
    <div class="form-group">
        <label for="category_id">Категория:</label>
        <select class="form-control" name="category_id">
        <?php
        foreach ($this->data->product_category_all as $key => $value){
            if($value['id'] == $this->data->category_id){
                 echo "<option selected value=".$value['id']."> ".$value['category_name']."</option>";
            }else {
                echo "<option value=".$value['id']."> ".$value['category_name']."</option>";
            }
        }
        ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="brand_id">Производитель:</label>
        <select class="form-control" name="brand_id">
        <?php
        foreach ($this->data->product_brand_all as $key => $value){
            if($value['id'] == $this->data->brand_id){
                 echo "<option selected value=".$value['id']."> ".$value['brand_name']."</option>";
            }else {
                echo "<option value=".$value['id']."> ".$value['brand_name']."</option>";
            }
        }
        ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="prod_name">Наименование товара:</label>
        <input type="text" name="prod_name" value="<?php echo $this->data->prod_name; ?>"
            placeholder="например: Mass Effect Revolution" class="form-control" />
    </div>

    <div class="form-group">
        <label for="txt_full">Описание товара:</label>
        <textarea name="txt_full" cols="50" rows="18" class="form-control"><?php echo $this->data->txt_full; ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="v">Упаковка(объём):</label>
        <input type="text" name="v" value="<?php echo $this->data->v; ?>"
            placeholder="например: 60 капсул" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="portion_quantity">Количество порций:</label>
        <input type="text" name="portion_quantity" value="<?php echo $this->data->portion_quantity; ?>"
            placeholder="например: 30" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="price">Цена:</label>
        <input type="text" name="price" value="<?php echo $this->data->price; ?>"
            placeholder="например: 1560" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="old_price">Старая (перечёркнутая) цена:</label>
        <input type="text" name="old_price" value="<?php echo $this->data->old_price; ?>"
            placeholder="например: 1600" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="sklad">Наличие:</label>
        <select class="form-control" name="sklad">
        <?php
            if($this->data->sklad == "Под заказ"){
                echo '<option selected value="Под заказ">Под заказ</option>
                      <option value="Есть в наличии">Есть в наличии</option>';
            }else {
                echo '<option value="Под заказ">Под заказ</option>
                      <option selected value="Есть в наличии">Есть в наличии</option>';
            }
        ?>
        </select>
    </div>
    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 5%; text-align: center;">
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <p><b>Новинка:</b></p>
                <?php
                    if($this->data->in_new_products == 0){
                        echo '<input type="radio" name="in_new_products" value="0" checked>Нет
                                <input type="radio" name="in_new_products" value="1">Да';
                    }else {
                        echo '<input type="radio" name="in_new_products" value="0">Нет
                                <input type="radio" name="in_new_products" value="1" checked>Да';
                    }
                ?>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <p><b>Хит продаж:</b></p>
                <?php
                    if($this->data->in_bestsellers == 0){
                        echo '<input type="radio" name="in_bestsellers" value="0" checked>Нет
                                <input type="radio" name="in_bestsellers" value="1">Да';
                    }else {
                        echo '<input type="radio" name="in_bestsellers" value="0">Нет
                                <input type="radio" name="in_bestsellers" value="1" checked>Да';
                    }
                ?>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <p><b>Один из лучших в категории:</b></p>
                <?php
                    if($this->data->in_best_in_category == 0){
                        echo '<input type="radio" name="in_best_in_category" value="0" checked>Нет
                                <input type="radio" name="in_best_in_category" value="1">Да';
                    }else {
                        echo '<input type="radio" name="in_best_in_category" value="0">Нет
                                <input type="radio" name="in_best_in_category" value="1" checked>Да';
                    }
                ?>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <p><b>Акционный:</b></p>
                <?php
                    if($this->data->in_stock == 0){
                        echo '<input type="radio" name="in_stock" value="0" checked>Нет
                                <input type="radio" name="in_stock" value="1">Да';
                    }else {
                        echo '<input type="radio" name="in_stock" value="0">Нет
                                <input type="radio" name="in_stock" value="1" checked>Да';
                    }
                ?>
            </div>
        </div>
    </div>
    
    <!-- <div class="form-group">
        <label for="article_keywords">Ключевые слова:</label>
        <input type="text" name="article_keywords" value="<?php echo $this->data->article_keywords; ?>"
           required placeholder="Ключевые слова (для поиска) через запятую" class="form-control" />
    </div> -->

    <input name="id" type="hidden" value="<? echo $this->data->id; ?>" />
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
        <p>- Все введённые данные могут быть отредактированы позднее в соответствующем разделе сайта.</p>
        <p>- При администрировании сайта с чужого компьютера - по завершении, (в целях безопастности) <b>НЕ</b> забываем нажимать на кнопку <b>"Выйти"</b>
        в главном меню.</p>
        <p style="color:gray; float: right;">... Admin <a href="https://plus.google.com/u/0/112479966809654700772/about" target="_blank" 
        title="удачной работы ! =)"><img src="/assets/media/images/main/smaylik-sport.gif" height="40" width="35"/></a></p>
    </div>
        </div><!--.ms_content_forms_div-->
    </div><!--.content div-->
</div><!-- .content row-->