<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
	
	if ($_POST){
		
		// записываем фамилию
		$surname = iconv("utf-8", "windows-1251", $_POST['tabnum']);
		
		$sql = <<<SQL
		select SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, TABEL_KADR from stat.sotrud where 
		upper(SOTRUD.SOTRUD_FAM) LIKE upper('%$surname%') and DEL IS NULL and PREDPR_K=10
SQL;
		$array_employee = $db->go_result($sql);
		
		$smarty->assign("array_employee", $array_employee);
	}
	
	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Поиск сотрудника");
	$smarty->display("search_employee.tpl.html");
?>