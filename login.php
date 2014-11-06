<?php	
//убиваем куки, чтобы воткнуть новые, знач сессии удалять надо каждую отдельно, конкретно указывая какую
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	//print_r($_SESSION);
	
	if(array_key_exists('submit', $_POST)){//if(isset($_POST['submit'])){
		// TODO: экранируем -- уже.
		//$good_login = mysql_real_escape_string($_POST['login']);
		$good_login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);//$_POST['login'];
		$good_password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);//$_POST['password'];

		// Вытаскиваем из БД запись, у которой логин равняеться введенному
		$sql = <<<SQL
			SELECT ID, PASSWORD FROM stat.ADMINREG WHERE ADMINREG.LOGIN='$good_login' AND ROWNUM <= 1
SQL;
		$data = $db->go_result_once($sql);

		// , ip=INET_ATON('192.168.1.80')
		
		// Сравниваем пароли
		if( !empty($data['PASSWORD'])){
			if($data['PASSWORD'] === md5(md5($good_password))){

				// Генерируем случайное число и шифруем его
				$hash = md5(generateCode(10));

				$insip = 0;
				$not_attach_ip = filter_input(INPUT_POST, 'not_attach_ip', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				if(!$not_attach_ip){

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

				$_SESSION["register_id"] = $data['ID']; //запоминаем айди и хэш нашего авторизовавшегося юзера
				$_SESSION["register_hash"] = $hash;
				// Переадресовываем браузер на страницу проверки нашего скрипта
				die('<script>document.location.href= "'.lhost.'/check"</script>');
				//die('<script>document.location.href= "'.lhost.'/list_posts"</script>');
			}else{

				print "Вы ввели неправильный логин/пароль";
			}
		}
	}else{
		session_destroy();
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