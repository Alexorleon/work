<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';

	if (isset($_COOKIE['register_id']) and isset($_COOKIE['register_hash'])){

		$id = intval($_COOKIE['register_id']);
		
		$sql = <<<SQL
			SELECT * FROM stat.ADMINREG WHERE ADMINREG.ID='$id' AND ROWNUM <= 1
SQL;
		$userdata = $db->go_result_once($sql);

		if(($userdata['HASH'] !== $_COOKIE['register_hash']) or ($userdata['ID'] !== $_COOKIE['register_id']) 
		or (($userdata['IP'] !== $_SERVER['REMOTE_ADDR'])  and ($userdata['IP'] !== "0"))){

			setcookie("register_id", "", time() - 3600*24*30*12, "/");
			setcookie("register_hash", "", time() - 3600*24*30*12, "/");

			print "Хм, что-то не получилось";
		}else{

			//print "Привет, ".$userdata['LOGIN'].". Всё работает!";
			print(iconv ('utf-8', 'windows-1251', "Привет, ").$userdata['LOGIN']);
		}
	}else{

		print "Включите куки";
	}

	$smarty->assign("error_", $error_);

	//$smarty->assign("title", "Авторизация");
	//$smarty->display("register.tpl.html");

	// --- ФУНКЦИИ ---

  ?>