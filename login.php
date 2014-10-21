<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';

	if(isset($_POST['submit'])){
	
		// TODO: экранируем
		//$good_login = mysql_real_escape_string($_POST['login']);
		$good_login = $_POST['login'];
		$good_password = $_POST['password'];

		// Вытаскиваем из БД запись, у которой логин равняеться введенному
		$sql = <<<SQL
			SELECT ID, PASSWORD FROM stat.ADMINREG WHERE ADMINREG.LOGIN='$good_login' AND ROWNUM <= 1
SQL;
		$data = $db->go_result_once($sql);

		// , ip=INET_ATON('192.168.1.80')
		
		// Сравниваем пароли
		if($data['PASSWORD'] === md5(md5($good_password))){

			// Генерируем случайное число и шифруем его
			$hash = md5(generateCode(10));

			$insip = 0;
			
			if(!@$_POST['not_attach_ip']){

				// Если пользователь выбрал привязку к IP
				// Переводим IP в строку
				//$insip = ", ip=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";
				$origin_ip = $_SERVER['REMOTE_ADDR'];
				// $insip = 
			}

			$id = $data['ID'];
			
			// Записываем в БД новый хеш авторизации и IP
			$sql = <<<SQL
				UPDATE stat.ADMINREG SET HASH='$hash', IP='$insip' WHERE ADMINREG.ID='$id'
SQL;
			$db->go_query($sql);
			
			// Ставим куки
			setcookie("register_id", $data['ID'], time()+60*60*24*30);
			setcookie("register_hash", $hash, time()+60*60*24*30);

			// Переадресовываем браузер на страницу проверки нашего скрипта
			die('<script>document.location.href= "'.lhost.'/check"</script>');
		}else{

			print "Вы ввели неправильный логин/пароль";
		}
	}

	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Авторизация");
	$smarty->display("login.tpl.html");

	// --- ФУНКЦИИ ---
	
	// Функция для генерации случайной строки

	function generateCode($length){

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";

		$code = "";

		$clen = strlen($chars) - 1;  
		while (strlen($code) < $length){

			$code .= $chars[mt_rand(0,$clen)];  
		}

		return $code;
	}
  ?>