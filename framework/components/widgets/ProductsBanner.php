<?php
/**
* Виджет: баннер с продукцией*/
class ProductsBanner extends MsDBProcess

{
    public $productsDataArray = false; //массив с данными товаров
    //public $mysqli;
 
    function __construct(){
            $this->mysqli = MsDBConnect::getInstance()->getMysqli();
            //$MsDBProcess = new MsDBProcess;
            $this->productsDataArray = $this->ProductsBannerDataSelect('t1.in_best_in_category = "1"');
            //$x = $this->ProductsBannerDataSelect('t1.in_best_in_category = "1"');
            //echo $this->productsDataArray;
            return true;
    }
    
    //выводим блок с сылками страниц
    public function bestInCategoryRun(){
//var_dump($this->productsDataArray);die;
        $countArray = count($this->productsDataArray);
        if($countArray >= 1){
            $hfu = new MsHfu;//переводчик в латиницу (объект)
            echo '<div class="col-lg-12 col-md-12 hidden-sm hidden-xs bestInCategory_ms" style="padding:5px;">';
            echo '<div style="color:white;background-color: red; padding-top:8px; padding-bottom:8px; padding-left:0px; 
                                margin-bottom:20px;text-align: center; font-size: 14px; border-top-left-radius: 20px; border-bottom-right-radius: 20px; 
                                font-family: Helvetica,Geneva,Georgia,"Times New Roman",sans-serif;">
                    PROSPORTPIT рекомендует:</div>';
            foreach($this->productsDataArray as $key => $value){
                $prod_name_titleTranslit = $hfu->hfu_gen($value['prod_name']);
                $img_path = "assets/media/images/production/".$value['id'].".jpg";//путь к изображению
print <<<HERE
<div style="text-align:center; background-color: #fff; border:0px solid #E6E6E6; border-radius:3px; margin-bottom:5px; 
            font-family: Helvetica,Geneva,Georgia,"Times New Roman",sans-serif;">

    <span><a href="/$prod_name_titleTranslit/production/v/{$value['id']}" title="Подробнее o {$value['prod_name']} ...">{$value['prod_name']}</a></span>
    <p class="small text-muted">{$value['category_name']}</p>
    <p><a href="/$prod_name_titleTranslit/production/v/{$value['id']}" title="Подробнее o {$value['prod_name']} ...">
            <figure>
                <img src="/$img_path" class="img-responsive center-block img-rounded prod_img_i" style="width: 40%;" />
                <figcaption>
                    <span style="display:none;">
                        Предлагаем {$value['prod_name']} лидера категории {$value['category_name']}, производства {$value['brand_name']} ($country)
                    </span>
                </figcaption>
            </figure>
        </a>
    </p>
    <p class="small text-muted">{$value['v']}</p>
</div>
<hr>
HERE;
            }
             //
            echo '</div>
                <div class="clearfix"></div>';  
        }else{
            return '<b>'.__METHOD__.'</b> Ошибка! Не удалось получить необходимые параметры!<br>';
        }
    }
}

?>