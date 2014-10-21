<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
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

		// TODO: экранируем
		//$good_login = mysql_real_escape_string($_POST['login']);
		$good_login = $_POST['login'];
		$good_password = $_POST['password'];
		
		// проверяем, не сущестует ли пользователя с таким именем
		$sql = <<<SQL
			SELECT COUNT(ID) AS COUNT FROM stat.ADMINREG WHERE ADMINREG.LOGIN='$good_login'
SQL;
		$check_login = $db->go_result_once($sql);
		
		//print_r($check_login);
		//die();

		if($check_login['COUNT'] > 0){

			$err[] = "Пользователь с таким логином уже существует в базе данных";
		}

		// Если нет ошибок, то добавляем в БД нового пользователя
		if(count($err) == 0){
			
			// Делаем двойное шифрование
			$password = md5(md5($good_password));

			$sql = <<<SQL
				INSERT INTO stat.ADMINREG (LOGIN, PASSWORD) VALUES ('$good_login', '$password')
SQL;
			$db->go_query($sql);
				
			die('<script>document.location.href= "'.lhost.'/login"</script>');
		}else{

			print "<b>При регистрации произошли следующие ошибки:</b><br>";

			foreach($err AS $error){

				print $error."<br>";
			}
		}
	}

	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Авторизация");
	$smarty->display("register.tpl.html");

	// --- ФУНКЦИИ ---

  ?>