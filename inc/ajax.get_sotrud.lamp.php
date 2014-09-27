<?php	
	//die("Its work mf!!!!!=)s");
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';

	if ($_POST){
		
		// получаем табельный и ищем его
		$check_tab_num = $_POST['check_tab_num'];
		print_r($check_tab_num);
	}
	
	$period = time() - (3 * 60 * 60); // TODO: установить нужный период
	$current_date = date('d.m.Y H:i:s', $period);
		
	// получаем список сотрудников прошедших предсменный экзаменатор за выбранный период
	$sql = <<<SQL
	SELECT TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K IN 
	(SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEEND >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND 
	EXAMINERTYPE=1)
SQL;
	/*$sql = <<<SQL
	SELECT SOTRUD.TABEL_KADR, TO_CHAR(ALLHISTORY.DATEEND, 'YY-mm-dd HH24:MI:SS') AS DATEEND FROM stat.SOTRUD, stat.ALLHISTORY WHERE 
	ALLHISTORY.DATEEND >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND SOTRUD.SOTRUD_K IN 
	(SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEEND >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND 
	EXAMINERTYPE=1)
SQL;*/
	$array_sotrud = $db->go_result($sql);
	echo json_encode($array_sotrud);
  ?>