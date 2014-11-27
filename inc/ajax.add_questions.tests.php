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
	
		$id_question = filter_input(INPUT_POST, 'id_question', FILTER_SANITIZE_NUMBER_INT);
		$test_id = filter_input(INPUT_POST, 'test_id', FILTER_SANITIZE_NUMBER_INT);

		$sql = <<<SQL
			INSERT INTO stat.ALLQUESTIONS_B (TESTNAMESID, ALLQUESTIONSID) VALUES('$test_id', '$id_question')
SQL;
		if( !$db->go_query($sql)){
		
			die("true");
		}else{

			die("false");
		}
	}
//}
?>