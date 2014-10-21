<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	
	// инициализация
	$_SESSION['add_or_edit_employee'] = 0; // добавление
		
	if($_GET){
		
		if($_GET['del_employeeid']){
		
			if($_GET['del_employeeid'] != ''){
			
				$del_employeeid = $_GET['del_employeeid']; // id должности
				
				// удаляем сотрудника
				$sql = <<<SQL
				DELETE FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K='$del_employeeid'
SQL;
				$db->go_query($sql);
				
				//unset($_GET['del_employeeid']);
			}
		}
	}
	
	// получаем список всех сотрудников. 10 - кокс-майнинг
	$sql = <<<SQL
	SELECT SOTRUD.SOTRUD_K, SOTRUD.SOTRUD_FAM, SOTRUD.SOTRUD_IM, SOTRUD.SOTRUD_OTCH, DOLJNOST.TEXT AS TEXT, DOLJNOST.KOD AS KOD, SOTRUD.TABEL_KADR 
	FROM stat.SOTRUD, stat.DOLJNOST WHERE SOTRUD.PREDPR_K=10 AND SOTRUD.DOLJ_K=DOLJNOST.KOD
SQL;
	$array_employees = $db->go_result($sql);
		
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_employees", $array_employees);

	$smarty->assign("title", "Список сотрудников");
	$smarty->display("list_employees.tpl.html");

	// --- ФУНКЦИИ ---

  ?>