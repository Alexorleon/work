<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){
		
		// записываем фамилию
		$surname = iconv("utf-8", "windows-1251", $_POST['tabnum']);
	}
	
	// разделим на 2 меню. просто вход на страницу и поиск сотрудника по введеной фамилии.
	if(isset($_GET['search_emp_type'])){
	
		if($_GET['search_emp_type'] == 0){ // просто вход
			
			$smarty->assign("search_emp_type", "view");
		}elseif($_GET['search_emp_type'] == 1){ // поиск сотрудника
		
			// находим сотрудников с такой фамилией
			/*$sql = <<<SQL
			SELECT TO_CHAR(DATE_SENT, 'YY-mm-dd HH24:MI:SS') AS DATE_SENT, PROPOSAL, ANSWER, DATE_ANSWER FROM stat.PROPOSALS 
			WHERE PROPOSALS.SOTRUDID='$sotrudID' AND PROPOSALS.PROPOSAL_TYPEMESID='$temp_typemesid'
SQL;
			//die($sql);
			$statistic = $db->go_result($sql);*/
			
			//print_r($array_statistic);
			//die();
		
			$smarty->assign("search_emp_type", "search");
		}else{}
	}
		
	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Поиск сотрудника");
	$smarty->display("search_employee.tpl.html");
?>