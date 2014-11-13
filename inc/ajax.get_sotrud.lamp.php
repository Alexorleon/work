<?php
	//die("Где то тут ошибка");
	/*if ((!isset($_SESSION['sotrud_id'])) or (empty($_SESSION['sotrud_id'])))
	{
		die('not login');	
	}else{*/
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
	$db = new db;
	$db->GetConnect();
	$error_='';
	$temp_type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_NUMBER_INT);
if ($temp_type == 1){//тут массив с сотрудниками	
	$period = time() - (3 * 60 * 60); // TODO: установить нужный период
	$current_date = date('d.m.Y H:i:s', $period);
		
	$sql = <<<SQL
	SELECT TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K IN 
	(SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEBEGIN >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND 
	EXAMINERTYPE=1) ORDER BY TABEL_KADR
SQL;
	$array_sotrud = $db->go_result($sql);

	// заменяем ID сотрудника на его табельный
	/*for ($i = 0; $i < count($array_sotrud); $i++){
	
		$temp_sotrudid = $array_sotrud[$i]['SOTRUD_ID'];
				
		$sql = <<<SQL
		SELECT TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K='$temp_sotrudid' AND PREDPR_K=$predpr_k_glob
SQL;
		$temp_tabkadr = $db->go_result_once($sql);
		
		$array_sotrud[$i]['SOTRUD_ID'] = $temp_tabkadr['TABEL_KADR'];
	}*/
	
	//$array_sotrud = $db->go_result($sql);
	echo json_encode($array_sotrud);
	//echo json_encode($bool_sotrud);
}else if ($temp_type == 2){//тут проверка табельного
	// получаем табельный и ищем его
	$check_tab_num = filter_input(INPUT_POST, 'check_tab_num', FILTER_SANITIZE_NUMBER_INT);//$_POST['check_tab_num'];
		
	$sql = <<<SQL
	SELECT SOTRUD_K FROM stat.SOTRUD WHERE SOTRUD.TABEL_KADR='$check_tab_num' AND PREDPR_K=$predpr_k_glob
SQL;
	$bool_sotrud = $db->go_result_once($sql);

	if ($bool_sotrud){
	
		$temp_sotrud = $bool_sotrud['SOTRUD_K'];
		
		$sql = <<<SQL
		SELECT to_char(MAX(DATEBEGIN), 'DD.MM.YYYY HH24:MI:SS') AS DATEBEGIN FROM stat.ALLHISTORY WHERE ALLHISTORY.SOTRUD_ID='$temp_sotrud' AND EXAMINERTYPE=1
SQL;
		$datemax = $db->go_result_once($sql);
		
		//print_r($datemax['DATEEND']);
		//die();
		
		die("good_".$check_tab_num."_".$datemax['DATEBEGIN']);
		//die("good_".$check_tab_num."_".$datemax);
	}else{
		die("none_".$check_tab_num);
	}
	
}
//}
?>