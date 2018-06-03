<?php
header("HTTP/1.0 404 Not Found");
?>
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
	<title><?php echo $this->data->pageTitle;?></title>
	<meta name="keywords" content="404, ошибка" />
	<meta name="description" content="Страница не найдена,ошибка 404" />
    <link rel="icon" href="/mss.ico" type="ico"/>
    <!--стили bootstrap:-->                                   
    <link href="<?php echo MSS::app()->config->bootstrap_path;?>/css/bootstrap.css" rel="stylesheet"/>
    <!--собственный фаил стилей:-->
    <link href="<?php echo MSS::app()->config->css_path;?>/ms_style.css" rel="stylesheet"/>
    <script src="<?php echo MSS::app()->config->scripts_path;?>/audiojs/audio.min.js"></script><!-- аудиоплеер -->
    
    <script>
      audiojs.events.ready(function() {
        var as = audiojs.createAll();
      });
    </script><!--скрипт аудиоплеера END -->
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="<?php echo MSS::app()->config->bootstrap_path;?>/js/bootstrap.js"></script>
    <script src="https://vk.com/js/api/openapi.js?135" type="text/javascript"></script><!--интерфейс VK (вконтакте)-->
    <!-- инициализация VK.init (вконтакте) для блока комментариев START-->
    <script type="text/javascript">
      VK.init({apiId: 5680768, onlyWidgets: true});
    </script><!-- инициализация VK.init (вконтакте) для блока комментариев END--> 
</head>
<body>
<!---------------------------------------------------------------------- header ---------------------------------------------------------------->
<header>





</header>
<!-- .-------------------------------------------------------------------- header END ---------------------------------------------------------->













 <!-------------------------------------------------------------- Основной контент страницы ---------------------------------------------------->
<main>
<div class="container-fluid">
<div class="row"  style="background-color: ;">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!--навигация-->
    <nav class="navbar navbar-inverse navbar-fixed-top"  role="navigation nav-stacked">
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
                    ?>
                    </ul>
                    </li>

                    <ul class="nav navbar-nav navbar-right">
                        <?php if(MSS::$user_role != 'Guest'){
                        echo '<li><a href="/Exit" rel="nofollow"><button class="btn btn-danger btn-sm">Выйти</button></a></li>';
                        }else{
                         echo '<li><a href="/Login" rel="nofollow"><button class="btn btn-success btn-sm">Вход</button></a></li>';
                         echo '<li><a href="/Registration" rel="nofollow"><button class="btn btn-primary btn-sm">Регистрация</button></a></li>';
                        }
                        ?>
                    </ul>
                </ul>
            </div>
        </div>
    </nav>
    <!--навигация end-->
    
    <?php
        ////подключаем виджет хлебные крошки
        //echo '<br/><br/><br/><br/>';
        //$breadcumb = new Breadcrumb;
        //$breadcumb->run();
    ?>

    <div style="text-align: right; margin: 5px;"><!-- приветствие -->
        <p>Добро пожаловать, Гость!</p>
    </div><!-- приветствие END-->

    <div class="row">
    
        <!-- левый блок-->
                <?php
                    echo $this->leftSideBarContent; //активируется в контроллерах передачей в метод render второго параметра со значением true
                ?>
       <!-- левый блок END-->
        <h4 style="margin-left: 34%;">Запрашиваемая страница не найдена :(</h4>
            <center>
                <img src="/assets/images/404.jpg"/>
            </center>
   
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

</div><!-- row -->
</div><!-- container-fluid -->
</main>
 <!----------------------------------------------------------- Основной контент страницы END------------------------------------------------->
 
 
 
 
 
 





 
 <!--------------------------------------------------------------------- Футер --------------------------------------------------------------->
<footer class="footer">
<div class="container-fluid" style="margin-top: 70%;">
<div class="row" style="background-image:url(/assets/images/footer/footer1.jpg);">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
        <p class="text-center" style="color:#999; margin-top: 1%;"><small>
            Copyright &copy; <?php echo date('Y'); ?> by Moskaleny.<br/>
            All Rights Reserved.<br/>
    		<?php echo MSS::app()->config->app_name; ?>
        </small></p>
    </div>
</div><!-- row -->
</div><!-- container-fluid -->
</footer>
 <!-------------------------------------------------------------------- Футер END--------------------------------------------------------------->
 <!-- <div class="clearfix"></div>  ОБРАЗЕЦ --> 
</body>
</html>