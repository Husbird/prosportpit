<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries КОСТЫЛИ ДЛЯ IE-->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <meta charset="utf-8" />
	<title><?php echo $this->data->title;?></title>
	<meta name="keywords" content="<?php echo $this->data->meta_keywords;?>" />
	<meta name="description" content="<?php echo $this->data->meta_description;?>" />
    <meta name="google-site-verification" content="7JlWV9sU4YJKS_ciJGYDqnntKMY6Sh7zfUgHdPGCjJk" />
    <link rel="icon" href="/mss.ico" type="ico"/>
    <!--стили bootstrap:-->                                   
    <link href="<?php echo MSS::app()->config->bootstrap_path;?>css/bootstrap.css" rel="stylesheet"/>
    <!--собственный фаил стилей:-->
    <link href="<?php echo MSS::app()->config->css_path;?>ms_style.css" rel="stylesheet"/>

     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="<?php echo MSS::app()->config->bootstrap_path;?>js/bootstrap.js"></script>
    <script src="<?php echo MSS::app()->config->scripts_path;?>maskedinput.js"></script><!--маски для полей input форм (маска для поля ввода № телефона)-->
    <script src="https://vk.com/js/api/openapi.js?135" type="text/javascript"></script><!--интерфейс VK (вконтакте)-->
    <!-- инициализация VK.init (вконтакте) для блока комментариев START-->
    <script type="text/javascript">
      VK.init({apiId: 5680768, onlyWidgets: true});
    </script><!-- инициализация VK.init (вконтакте) для блока комментариев END-->
    
    <script src="/ajax.js"></script><!-- функции отправки\приёма JSON запросов -->
</head>

<body>

<?php
//скрипты информеров счётчиков посещений
include_once("inc_block/counter_code.php");
?>

<!---------------------------------------------------------------------- header ---------------------------------------------------------------->
<header>
    <img src="/assets/media/images/main/header_prosportpit2.jpg" class="img-responsive" style="width:100%;"/>
</header>
<!-- .-------------------------------------------------------------------- header END ---------------------------------------------------------->













 <!-------------------------------------------------------------- Основной контент страницы ---------------------------------------------------->
 
 <!--навигация-->
<nav class="navbar navbar-inverse background-navbar-inverse"  role="navigation nav-stacked"> <!-- navbar-fixed-top -->
    <div class="container-fluid">
        <!-- Название компании и кнопка, которая отображается для мобильных устройств группируются для лучшего отображения при свертывание -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">prosportpit.com</a>
        </div>
        
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="<?php echo $this->data->activeHome;?>"><a href="/">Главная</a></li>
                
                <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Спортивное питание<b class="caret"></b></a>
                <ul class="dropdown-menu">
                <?php
                
                    $menu = new NavigationWG;
                    $menu->runVertMenuOneDropdownLiMobilVersion($this->data->parser['category_id'],$this->data->parser['category_key']);
                    //echo '<li><a href="/'.$translitTitle.'/article/i/1/'.$value['id'].'">'.$value['title'].'</a></li>';
                    if(MSS::app()->accessCheck('Admin,Moderator')){
                        echo '<li class="divider"></li>';
                        echo '<li><a href="/info/production/add_cat" rel="nofollow">Добавить категорию</a></li>';
                    }
                
                ?>
                </ul>
                </li>
                
                <?php
                    if(!MSS::app()->accessCheck('Guest')){
                        echo '<li class="'.$this->data->activeSite.'"><a href="/Settings" rel="nofollow">Настройки</a></li>';
                    }
                    
                    if(MSS::app()->accessCheck('Admin,Moderator')){
                          echo '<li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" rel="nofollow">Управление<b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/admin/orders/i/1/current" rel="nofollow">Текущие заказы</a></li>
                                        <li><a href="/admin/orders/i/1/sold" rel="nofollow">Реализованные заказы</a></li>
                                        <li><a href="/admin/orders/i/1/aborted" rel="nofollow">Отменённые заказы</a></li>
                                    </ul>
                                </li>
                        ';
                    }
                ?>
                <li class="<?php echo $this->data->activeContacts;?>"><a href="/contacts">Контакты</a></li>
            </ul>
        </div>
    </div>
</nav><!--навигация end-->

<?php
//подключаем виджет хлебные крошки
$breadcumb = new Breadcrumb;
$breadcumb->run();
?>
 
<main>
<div class="container-fluid">
<div class="row"  style="">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

    <div style="text-align: right; margin: 2px;"><!-- приветствие -->
        <p><?php if(MSS::$userData['name']){
                //$imgProcess = new MsIMGProcess;//для использования метода ресайза изображения
                //готовим картинку
                //$img_path = "assets/media/images/user/".MSS::$userData['id']."/ava.jpg";//путь к изображению
                //проверяем наличие файла изображения
                //if (!file_exists($img_path)) {
                //	$img_path = 'assets/images/img/avatar_male.png';//указываем путь к "заглушке"
                //}
                //$ava_size_massiv = $imgProcess->img_out_size_mss($img_path, 80); //ресайз изображения
                //$h_view = $ava_size_massiv[0]; //полученная высота
                //$w_view = $ava_size_massiv[1]; //полученная длинна
                echo 'Здравствуйте '.MSS::$userData['name'].'! <span class="glyphicon glyphicon-user"></span>';
                echo '<ul class="nav navbar-nav navbar-right">';
                echo '<li><a href="/Exit" rel="nofollow"><button class="btn btn-danger btn-sm">Выйти</button></a></li>';
                echo '</ul>';
                //echo "<p><a href='/Settings'><img src='/$img_path' height='$h_view' width='$w_view' class='img-circle'/></a></p>";
            }else{
                //$imgProcess = new MsIMGProcess;//для использования метода ресайза изображения
                echo 'Добро пожаловать, Гость! <span class="glyphicon glyphicon-user"></span>';
                echo '<ul class="nav navbar-nav navbar-right">';
                echo '<li><a href="/Registration" rel="nofollow"><button class="btn btn-default btn-sm">Регистрация</button></a></li>';
                echo '<li><a href="/Login" rel="nofollow"><button class="btn btn-default btn-sm">Вход</button></a></li>';
                echo '</ul>';
                //$img_path = 'assets/images/img/avatar_male.png';//указываем путь к "заглушке"
                //$ava_size_massiv = $imgProcess->img_out_size_mss($img_path, 80); //ресайз изображения
                //$h_view = $ava_size_massiv[0]; //полученная высота
                //$w_view = $ava_size_massiv[1]; //полученная длинна
                //echo "<p><img src='/$img_path' height='$h_view' width='$w_view' class='img-circle'/></p>";
            } ?>
        </p>
    </div><!-- приветствие END-->

    <div class="row">

        <!-- левый блок-->
            <?php
                echo $this->leftSideBarContent; //активируется в контроллерах передачей в метод render второго параметра со значением true
            ?>
       <!-- левый блок END-->
            
       <!-- основной контент средний блок -->
            <?php echo $this->content;?>
       <!-- основной контент средний блок END -->
       
       <!-- правый блок -->
            <?php
                echo $this->rightSideBarContent; //активируется в контроллерах передачей в метод render второго параметра со значением true
            ?>
       <!-- правый блок END -->
   
    </div>
<?php
//подключаем вывод протокола работы приложения (если включен режим отладки)
include_once('framework/components/massages/sysLog.php');
?>
    <!-- виджет группы ВК -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- виджет ВК START-->
        <div id="vk_community_messages"></div>
        <script type="text/javascript">
        VK.Widgets.CommunityMessages("vk_community_messages",106582958);
        </script>
    </div><!-- виджет ВК END-->

</div>
</div><!-- row -->
</div><!-- container-fluid -->
</main>
 <!----------------------------------------------------------- Основной контент страницы END------------------------------------------------->
 
 
 
 
 
 





 
 <!--------------------------------------------------------------------- Футер --------------------------------------------------------------->
<footer class="footer">
<div class="container-fluid" style="margin-top: 70%;">
<div class="row" style="background-image:url(/assets/images/footer/footer1.jpg);">

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding-top: 3%;">
        <?php
        //информеры счётчиков посещений
        include_once("inc_block/counter_informer.php");
        ?>
    </div>
    
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"> 
        <p class="text-center" style="color:#999; margin-top: 1%;"><small>
            Copyright &copy; <?php echo date('Y'); ?> by Moskaleny.<br/>
            All Rights Reserved.<br/>
    		<?php echo MSS::app()->config->app_name; ?>
        </small></p>
    </div>
    
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
    </div>
    
</div><!-- row -->
</div><!-- container-fluid -->
</footer>
 <!-------------------------------------------------------------------- Футер END--------------------------------------------------------------->
 <!-- <div class="clearfix"></div>  ОБРАЗЕЦ --> 
</body>
</html>