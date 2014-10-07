<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	
	// инициализация
	$_SESSION['add_or_edit_post'] = 0; // добавление
		
	if ($_POST){
		
	}
	
	// получаем список всех сотрудников. 10 - кокс-майнинг
	$sql = <<<SQL
	SELECT SOTRUD_K, TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.PREDPR_K=10
SQL;
	$array_employees = $db->go_result($sql);
	
	
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_employees", $array_employees);

	$smarty->assign("title", "Список сотрудников");
	$smarty->display("list_employees.tpl.html");

	// --- ФУНКЦИИ ---

  ?>