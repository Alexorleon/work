<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	/*if( !isset($_SESSION['admin_access'])){
	
		die('<script>document.location.href= "'.lhost.'/login"</script>');
	}*/
	//unset($_SESSION['admin_access']);
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	
	if(isset($_POST['submit'])){

		$err = array();

		// проверям логин
		if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['login'])){

			$err[] = "Логин может состоять только из букв английского алфавита и цифр";
		}

		if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30){

			$err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
		}
		
		// проверям пароль
		if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['password'])){

			$err[] = "Пароль может состоять только из букв английского алфавита и цифр";
		}

		if(strlen($_POST['password']) < 4 or strlen($_POST['password']) > 32){

			$err[] = "Пароль должен быть не меньше 4-х символов и не больше 32-х";
		}
		
		// сверяем пароли
		if($_POST['password'] != $_POST['conf_password']){

			$err[] = "Пароли не совпадают";
		}

		// TODO: экранируем
		$good_login = $_POST['login'];
		$good_password = $_POST['password'];
		
		//print_r($check_login);
		//die();

		// Если нет ошибок, то обновляем параметры
		if(count($err) == 0){
			
			// Делаем двойное шифрование
			$password = md5(md5($good_password));

			$sql = <<<SQL
				UPDATE stat.ADMINREG SET LOGIN='$good_login', PASSWORD='$password', 
				HASH='', IP='0' WHERE ADMINREG.ID='1'
SQL;
			$db->go_query($sql);
				
		}else{

			print "<b>При регистрации произошли следующие ошибки:</b><br>";

			foreach($err AS $error){

				print $error."<br>";
			}
		}
	}
	
	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Настройки");
	$smarty->display("admin_settings.tpl.html");

	// --- ФУНКЦИИ ---

  ?>