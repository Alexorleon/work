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
		//print_r($_POST);
		$dolj_id = $_POST['dolj_id'];
		$test_id = $_POST['test_id'];
		
		$sql = <<<SQL
			insert into stat.speciality_b (TESTNAMESID, DOLJNOSTKOD) VALUES('$test_id', '$dolj_id')
SQL;
		$ans = ($db->go_query($sql))?"0":$dolj_id."_".$test_id;
		die($ans);		
	}
//}
?>