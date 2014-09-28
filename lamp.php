<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){	
	}
	
	//(ALLHISTORY.DATEEND >= to_date('$date_to',  'DD.MM.YYYY HH24:MI:SS')) and (VYD_DATA <= to_date('$date_do',  'DD.MM.YYYY HH24:MI:SS')))
	
	$period = time() - (3 * 60 * 60); // TODO: установить нужный период
	$current_date = date('d.m.Y H:i:s', $period);
	
	// , TO_CHAR(ALLHISTORY.DATEEND, 'YY-mm-dd HH24:MI:SS') AS DATEEND
	// , stat.ALLHISTORY 
	// получаем список сотрудников прошедших предсменный экзаменатор за выбранный период
//	select sotrud_id, TO_CHAR(ALLHISTORY.DATEEND, 'YY-mm-dd HH24:MI:SS') AS DATEEND from stat.allhistory where ALLHISTORY.DATEEND >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND EXAMINERTYPE=1
	$sql = <<<SQL
	SELECT TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K IN 
	(SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEEND >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND 
	EXAMINERTYPE=1)
SQL;
	$array_sotrud = $db->go_result($sql);
	
	// заменяем ID сотрудника на его табельный
	/*for ($i = 0; $i < count($array_sotrud); $i++){
	
		$temp_sotrudid = $array_sotrud[$i]['SOTRUD_ID'];
				
		$sql = <<<SQL
		SELECT TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K='$temp_sotrudid' AND PREDPR_K=10
SQL;
		$temp_tabkadr = $db->go_result_once($sql);
		
		$array_sotrud[$i]['SOTRUD_ID'] = $temp_tabkadr['TABEL_KADR'];
	}*/
	
	/*$sql = <<<SQL
	SELECT TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K IN 
	(SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEEND >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND 
	EXAMINERTYPE=1)
SQL;*/
	//echo "</br>";
	//print_r($array_sotrud);
	//die();

	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_sotrud", $array_sotrud);

	$smarty->assign("title", "Ламповая");
	$smarty->display("lamp.tpl.html");

	// --- ФУНКЦИИ ---

  ?>