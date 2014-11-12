<?php
	/*if ((!isset($_SESSION['sotrud_id'])) or (empty($_SESSION['sotrud_id'])))
	{
		die('not login');	
	}else{*/
		require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 		
		$db = new db;
		$db->GetConnect();
		$error_='';
		
	if (!empty($_POST)){
	
		//print_r($_POST);
		$dolj_id = filter_input(INPUT_POST, 'dolj_id', FILTER_SANITIZE_NUMBER_INT);//$_POST['dolj_id'];
		$test_id = filter_input(INPUT_POST, 'test_id', FILTER_SANITIZE_NUMBER_INT); //$_POST['test_id'];
		
		// проверяем на совпадение
		$sql = <<<SQL
			SELECT ID FROM stat.SPECIALITY_B WHERE TESTNAMESID='$test_id' AND DOLJNOSTKOD='$dolj_id'
SQL;
		$s_res = $db->go_result_once($sql);
		
		if(empty($s_res)){
		
			$sql = <<<SQL
				insert into stat.speciality_b (TESTNAMESID, DOLJNOSTKOD) VALUES('$test_id', '$dolj_id')
SQL;
			if( !$db->go_query($sql)){
				
				// получаем номер последнего ID после вставки. нужен для таблицы.
				$sql = <<<SQL
					SELECT Max(ID) AS "max" FROM stat.speciality_b
SQL;
				$s_res = $db->go_result_once($sql);
				
				$ans = $dolj_id."_".$test_id."_".$s_res['max'];
				die($ans);
			}else{
				$ans = "0";
				die($ans);
			}
			//$ans = ($db->go_query($sql))?"0":$dolj_id."_".$test_id;
		}else{
			$ans = "not";
			die($ans);
		}
	}
//}
?>