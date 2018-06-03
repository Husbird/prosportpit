//jQuery( document ).ready(function() {
//    $("#btn").click(
//		function(){
//			sendAjaxForm('result_form', 'ajax_quick_order_confirm', '/action_ajax_form.php');
//			return false; 
//		}
//	);
//});
 
function sendAjaxForm(result_form, ajax_form, url) {
    jQuery.ajax({
        url:     url, //url страницы (action_ajax_form.php)
        type:     "POST", //метод отправки
        dataType: "html", //формат данных
        data: jQuery("#"+ajax_form).serialize(),  // Сеарилизуем объект
        success: function(response) { //Данные отправлены успешно
        	result = jQuery.parseJSON(response);
        	document.getElementById(result_form).innerHTML = result.order_rezult;
            //document.getElementById(blockId).innerHTML = "";
    	},
    	error: function(response) { // Данные не отправлены
    		document.getElementById(result_form).innerHTML = "Ошибка. Данные не отправленны.";
    	}
 	});
}

<!-- Скрипт плавного открытия и закрытия блока -->
function diplay_hide (blockId){
    
    if ($(blockId).css('display') == 'none'){ 
            $(blockId).animate({height: 'show'}, 500); 
        } 
    else{     
            $(blockId).animate({height: 'hide'}, 500);
        }
} 
///framework/components/modules/ajax/action_ajax_form.php