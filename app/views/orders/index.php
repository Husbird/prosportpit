<div class="col-lg-2 col-md-2 hidden-sm hidden-xs"><!-- пустой div-->
</div>

<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12"><!-- контент (средний блок)-->

<!-- Скрипт плавного открытия и закрытия блока -->
<script type="text/javascript"> 
    function diplay_hide (blockId){
        
        if ($(blockId).css('display') == 'none'){ 
                $(blockId).animate({height: 'show'}, 500); 
            } 
        else{     
                $(blockId).animate({height: 'hide'}, 500);
            }
    } 
</script>

<?php
/**
 * @author Biblos
 * @copyright 2014
 * index.php (Author)
 */
// $pages = $this->data->pagesNav;
 $data = $this->data->data;
 $parser = $this->data->parser;
 $modelName = strtolower($this->data->parser['model']); //для формирования ссылки на view
 
$hfu = new MsHfu; //подключаем транслит кодер
$comments = new MsGbook;//создаём объект
$imgProcess = new MsIMGProcess;//для использования метода ресайза изображения
$stringProcess = new MsStringProcess;
$timeProcess = new MsTimeProcess;
$MsDBProcess = new MsDBProcess;
$MsOrderProcess = new MsOrderProcess;
 
 echo '<h1>'.$this->data->pageTitle.'</h1>';
//выводим сообщения о переносе заказа (если действие совершалось) 
$MsOrderProcess->orderConfirmMassage();
//навигация страниц
Pagination::run($this->data->pagesNav);

//echo '<table class="table">';
foreach($data as $key => $value){
    $orderNum = ++$key;
    $date = $timeProcess->dateFromTimestamp($value['order_date']);
    
    //получаем массив с id заказанных товаров
    $productIdArray = explode('|',$value['products_id']);
    array_shift($productIdArray); //удаляем первы пустой элемент массива
    //var_dump($productIdArray);
    
print <<<HERE
<table class="table table-hover">

    <thead>
    <tr class="info">
        <th>
            Заказ №$orderNum 
        </th>
        <th>
            от $date
        </th>
    </tr>
    </thead>
    
    <tbody>
HERE;
    //если указана дата продажи - выводим её
    if($value['status'] == 1){
        $sold_date = $timeProcess->dateFromTimestamp($value['sold_date']);
print <<<HERE
    <tr class="warning">
            <td>
                Дата продажи:
            </td>
            <td>
                $sold_date
            </td>
        </tr>
HERE;
    }
    
    //если указана дата отмены заказа - выводим её
    if($value['status'] == 2){
        $abort_date = $timeProcess->dateFromTimestamp($value['abort_date']);
print <<<HERE
    <tr class="danger">
            <td>
                Дата отмены:
            </td>
            <td>
                $abort_date
            </td>
        </tr>
HERE;
    }

print <<< HERE
    <tr class="success">
        <td style='width:20%'>
            Имя:
        </td>
        <td>
            {$value[client_name]}
        </td>
    </tr>
    
    <tr class="success">
        <td>Номер телефона:
        </td>
        <td>{$value[telephone_num]}
        </td>
    </tr>
    
    <tr class="success">
        <td>E-mail:
        </td>
        <td>{$value[email]}
        </td>
    </tr>
    
    <tr class="success">
        <td>Адрес доставки:
        </td>
        <td>{$value[address]}
        </td>
    </tr>
    
    <tr class="warning">
        <td>Заметка заказчика:
        </td>
        <td>{$value[extra]}
        </td>
    </tr>
    
    <tr class="success">
        <td>Код заказа:
        </td>
        <td>{$value[order_code]}
        </td>
    </tr>
    
    <tr class="success">
        <td>
            Товары:
        </td>
        <td>
HERE;

$back_url = $_SERVER['REQUEST_URI'];
$currentTime = time();
$totalSum = 0;
foreach($productIdArray as $key2 => $value2){
    $productData = $MsDBProcess->productSingleSelect($value2);
    $totalSum = $productData['price'] + $totalSum;
    
    $productNameTranslit = $hfu->hfu_gen($productData['prod_name']);
    echo "<a href='/$productNameTranslit/production/v/{$productData['id']}' title='
    
            {$productData['category_name']} {$productData['brand_name']} {$productData['price']} pуб.
            
            ' target='_blank'>{$productData['prod_name']}</a> (<small>{$productData['v']}</small>), ";
}
            
print <<< HERE
        </td>
    </tr>
    
    <tr class="success">
        <td>
            На сумму:
        </td>
        <td>
            <b>$totalSum руб.</b>
        </td>
    </tr>
HERE;


if($value['extra_admin']){
print <<<HERE
    <tr class="danger">
            <td>
                Заметка админа:
            </td>
            <td>
                {$value[extra_admin]}
            </td>
        </tr>
HERE;

}
    //если статус - текущие заказы - выводим кнопки
    if($value['status'] == 0){
print <<< HERE
    <tr class="success">
        <td style='width:20%'>
            <form method="post" action="/" enctype="multipart/form-data"  role="form">
                <input name="id_for_update" type="hidden" value="{$value[id]}" />
                <input name="sold_date" type="hidden" value="$currentTime" />
                <input name="status" type="hidden" value="1" />
                <input name="back_url" type="hidden" value="$back_url" />
                <button name="updateOrder" type="submit" class="btn btn-success btn-sm btn-block">Продано!</button>
            </form>
        </td>
        <td style="padding: 1% 5% 0% 60%;">
            
                <a href="#" onclick="diplay_hide('#order_form_block_{$value[id]}');return false;"><button class="btn btn-danger btn-sm btn-block">Отменён</button></a>
                
                <div id="order_form_block_{$value[id]}" style="display: none;">
                <form method="post" action="/" enctype="multipart/form-data"  role="form">
                
                    <input name="id_for_update" type="hidden" value="{$value[id]}" />
                    <input name="abort_date" type="hidden" value="$currentTime" />
                    <input name="status" type="hidden" value="2" />
                    <input name="back_url" type="hidden" value="$back_url" />
                    <div class="form-group">
                        <label for="extra_admin">Причина отмены:</label>
                        <textarea name="extra_admin" required cols="15" rows="3" class="form-control"></textarea>
                    </div>
                    <button name="updateOrder" type="submit" class="btn btn-danger btn-sm btn-block">Отправить в отменённые</button>
                </form>
                </div>
        </td>
    </tr>
HERE;
    }
print <<< HERE
    </tbody>
</table>
HERE;

                
}
//навигация страниц
Pagination::run($this->data->pagesNav);
?>
<div class="col-lg-2 col-md-2 hidden-sm hidden-xs"><!-- пустой div-->
</div>