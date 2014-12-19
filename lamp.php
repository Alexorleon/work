<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if (!empty($_POST)){
	}
	
	//(ALLHISTORY.DATEEND >= to_date('$date_to',  'DD.MM.YYYY HH24:MI:SS')) and (VYD_DATA <= to_date('$date_do',  'DD.MM.YYYY HH24:MI:SS')))
	
	$period = time() - (3 * 60 * 60); // TODO: установить нужный период
	$current_date = date('d.m.Y H:i:s', $period);
        $sql = "SELECT UCHAST_K, UCHAST_NAIM FROM stat.UCHAST WHERE
                PREDPR_K='$predpr_k_glob' AND
                UCHAST_K IN (SELECT DISTINCT SOTRUD.UCHAST_K FROM stat.SOTRUD WHERE
                            SOTRUD_K IN (SELECT DISTINCT SOTRUD_ID FROM stat.ALLHISTORY))";
        
        $array_uchast = $db->go_result($sql);
        
	$sql = <<<SQL
	SELECT TABEL_SPUSK FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K IN 
	(SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEBEGIN >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND 
	EXAMINERTYPE=1) ORDER BY TABEL_SPUSK
SQL;
	$array_sotrud = $db->go_result($sql);
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_sotrud", $array_sotrud);
        $smarty->assign("array_uchast", $array_uchast);
	$smarty->assign("count_array_sotrud", count($array_sotrud));

	$smarty->assign("title", "Ламповая");
	$smarty->display("lamp.tpl.html");

	// --- ФУНКЦИИ ---

  ?>