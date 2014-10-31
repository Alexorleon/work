<?php
	// предсменный экзаменатор;
	$typetest = 1;

	if ($_POST){

		$answer = $_POST['comp_lvl_id'];
		$idans = $_POST['answ_id'];
		
		// выбираем вариант ответа
		if ($answer == 21){ // магическое число 21 это ID в таблице уровень компетенции // TODO: потом можно будет заменить
			// правильно
			$_SESSION['transitionOption'] = 1;

			$dateBegin = $_SESSION['DATEBEGIN'];
			$dateEnd = date('d.m.Y H:i:s');
			$tempID = $_SESSION['sotrud_id'];
			$tempqu = $_SESSION['ID_question'];
			$tempans = $_SESSION['answer_attempt'];
			
			$tempAnsID = 0;
			// если сразу ответили правильно то пишем ответ, иначе берем первый вариант ответа
			if($_SESSION['answer_attempt'] == 0){
				$tempAnsID = $idans;
			}else{
			
				$tempAnsID = $_SESSION['first_answerid'];
			}

			// ответили правильно, записываем все в историю
			// TODO: транзакция
			$sql = <<<SQL
			INSERT INTO stat.ALLHISTORY (SOTRUD_ID, ALLQUESTIONSID, DATEBEGIN, DATEEND, ATTEMPTS, EXAMINERTYPE, DEL, ALLANSWERSID) VALUES 
			($tempID, $tempqu, to_date('$dateBegin', 'DD.MM.YYYY HH24:MI:SS'), to_date('$dateEnd', 'DD.MM.YYYY HH24:MI:SS'), 
			'$tempans', 1, 'N', '$tempAnsID')
SQL;
			$db->go_query($sql);
			
			die('<script>document.location.href= "'.lhost.'/commentAnswer.php?type_exam=1"</script>'); // type_exam=1 означает предсменный экзаменатор pre_shift_examiner
		}else{
			//неправильно
			// запоминаем первый вариант ответа если неправильно отвечаем впервые
			if($_SESSION['answer_attempt'] == 0){
				$_SESSION['first_answerid'] = $idans;
			}
			
			$_SESSION['transitionOption'] = 0;
			
			die('<script>document.location.href= "'.lhost.'/commentAnswer.php?type_exam=1"</script>');
		}
	}

	$temp_id = $_SESSION['ID_question']; // щас тут 0

	// повторить тот же вопрос или взять новый
	if($_SESSION['transitionOption'] == 0){ // если прошлый раз ответили не правильно

		$sql = <<<SQL
		SELECT TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_id'
SQL;
		$s_res = $db->go_result_once($sql);
		//$question_text = $s_res['TEXT']; TODO: вроде и не нужно

		// увеличиваем счетчик попыток
		$_SESSION['answer_attempt'] = $_SESSION['answer_attempt'] + 1;
	
	}else{// отвечаем впервые или в прошлый раз ответили правильно

		// стартуем таймер если начали тест, убедиться что отвечаем впервые
		if($_SESSION['answer_attempt'] == 0){

			$_SESSION['DATEBEGIN'] = date('d.m.Y H:i:s');
		}

		$sotrud_dolj = $_SESSION['sotrud_dolj'];

		// 1. получаем все тесты для определенной должности.
		// 2. получаем все вопросы по выбранным тестам.
		// 3. получаем один случайный вопрос по модулю знания из выбранных вопросов.
		//AND ALLQUESTIONS.TYPEQUESTIONSID='8'
		$sql = <<<SQL
		SELECT ID, TEXT, TYPEQUESTIONSID, SIMPLEPHOTO FROM 
		(SELECT ID, TEXT, TYPEQUESTIONSID, SIMPLEPHOTO FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.ID IN 
		(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
		(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj')) ORDER BY dbms_random.value) WHERE rownum=1
SQL;
		$s_res = $db->go_result_once($sql);

		if(empty($s_res)){
		
			die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
		}
		
		// запоминаем ID вопроса если потребуется отвечать на него снова
		$_SESSION['ID_question'] = $s_res['ID'];
		
		// запоминаем тип вопроса
		$_SESSION['type_question'] = $s_res['TYPEQUESTIONSID'];
		
		// запоминаем имя картинки
		$_SESSION['simplephoto'] = $s_res['SIMPLEPHOTO'];
		
		//$question_text = $s_res['TEXT']; TODO: вроде и не нужно
	}

	$temp_id = $_SESSION['ID_question'];
	
	// берем ответы к этому вопросу
	$sql = <<<SQL
	SELECT ID, TEXT, COMPETENCELEVELID FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_id'
SQL;
	$array_answers = $db->go_result($sql);
	
	// получаем название должности
	$temp_doljkod = $_SESSION['sotrud_dolj'];
	$sql = <<<SQL
	SELECT TEXT FROM stat.DOLJNOST WHERE DOLJNOST.KOD='$temp_doljkod'
SQL;
	$sm_sotrud_dolj = $db->go_result_once($sql);
	
	// получаем табельный
	$temp_sotrud_id = $_SESSION['sotrud_id'];
	$sql = <<<SQL
	SELECT TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.PREDPR_K='$predpr_k_glob' AND SOTRUD.SOTRUD_K='$temp_sotrud_id'
SQL;
	$sm_sotrud_tabel = $db->go_result_once($sql);
	
	/*резерв
	$sql = <<<SQL
	SELECT ID, TEXT FROM
	(SELECT ID, TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='8' AND ALLQUESTIONS.TESTNAMESID IN 
	(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') ORDER BY dbms_random.value) WHERE rownum=1
SQL;*/

	shuffle($array_answers);

	$smarty->assign("error_", $error_);

	$smarty->assign("question", $s_res);//вопрос
	$smarty->assign("array_answers", $array_answers);//ответы
	
	// FIO
	$smarty->assign("sm_sotrud_fam", $_SESSION['sotrud_fam']);
	$smarty->assign("sm_sotrud_im", $_SESSION['sotrud_im']);
	$smarty->assign("sm_sotrud_otch", $_SESSION['sotrud_otch']);	
	$smarty->assign("sm_sotrud_dolj", $sm_sotrud_dolj);
	$smarty->assign("sm_sotrud_tabel", $sm_sotrud_tabel);
	$smarty->assign("sm_ID_question", $_SESSION['ID_question']);
	
	$smarty->assign("type_question", $_SESSION['type_question']);
	$smarty->assign("simplephoto", $_SESSION['simplephoto']);

	$smarty->assign("typetest", $typetest);
	$smarty->assign("title", "Предсменный экзаменатор");
	$smarty->display("questions.tpl.html");
	
?>