<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){
		
	}
	
	// �������� ������ ����������� ��������� ����������� ����������� �� ��������� ������
	
	//(ALLHISTORY.DATEEND >= to_date('$date_to',  'DD.MM.YYYY HH24:MI:SS')) and (VYD_DATA <= to_date('$date_do',  'DD.MM.YYYY HH24:MI:SS')))
	$period = time() - (3 * 60 * 60); // TODO: ���������� ������ ������
	$current_date = date('d.m.Y H:i:s', $period);
		
	$sql = <<<SQL
	SELECT SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K IN 
	(SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEEND >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND 
	EXAMINERTYPE=1)
SQL;
	$array_sotrud = $db->go_result($sql);
	
	print_r($array_sotrud);
	die();

	$smarty->assign("error_", $error_);

	$smarty->assign("title", "��������");
	$smarty->display("lamp.tpl.html");

	// --- ������� ---

  ?>