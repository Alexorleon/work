<?php
	//print_r($_POST);
	//die("Its work mf!!!!!=)s");
	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';

	if ($_POST){
		
		// получаем табельный и ищем его
		$check_tab_num = $_POST['check_tab_num'];
		
		/*$sql = <<<SQL
		SELECT SOTRUD_K FROM stat.SOTRUD WHERE SOTRUD.TABEL_KADR='$check_tab_num'
SQL;
		$bool_sotrud = $db->go_result_once($sql);*/
	}
	
	$period = time() - (3 * 60 * 60); // TODO: установить нужный период
	$current_date = date('d.m.Y H:i:s', $period);
		
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
	
	//$array_sotrud = $db->go_result($sql);
	echo json_encode($array_sotrud);
	//echo json_encode($bool_sotrud);
  ?>