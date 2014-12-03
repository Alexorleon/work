<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
	
	if (!empty($_POST)){
		
		// записываем фамилию
                $surname = filter_input(INPUT_POST, 'tabnum', FILTER_SANITIZE_SPECIAL_CHARS);
		//$surname = iconv("utf-8", "windows-1251", $surname);
		
		$sql = <<<SQL
		select SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, TABEL_SPUSK from stat.sotrud where 
		upper(SOTRUD.SOTRUD_FAM) LIKE upper('%$surname%') and DEL IS NULL and PREDPR_K=$predpr_k_glob
SQL;
		$array_employee = $db->go_result($sql);
		
		$smarty->assign("array_employee", $array_employee);
	}
	
	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Поиск сотрудника");
	$smarty->display("search_employee.tpl.html");
?>