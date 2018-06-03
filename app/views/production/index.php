<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!-- контент (средний блок)-->

<?php
/**
 * @author Biblos
 * @copyright 2014
 * index.php (Production)
 */
 //var_dump($this->data);
//$pages = $this->data->pagesNav;//массив ссылок для постраничной навигации (для виджета Pagination)
$data = $this->data->data;//данные из БД
$parser = $this->data->parser;//поарметры из парсера


$modelName = strtolower($this->data->parser['model']); //для формирования ссылки на view

$timeProcess = new MsTimeProcess;
$stringProcess = new MsStringProcess;
$imgProcess = new MsIMGProcess;
$hfu = new MsHfu;
$comments = new MsGbook;//создаём объект
//подключаем поиск по ключевому слову
$MsSearchKWord = new MsSearchKWord($tableName = strtolower($this->data->parser['model']), strtolower($this->data->parser['model']), 
                                        $this->data->parser['action_atribute'] ,$this->data->parser['translit']);
 
//$modelName = strtolower($this->data->parser['model']); //для формирования ссылки на view
 //Выводим корзину покупок:
$MsBasket = new MsBasket;
$MsBasket->echoBasket();
//выводим сообщение о результате обработки заказа
$MsOrderProcess = new MsOrderProcess;
$MsOrderProcess->orderConfirmMassage();//вывод сообщения пользователю о результате обработки его заказа

 echo '<h1 style="font-size:17px; display:inline-block;">'.$this->data->pageTitle.'</h1>';
 if($this->data->pageH2Tite){
     echo '&nbsp<span class="glyphicon glyphicon-arrow-right"></span>&nbsp<h2 style="font-size:17px; display:inline-block;">'.$this->data->pageH2Tite.'</h2>';   
 }
 if($this->data->pageH3Tite){
    echo '&nbsp<span class="glyphicon glyphicon-arrow-right"></span>&nbsp<h3 style="font-size:17px; display:inline-block;">'.$this->data->pageH3Tite.'</h3>';   
 }
  //var_dump($data);
 //кнопка "ДОБАВИТЬ"
if($this->data->addNewBtn){
print <<<HERE
   <p><a href="/admin/{$this->data->parser['model']}/add" class="a_decoration_off_ms">
   <button type="button" class="btn btn-primary btn-lg btn-block">Добавить новый продукт!
   </button></a></p>
HERE;
}
//навигация страниц
Pagination::run($this->data->pagesNav);
$menu = new NavigationWG;
//выводим и прячем метадискрипшн
print <<< HERE
<p style="display:none">{$this->data->meta_description}</p>
HERE;

//блок горизонтального меню
echo '<div class="hidden-lg hidden-md col-sm-12 col-xs-12">';
$menu->runGorMenuOne($this->data->parser['category_id'],$this->data->parser['sub_category_id'], $this->data->parser['category_key']);
echo '</div>';//блок горизонтального меню END
//var_dump($this->data->parser['category_key']);
echo '<div class="row" id="ms_production_main_block">'; //основной блок
    //var_dump($diaryPageData[$i]);
    $iterationNum = 0;
    foreach ($data as $key => $value){
       //var_dump($data);die;
    $id = $value['id'];
    $brand_name = $value['brand_name'];
    $country = $value['country'];
    $prod_name = $value['prod_name'];
    $category = $value['category_name'];
    $txt_full = $stringProcess->cutText_mss(nl2br($value['txt_full']),400);
    $price = $value['price'].'p.';
    $old_price = $value['old_price'];
    $v = $value['v'];
    $status = $value['status'];
    $sklad = $value['sklad'];
    $production_keywords = $value['production_keywords'];
    $view = intval($value['view']);
    
    $prod_name_titleTranslit = $hfu->hfu_gen($prod_name);//для ЧПУ
    
    //считаем кол-во комментариев у записи:
    $comments->selectComments($id, $parser['table_name']);//отбираем соответствующие комментарии
    $commentsCall = $comments->selectedCommentsCount;//получаем кол-во соответсвующих комментариев
    
    //готовим к выводу изображение
    $img_path = "assets/media/images/production/$id.jpg";//путь к изображению
    
    //проверяем наличие файла изображения
	if (!file_exists($img_path)) {
		$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
	}
    $max_scale = 140;//максимальный размер наибольшей стороны изображения
    $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, $max_scale); //ресайз изображения
    $h_view = $ava_size_massiv[0]; //полученная высота
    $w_view = $ava_size_massiv[1]; //полученная длинна
    
    
    //var_dump($sourceInfo);die;
$iterationNum = ++$iterationNum;
if(($iterationNum == 3) OR ($iterationNum == 6) OR ($iterationNum == 9)){
    $clearfix = '<div class="clearfix"></div>';
}else{
    $clearfix = '';
}
print <<<HERE
<script type="text/javascript">
jQuery( document ).ready(function() {
    $("#btn_$id").click(
		function(){
			sendAjaxForm('order_quick_block_$id', 'ajax_quick_order_confirm_$id', '/action_ajax_form.php');
			return false; 
		}
	);
});
</script>

<script type="text/javascript">
//активируем маску для поля ввода номера телефона
jQuery(function($){
   $("#phone_$id").mask("+38 (999) 999-99-99");
});
</script>

    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="ms_one_product_block"> <!-- ms_one_product_block -->
        <section>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="ms_product_one_block_border"> <!-- ms_product_one_block_border -->
            </div> <!-- ms_product_one_block_border -->
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <!-- name -->
                <hgroup>
                    <h3 style="font-size:19px;"><a href="/$prod_name_titleTranslit/$modelName/v/$id">$prod_name</a></h3>
                    <h5>$v</h5>
                    <h5>$brand_name <small>($country)</small></h5>
                    <h5><small>Категория: </small>$category</h5>
                </hgroup>
            </div><!--.name -->
        
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <!-- img -->
                <p><a href="/$prod_name_titleTranslit/$modelName/v/$id" title="Подробнее o $prod_name ...">
                        <figure>
                            <img src="/$img_path" class="img-responsive center-block img-rounded prod_img_i" style="width: 40%;" />
                            <figcaption>
                                <span style="display:none;">
                                    Предлагаем $prod_name спортивное питание категории $category, производства $brand_name ($country)
                                </span>
                            </figcaption>
                        </figure>
                    </a>
                </p>
                <p><span class="ms_old_price">$old_price</span></p>
                <p>Цена: <span class="ms_price">$price</span></p>
                <p><a href="/addToBasket/$id" rel="nofollow"><button class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-ok"></span> Добавить в корзину</button></a></p>
                
                <p><a href="#" rel="nofollow" onclick="diplay_hide('#order_quick_block_$id');return false;"><button class="btn btn-success btn-sm">Заказать одной кнопкой</button></a></p>
                    <div id="order_quick_block_$id" style="display: none; margin-bottom:10px;">
                        
                        
                        <form method="post" id="ajax_quick_order_confirm_$id" action="" role="form">
                            <div class="form-group">
                                <label for="client_name">Введите имя:</label>
                                <input type="text" name="client_name" value="" required
                                    placeholder="введите имя" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="telephone_num">Номер телефона:</label>
                                <input type="text" name="telephone_num" value="" required
                                    placeholder="" id="phone_$id" class="form-control" />
                            </div>
                            <input name="product_id" type="hidden" value="$id" />
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:;"">
                                    <input type="button" id="btn_$id" class="btn btn-success btn-sm" value="Готово" />
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:left; background-color:#fff; padding-top:2%">
                                    <a href="#" rel="nofollow" onclick="diplay_hide('#order_quick_block_$id');return false;">
                                    <span class="glyphicon glyphicon-remove-circle"></span> Отмена</a>
                                </div>
                            </div>
                        </form>
                        
                    </div> <!-- order_quick_block END -->
                    <div id="result_form_$id" style="font-size:10px"></div>

                <div class="small text-muted">
                    <p>$sklad</p>
                    <p>Комментариев: $commentsCall</p>
                    <p>[Просмотров:$view]</p>
                   <!-- $iterationNum -->
                </div>
                
            </div><!--.img -->
            
            <!-- <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <p><big>$txt_full</big></p>
                <hr>
                <p class="text-right"><a href="/$article_titleTranslit/$modelName/v/$id" class="btn btn-primary btn-large" ><big>Подробнее...</big></a></p>
            </div> --> <!--.text -->
        </section>
    </div> <!-- ms_one_product_block -->
$clearfix
HERE;
    }
echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="ms_product_one_block_border"> <!-- ms_product_one_block_border -->
      </div> <!-- ms_product_one_block_border -->';
echo '</div>'; //основной блок END
//навигация страниц
Pagination::run($this->data->pagesNav);

 //кнопка "ДОБАВИТЬ"
if($this->data->addNewBtn){
print <<<HERE
   <p><a href="/admin/{$this->data->parser['model']}/add" class="a_decoration_off_ms">
   <button type="button" class="btn btn-primary btn-lg btn-block">Добавить новый продукт!
   </button></a></p>
HERE;
}
?>


                        
</div><!-- контент (средний блок) END -->

<!--<div class="col-lg-1 col-md-1 hidden-sm hidden-xs"> (пустой блок левый)
</div> -->