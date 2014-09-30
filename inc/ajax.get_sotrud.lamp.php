<?php
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	
if ($_POST['type'] == 1){//тут массив с сотрудниками	
	$period = time() - (3 * 60 * 60); // TODO: установить нужный период
	$current_date = date('d.m.Y H:i:s', $period);
		
	$sql = <<<SQL
	SELECT TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K IN 
	(SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEEND >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND 
	EXAMINERTYPE=1) ORDER BY TABEL_KADR
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
}else if ($_POST['type'] == 2){//тут проверка табельного
	// получаем табельный и ищем его
	$check_tab_num = $_POST['check_tab_num'];
		
	$sql = <<<SQL
	SELECT SOTRUD_K FROM stat.SOTRUD WHERE SOTRUD.TABEL_KADR='$check_tab_num'
SQL;

	if ($bool_sotrud = $db->go_result_once($sql)){
		die("good_".$check_tab_num);
	}else{
		die("none_".$check_tab_num);
	}
	
}
?>