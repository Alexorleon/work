<?php
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

					// ответили правильно, записываем все в историю TODO: транзакция
					$sql = <<<SQL
					INSERT INTO stat.ALLHISTORY (SOTRUD_ID, ALLQUESTIONSID, DATEBEGIN, DATEEND, ATTEMPTS, EXAMINERTYPE, DEL) VALUES ($tempID, $tempqu, to_date('$dateBegin', 'DD.MM.YYYY HH24:MI:SS'), to_date('$dateEnd', 'DD.MM.YYYY HH24:MI:SS'), $tempans, 'pred', 'N')
SQL;
					$db->go_query($sql);

					die('<script>document.location.href= "'.lhost.'/commentAnswer.php?type_exam=1"</script>'); // type_exam=1 означает предсменный экзаменатор pre_shift_examiner
				}else{
					//не правильно
					//echo "not good";
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
				
				//$question_text = $s_res['TEXT']; TODO: вроде и не нужно
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
			
?>