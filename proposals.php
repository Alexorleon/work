<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){
		
		$type_proposals = $_POST['type_proposals'];

		if ($type_proposals == 0){

			// переходим в лобби
			die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}elseif($type_proposals == 1){
		
			// записываем предложение
			$sotrudID = $_SESSION['sotrud_id'];
			$typemessage = $_POST['typemessage'];
			$message = iconv("utf-8", "windows-1251", $_POST['tabnum']);
			$current_date = date('d.m.Y H:i:s');
			
			$sql = <<<SQL
				INSERT INTO stat.PROPOSALS (SOTRUDID, PROPOSAL_TYPEMESID, PROPOSAL, DATE_SENT) VALUES 
				('$sotrudID', 
				'$typemessage', 
				'$message', 
				to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS'))
SQL;
			$db->go_query($sql);
			
			// TODO: доложить об успешности
			
		}else{}
	}
	
	// получаем типы сообщения
	$sql = <<<SQL
	SELECT ID, TITLE FROM stat.PROPOSALS_TYPEMES
SQL;
	$array_typemes = $db->go_result($sql);
		
	// разделим на 2 меню. статистику и добавление нового предложения.
	if(isset($_GET['type_prop'])){
	
		if($_GET['type_prop'] == 0){ // выводим статистику
			
			$sotrudID = $_SESSION['sotrud_id'];
			$array_statistic = array();
		
			for($count_i = 0; $count_i < count($array_typemes); $count_i++){
			
				$temp_typemesid = (int)$array_typemes[$count_i]['ID'];
				
				// собираем статистику
				$sql = <<<SQL
				SELECT TO_CHAR(DATE_SENT, 'YY-mm-dd HH24:MI:SS') AS DATE_SENT, PROPOSAL, ANSWER, DATE_ANSWER FROM stat.PROPOSALS 
				WHERE PROPOSALS.SOTRUDID='$sotrudID' AND PROPOSALS.PROPOSAL_TYPEMESID='$temp_typemesid'
SQL;
				//die($sql);
				$statistic = $db->go_result($sql);

				$array_temp2 = array();
				
				for($count_n = 0; $count_n < count($statistic); $count_n++){
				
					$array_temp = array();
					
					array_push($array_temp, $statistic[$count_n]['DATE_SENT']);
					array_push($array_temp, $statistic[$count_n]['PROPOSAL']);
					array_push($array_temp, $statistic[$count_n]['ANSWER']);
					array_push($array_temp, $statistic[$count_n]['DATE_ANSWER']);
					
					array_push($array_temp2, $array_temp);
				}
				
				array_push($array_statistic, $array_temp2);
			}		
			
			//print_r($array_statistic);
			//die();
			
			$smarty->assign("type_prop", "view");
			$smarty->assign("array_statistic", $array_statistic);
		}elseif($_GET['type_prop'] == 1){ // добавляем новое предложение
		
			$smarty->assign("type_prop", "add");
		}else{}
	}
		
	$smarty->assign("error_", $error_);
	$smarty->assign("array_typemes", $array_typemes);

	$smarty->assign("title", "Предложения руководству");
	$smarty->display("proposals.tpl.html");
?>