<?php
session_start();
function __autoload($class) // пишем функцию автозагрузки классов
{
    require_once('framework/classes/'.$class.'.class.php');//файлы классы фреймворка
}


$MsDBProcess = new MsDBProcess;

if (isset($_POST["client_name"]) && isset($_POST["telephone_num"]) && isset($_POST["product_id"])) { 

    $params['client_name'] = mysqli_real_escape_string($MsDBProcess->mysqli,$_POST["client_name"]);
    $params['telephone_num'] = mysqli_real_escape_string($MsDBProcess->mysqli,$_POST["telephone_num"]);
    $params['order_date'] = time(); //$date_today = date("d.m.y / H:i:s"); //дата и время заказа
    //получаем код заказа
    $params['order_code'] = strtoupper('X-'.substr(md5($params['order_date'].$params['telephone_num']),1,8));
    $params['products_id'] = "|".mysqli_real_escape_string($MsDBProcess->mysqli,$_POST["product_id"]);
    $params['address'] = ' - отсутствует так как <b>клиент воспользовался кнопкой быстрого заказа</b>';
    $add = $MsDBProcess->universalInsertDB('orders',$params);//универсальный метод добавления данных в БД 
    
    //отправляем сообщеня на электронную почту.
    $SendMail = new MsSendMail();
    $timeProcess = new MsTimeProcess;
    $sitePath = MSS::app()->config->site_path;
    $order_date = $timeProcess->dateFromTimestamp($params['order_date']);
    $array = array("developer" => "ms-projects@mail.ru", "admin" => "prosportpit@mail.ru"); //кому отправляем
    $subject = "Заказ на сайте ".$sitePath." от ".$order_date.""; //тема сообщения
    
    //если запись в БД прошла успешно
    if($add){ 
        $text = "
                <html>
            		<head>
                        <meta charset='windows-1251' />
                        <meta http-equiv='Content-Type' content='text/html; charset=windows-1251' />
            		</head>
                    <body>
                		<table>
                		<center>
                		<h4>Здравствуйте!</h4>
                		</center>
                		<tr>
                			<td>
                                <p>".$order_date." на сайт".$sitePath.", поступил заказ <br>код заказа: <b>".$params['order_code']."</b></p>
                                <span style='color:#333'><a href='".$sitePath."'>Перейти на сайт</a></span>
                			</td>
                		 </tr>
                         
                		 <tr>
                			<td>
                                <i><span style='margin-left:300px'>Удачи!</span></i><br>
                                <i><span style='margin-left:300px'>С уважением, $sitePath</span></i>
                                <i><span style='margin-left:300px'>г.Донецк</span></i>
                            </td>
                		 </tr>
                		 <tr>
                			<td><span style='color:red'> P.S. Письмо отправлено автоматически <br> если это письмо попало к вам по ошибке - просто удалите его</span></td>
                		 </tr>
                		</table>
            		</body>
          		</html>
            "; //полный текст сообщения (с тегами)

        // Формируем массив для JSON ответа
        $result = array(
        	'order_rezult' => "
            
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center; font-size:12px;'>
                <br/>
                <div class='alert alert-success' role='alert'>
                    <p><span class='glyphicon glyphicon-ok-circle'></span> Заказ успешно принят!</p>
                    <p>Код Вашего заказа:</p>
                    <p><b>{$params['order_code']}</b>.</p>
                    <p>Рекомендуем сохранить эти данные до получения заказа!</p>
                </div>
                <br/>
            </div>
            
            ",
        	//'telephone_num' => $_POST["telephone_num"],
            //'product_id' => $_POST["product_id"]
        );
    //если при записи в БД возникла ошибка
    }else{
        
        $text = "
                <html>
            		<head>
                        <meta charset='windows-1251' />
                        <meta http-equiv='Content-Type' content='text/html; charset=windows-1251' />
            		</head>
                    <body>
                		<table>
                		<center>
                		<h4>Здравствуйте!</h4>
                		</center>
                		<tr>
                			<td>
                                <p>".$order_date." на сайте".$sitePath.", при поступлении заказа (быстрого) <br>код: <b>".$params['order_code']."</b> произошла
                                ошибка =(</p>
                                <span style='color:#333'><a href='".$sitePath."'>Перейти на сайт</a></span>
                			</td>
                		 </tr>
                         
                		 <tr>
                			<td>
                                <i><span style='margin-left:300px'>С уважением, $sitePath</span></i><br />
                                <i><span style='margin-left:300px'>г.Донецк</span></i>
                            </td>
                		 </tr>
                		 <tr>
                			<td><span style='color:red'> P.S. Письмо отправлено автоматически <br> если это письмо попало к вам по ошибке - просто удалите его</span></td>
                		 </tr>
                		</table>
            		</body>
          		</html>
            "; //полный текст сообщения (с тегами)
        
        // Формируем массив для JSON ответа
        $result = array(
        	'order_rezult' => 'Ошибка записи в БД: return:'.$add,
        	//'telephone_num' => $_POST["telephone_num"],
            //'product_id' => $_POST["product_id"]
        );     
    }
    
    $SendMail->sendMail($array,$from = false,$subject,$text);//отправка письма

    // Переводим массив в JSON
    echo json_encode($result); 
}

?>