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
	$_SESSION['first_answerid'] = 0; // первый неправильный ответ
	$_SESSION['counter_questions'] = 0; // счетчик заданных вопросов в контроле компетентности
		
	if ($_POST){
		
		$tabnum = $_POST['tabnum']; //получаем пост переменную табельного номера
		$type_submit = $_POST['type_submit'];
		
		$tabnum = trim(stripslashes(htmlspecialchars($tabnum)));
		
		// TODO: в данный момент PREDPR_K относится к Кокс-майнинг
		$sql = <<<SQL
			select SOTRUD_K, SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, DOLJ_K from stat.sotrud where TABEL_KADR='$tabnum' and DEL IS NULL and PREDPR_K=10
SQL;

		//if (!$res = $db->go_result($sql)) {to_log('res', $sql);}
		$s_res = $db->go_result_once($sql);
		if((empty($s_res))){
			// показать пользователю, что такого номера нет, можно просто сделать редирект или так
			//$error_='такого номера нет';
			// TODO: здесь нужно убрать выпадение ошибки
			die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
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

				// запомнить какой экзаменатор. необходимо для записи в таблицу истории.
				$_SESSION['examinertype']="PE";
				die('<script>document.location.href= "'.lhost.'/question.php"</script>');
			}elseif ($type_submit == "3"){

				// запомнить какой экзаменатор. необходимо для записи в таблицу истории.
				// контроль компетентности (control competence) - CC. предсменный экзаменатор (pre-shift examiner) - PE.
				$_SESSION['examinertype']="CC";

				set_numquestions($db);
				
				die('<script>document.location.href= "'.lhost.'/check_comp.html"</script>');
			}else{

				die('<script>document.location.href= "'.lhost.'/index.php"</script>');
			}
		}
	}

	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Авторизация");
	$smarty->display("auth.tpl.html");

	// --- ФУНКЦИИ ---

	// узнаем сколько должно быть вопросов в тесте
	function set_numquestions(&$obj){

		$sql = <<<SQL
		select NUMQUESTIONS from stat.ADMININFO
SQL;
		$numq_res = $obj->go_result_once($sql);

		// запоминаем количество задаваемых вопросов
		$_SESSION['numquestions'] = $numq_res['NUMQUESTIONS'];

	}
  ?>