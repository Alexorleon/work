<?php
	/*if ((!isset($_SESSION['sotrud_id'])) or (empty($_SESSION['sotrud_id'])))
	{
		die('not login');	
	}else{*/
		require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 		
		$db = new db;
		$db->GetConnect();
		$error_='';
		
	if ($_POST){
	//Что тут вообще происходит?
		print_r($_POST);
		// удаляем тест от должности
		/*if($_POST['del_testid']){
		
			if($_POST['del_testid'] != ''){
			
				$del_testid = $_POST['del_testid']; // id должности
				
				// удаляем должность
				$sql = <<<SQL
				DELETE FROM stat.SPECIALITY_B WHERE SPECIALITY_B.ID='$del_testid'
SQL;
				$db->go_query($sql);
				
				//unset($_GET['del_testid']);
			}
		}*/
	}
//}
?>