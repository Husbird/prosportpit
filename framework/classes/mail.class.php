<?php

/**
 * @author Biblos
 * @copyright 2014
 */

class mail extends My_DBase{
    //public $id; //id ��������������� ������������
	//public $pass;
	public $sitePath = 'http://tezis.dia-max.ru'; //������� ����� �����
    
    
    //�������� ������������ ��������������� ������
    public function sendRegDataMail($email, $pass, $name,$lastname){
        $sitePath = $this->sitePath;
        /* ���������� */
        //����� ��������� � utf8 �� 1251
        $name = iconv('UTF-8', 'windows-1251', $name);
        $lastname = iconv('UTF-8', 'windows-1251', $lastname);
        
		$to  = "user <".trim($email).">," ; //�������� �������� �� �������
		$to .= "ms <ms-projects@mail.ru>";
		
		
		/* ����\subject */
		$subject = "info@tezis.dia-max.ru";
		
		/* ��������� */
		$message = "
		<html>
		<head>
            <meta charset='windows-1251' />
            <meta http-equiv='Content-Type' content='text/html; windows-1251' />
		</head>
		<body>
		<table>
		<center>
		<h4>��������� ".$name." ".$lastname." !</h4> <h5>�� ������� ������������������ �� ����� info@tezis.dia-max.ru <br>
        (������ � � ���� ���� ������� =) )</h5>
		</center>
		<tr>
			<td><b>���� ��������������� ������:</b><br>
            E-mail: <b>".$email."</b><br>
            ������: <b>".$pass."</b><br>
            <p>����������� ��������� ��� ������ � ������� �����, � �� ���������� 3-� �����.<br>
            �������! ������������� ����� ������� �� ����� ���������� ���� ��������������� ������!</p>
            
            <center><span style='color:#333'><a href='".$sitePath."' title='������� �� ����'>������� �� ����!</a></span></center>
			</td>
		 </tr>
		 <tr>
			<td>
				<i><span style='color:green'>�������� ��� �������:</span></i><br>
				<i>+375 29 ���-96-73 (��� ��������)</i><br>
				<i>+375 25 ���-66-61 (Life ��������)</i><br><br>
                <i>��� �������� �� e-mail: info@tezis.dia-max.ru</i><br><br>
			</td>
		</tr>
		 <tr>
			<td><i><span style='margin-left:300px'>� ��������� ������������� info@tezis.dia-max.ru</i></td>
		 </tr>
		 <tr>
			<td><span style='color:red'>P.S. ���� ��� ������ ������ � ��� �� ������ - ������ ������� ���</span></td>
		 </tr>
		</table>
		</body>
		</html>
		";
		
		/* ��� �������� HTML-����� �� ������ ���������� ����� Content-type. */
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=windows-1251\r\n";
		
		/* �������������� ����� */
		$headers .= "From: info@tezis.dia-max.ru\r\n";
		/*$headers .= "Cc: birthdayarchive@example.com\r\n";
		$headers .= "Bcc: birthdaycheck@example.com\r\n";*/
		
		/* � ������ �������� �� */
		mail($to, $subject, $message, $headers);
        if(mail){
            return true;
        }
    }
    
}

?>