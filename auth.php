<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';

	// инициализация переменных по умолчанию
	$_SESSION['transitionOption'] = 1; // флаг правильности ответа
	$_SESSION['ID_question'] = 0; // ID вопроса
	$_SESSION['answer_attempt'] = 0; // количество попыток ответов на вопрос

	// TODO: определить какой экзаменатор. необходимо для записи в таблицу истории.
		
	if ($_POST){
		
		$tabnum = $_POST['tabnum'];//получаем пост переменную табельного номера
		$type_submit = $_POST['type_submit'];
		
		$tabnum = trim(stripslashes(htmlspecialchars($tabnum)));
		$sql = <<<SQL
			select SOTRUD_K, SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, DOLJ_K from stat.sotrud where TABEL_KADR='$tabnum' and DEL IS NULL
SQL;

		//if (!$res = $db->go_result($sql)) {to_log('res', $sql);}
		$s_res = $db->go_result_once($sql);

		if((empty($s_res))){
			// показать пользователю, что такого номера нет, можно просто сделать редирект или так
			$error_='такого номера нет'; 
		}else{

			// запоминаем данные сотрудника
			$_SESSION['sotrud_id']=$s_res['SOTRUD_K'];
			$_SESSION['sotrud_fam']=$s_res['SOTRUD_FAM'];
			$_SESSION['sotrud_im']=$s_res['SOTRUD_IM'];
			$_SESSION['sotrud_otch']=$s_res['SOTRUD_OTCH'];
			$_SESSION['sotrud_dolj']=$s_res['DOLJ_K'];

			// переход на другую страницу, вместо header используем die.
			if ($type_submit == "1"){
				die('<script>document.location.href= "'.lhost.'/index.php"</script>');
			}elseif ($type_submit == "2"){
				die('<script>document.location.href= "'.lhost.'/question.php"</script>');
			}elseif ($type_submit == "3"){
				die('<script>document.location.href= "'.lhost.'/check_comp.html"</script>');
			}else{
				die('<script>document.location.href= "'.lhost.'/index.php"</script>');
			}
		}
	}

	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Авторизация");
	$smarty->display("auth.tpl.html");

	//$smarty->assign('predpr', $s_res);
  ?>