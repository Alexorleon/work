<?php	
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	// проверка доступа к странице
	if( isset($_SESSION['admin_access']) && $_SESSION['admin_access'] === TRUE){
	}else{
		//если не авторизованы, то выкидываем на ивторизацию
		die('<script>document.location.href= "'.lhost.'/login"</script>');
	}
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	
	if(array_key_exists('submit', $_POST)){

		$err = array();

		// проверям логин
                $temp_login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
                $temp_password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
                $temp_conf_password = filter_input(INPUT_POST, 'conf_password', FILTER_SANITIZE_SPECIAL_CHARS);
                
		if(!preg_match("/^[a-zA-Z0-9]+$/",$temp_login)){

			$err[] = "Логин может состоять только из букв английского алфавита и цифр";
		}

		if(strlen($temp_login) < 3 or strlen($temp_login) > 30){

			$err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
		}
		
		// проверям пароль
		if(!preg_match("/^[a-zA-Z0-9]+$/",$temp_password)){

			$err[] = "Пароль может состоять только из букв английского алфавита и цифр";
		}

		if(strlen($temp_password) < 4 or strlen($temp_password) > 32){

			$err[] = "Пароль должен быть не меньше 4-х символов и не больше 32-х";
		}
		
		// сверяем пароли
		if($temp_password != $temp_conf_password){

			$err[] = "Пароли не совпадают";
		}

		// TODO: экранируем
                // Уже.
		$good_login = $temp_login;
		$good_password = $temp_password;
		
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