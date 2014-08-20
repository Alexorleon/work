<?php	
// v 0.0.2
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 

	$db = new db;
	$db->GetConnect();
	$error_='';
	//print_r($_GET);

	// в зависимости от экзаменатора выводим соответствующую инфу
	if(isset($_GET['qtype'])){

		// это предсменный экзаменатор
		if($_GET['qtype'] == 1){
			//echo "предсменный экзаменатор";
			$typetest = 1;

			if ($_POST){

				$answer = $_POST['answer'];

				// выбираем вариант ответа
				if ($answer == "21"){ // 21 это ID в таблице уровень компетенции
					//echo "good"; правильно
					$_SESSION['transitionOption'] = 1;

					$dateBegin = $_SESSION['DATEBEGIN'];
					$dateEnd = date('d.m.y H:i:s');
					$tempID = $_SESSION['sotrud_id'];
					$tempqu = $_SESSION['ID_question'];
					$tempans = $_SESSION['answer_attempt'];

					// ответили правильно, записываем все в историю
					$sql = <<<SQL
					INSERT INTO stat.ALLHISTORY (SOTRUD_ID, ALLQUESTIONSID, DATEBEGIN, DATEEND, ATTEMPTS, EXAMINERTYPE, DEL) VALUES ($tempID, $tempqu, to_date('$dateBegin', 'DD.MM.YYYY HH24:MI:SS'), to_date('$dateEnd', 'DD.MM.YYYY HH24:MI:SS'), $tempans, 'pred', 'N')
SQL;
					$db->go_query($sql);

					die('<script>document.location.href= "'.lhost.'/commentAnswer.php"</script>');
				}else{
					//не правильно
					//echo "not good";
					$_SESSION['transitionOption'] = 0;
					die('<script>document.location.href= "'.lhost.'/commentAnswer.php"</script>');
				}
			}

			$temp_id = $_SESSION['ID_question']; // щас тут 0

			// повторить тот же вопрос или взять новый
			if($_SESSION['transitionOption'] == 0){ // если прошлый раз ответили не правильно

				$sql = <<<SQL
				SELECT TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_id'
SQL;
				$s_res = $db->go_result_once($sql);
				$question_text = $s_res['TEXT'];

				// увеличиваем счетчик попыток
				$_SESSION['answer_attempt'] = $_SESSION['answer_attempt'] + 1;
			
			}else{// отвечаем впервые или в прошлый раз ответили правильно

				// стартуем таймер если начали тест, убедиться что отвечаем впервые
				if($_SESSION['answer_attempt'] == 0){

					$_SESSION['DATEBEGIN'] = date('d.m.y H:i:s');
				}

				$sotrud_dolj = $_SESSION['sotrud_dolj'];
				// из всех тестов берем только с определенной должностью.
				// затем из вопросов берем только один случайный вопрос. только по модулю знания, только текстовый и которые принадлежат к выбранным тестам.
				$sql = <<<SQL
				SELECT ID, TEXT FROM
				(SELECT ID, TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='8' AND ALLQUESTIONS.TESTNAMESID IN 
				(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') ORDER BY dbms_random.value) WHERE rownum=1
SQL;

				$s_res = $db->go_result_once($sql);

				// запоминаем ID вопроса если потребуется отвечать на него снова
				$_SESSION['ID_question'] = $s_res['ID'];

				$temp_id = $_SESSION['ID_question'];
				
				$question_text = $s_res['TEXT'];
			}
			
			// берем ответы к этому вопросу
			$sql = <<<SQL
			SELECT ID, TEXT, COMPETENCELEVELID FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_id'
SQL;
			
			$array_answers = $db->go_result($sql);
			
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

			$smarty->assign("typetest", $typetest);
			$smarty->assign("title", "Предсменный экзаменатор");
			$smarty->display("questions.tpl.html");

		// это контроль компетентности
		}elseif ($_GET['qtype'] == 2){
			//echo "контроль компетентности";
			$typetest = 2;

			// выбираем вариант тестирования (пробное или нет)
			if ($_POST){

				$answer = $_POST['answer'];

				if ($answer == "1"){ // Пробное тестирование

					//echo "Пробное тестирование";
					$_SESSION['qtype'] = 1;
					die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');

				}else{ //Тестирование

					//echo "тестирование";
					$_SESSION['qtype'] = 2;
					die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
				}
			}

			$smarty->assign("error_", $error_);

			$smarty->assign("typetest", $typetest);
			$smarty->assign("title", "Контроль компетентности");
			$smarty->display("questions.tpl.html");
		
		// попадаем сюда после выбора теста. чтобы остаться на этой же странице.
		}else{

			if($_SESSION['qtype'] == 1){ // Пробное тестирование

				echo "Пробное тестирование";

				$smarty->assign("error_", $error_);

				$smarty->assign("typetest", 3);
				$smarty->assign("title", "Контроль компетентности");
				$smarty->display("questions.tpl.html");

			}else{ // тестирование
				
				
				

				// 
				/*$sql = <<<SQL
				SELECT TESTNAMESID, MODULEID, TYPEQUESTIONSID, COUNT(TYPEQUESTIONSID) FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN (SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') GROUP BY TESTNAMESID, TYPEQUESTIONSID, MODULEID
SQL;
				echo $db->debug_show_sql_result($sql);*/
				control_competence($db);

				$smarty->assign("error_", $error_);

				$smarty->assign("typetest", 3);
				$smarty->assign("title", "Контроль компетентности");
				$smarty->display("questions.tpl.html");
			}
		}
	}

	// --- ФУНКЦИИ ---

	// контроль компетентности
	function control_competence(&$obj){

		$sotrud_dolj = $_SESSION['sotrud_dolj'];
		// выбираются только те вопросы, которые активны в таблице TESTPARAMETERS

		// 1. Посчитать сколько всего вопросов каждого типа
		$sql = <<<SQL
		SELECT TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID, COUNT(TYPEQUESTIONSID) CNT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL AND TESTPARAMETERS.MODULEID=5) GROUP BY TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID
SQL;

		$dataRow = $obj->go_result($sql);

		//print_r($dataRow);
		echo $obj->debug_show_sql_result($sql);
		
		for ($i = 0; $i < count($dataRow); $i++) {
    		echo $dataRow[$i]['TESTNAMESID'];
    		echo ',';
    		echo $dataRow[$i]['TYPEQUESTIONSID'];
    		echo ',';
    		echo $dataRow[$i]['MODULEID'];
    		echo ',';
    		echo $dataRow[$i]['RISKLEVELID'];
    		echo ',';
    		echo $dataRow[$i]['CNT'];
    		echo "---";
		}
		
		// 2. Распределить по заданному критерию

		// 3. Выбрать случайным образом необходимое количество каждого вопроса

		// 4. Проходим по массиву и грузим каждый вопрос и ответы к нему

		// 5. Выводим результаты и записываем их в историю

	}

	// пишем в историю
	function write_history(){

		echo "пишем в историю";
	}

	//$smarty->assign('predpr', $s_res);
  ?>