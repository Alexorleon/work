<?php
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

			// разрешаем вход в кабинет
			$_SESSION['admin_access'] = TRUE;
			die('<script>document.location.href= "'.lhost.'/list_posts"</script>');
		}
	}else{

		print "Включите куки";
	}

	$smarty->assign("error_", $error_);
  ?>