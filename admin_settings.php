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
        $temp_new_login = filter_input(INPUT_POST, 'new_login', FILTER_SANITIZE_SPECIAL_CHARS);
        $temp_password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
        $temp_old_password = filter_input(INPUT_POST, 'old_password', FILTER_SANITIZE_SPECIAL_CHARS);
        $temp_conf_password = filter_input(INPUT_POST, 'conf_password', FILTER_SANITIZE_SPECIAL_CHARS);
        
		if(!preg_match("/^[a-zA-Z0-9]+$/",$temp_login)){

			$err[] = "Логин может состоять только из букв английского алфавита и цифр";
		}

		if(strlen($temp_login) < 3 or strlen($temp_login) > 30){

			$err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
		}
		
		if(!preg_match("/^[a-zA-Z0-9]+$/",$temp_new_login)){

			$err[] = "Новый логин может состоять только из букв английского алфавита и цифр";
		}

		if(strlen($temp_login) < 3 or strlen($temp_new_login) > 30){

			$err[] = "Новый логин должен быть не меньше 3-х символов и не больше 30";
		}
		
		// проверям пароль
		if(!preg_match("/^[a-zA-Z0-9]+$/",$temp_old_password)){

			$err[] = "Пароль может состоять только из букв английского алфавита и цифр";
		}

		if(strlen($temp_old_password) < 4 or strlen($temp_old_password) > 32){

			$err[] = "Пароль должен быть не меньше 4-х символов и не больше 32-х";
		}
		
		// новый пароль
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
		$good_new_login = $temp_new_login;
		$good_password = $temp_password;
		$good_old_password = $temp_old_password;
		
		//print_r($check_login);
		//die();

		// Если нет ошибок, то обновляем параметры
		if(count($err) == 0){
			
			// Делаем двойное шифрование
			$password = md5(md5($good_old_password));
			
			// сверяем соответствие логина и пароля
			$sql = <<<SQL
				SELECT ID FROM stat.ADMINREG WHERE LOGIN='$good_login' AND PASSWORD='$password'
SQL;
			$value = $db->go_result_once($sql);
			
			if( !empty($value)){
			
				$password = md5(md5($good_password));
			
				$id = $value['ID'];
				
				// производим замену
				$sql = <<<SQL
					UPDATE stat.ADMINREG SET LOGIN='$good_new_login', PASSWORD='$password', 
					HASH='' WHERE ADMINREG.ID='$id'
SQL;
				$db->go_query($sql);
			}else{
				$err[] = "Логин и текущий пароль не совместимы";
				
				print "<b>При регистрации произошли следующие ошибки:</b><br>";

				foreach($err AS $error){

					print $error."<br>";
				}
			}
				
		}else{

			print "<b>При регистрации произошли следующие ошибки:</b><br>";

			foreach($err AS $error){

				print $error."<br>";
			}
		}
	}
	
	if(array_key_exists('submit_numwindows', $_POST)){

		$temp_login = filter_input(INPUT_POST, 'numwindows', FILTER_SANITIZE_NUMBER_INT);
		
		// запоминаем количество окон
		// TODO: разделение по предприятиям. пока временно через магическое число
		$sql = <<<SQL
			UPDATE stat.ADMININFO SET NUMWINDOWS='$temp_login' WHERE ADMININFO.ID='21'
SQL;
		$db->go_query($sql);
	}
	
	// текущее количество окон
	// TODO: разделение по предприятиям. пока временно через магическое число
	$sql = <<<SQL
		SELECT NUMWINDOWS FROM stat.ADMININFO WHERE ADMININFO.ID='21'
SQL;
	$value_numwindows = $db->go_result_once($sql)['NUMWINDOWS'];
	
	$role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    
    $smarty->assign("role", $role);
	$smarty->assign("error_", $error_);
	$smarty->assign("value_numwindows", $value_numwindows);
    $smarty->assign("curPage", 4);
	$smarty->assign("title", "Настройки");
	$smarty->display("admin_settings.tpl.html");

	// --- ФУНКЦИИ ---

  ?>