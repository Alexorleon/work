<?php
	 // Пробное тестирование. Без записи в историю.

	//echo "Пробное тестирование";

	if ($_POST){

		$answer = $_POST['answer'];
		//echo "<br /";
		//echo $answer;
		//echo "<br /";
		// выбираем вариант ответа
		if ($answer == "21"){ // 21 это ID в таблице уровень компетенции
			//echo "good"; // правильно


			//die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
		}else{ //не правильно
			//echo "not good";
			//
			//die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
		}
	}

	// если еще ни разу не отвечали, то требуются подготовительные действия
	if ($_SESSION['counter_questions'] == 0){
				
		// TODO: формируем контроль компетентности. количество вопросов по рискам. Это будет массив из ID вопросов.
		//-----
		$_SESSION['tempmass'] = array(41, 42, 43);
		//-----

		// TODO: задаем вопрос

		$_SESSION['counter_questions']++;

		die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
	}else{

		// Если количество уже заданных вопросов все еще меньше требуемого количества, задаем новый вопрос
		if($_SESSION['counter_questions'] < 3 /*$_SESSION['numquestions']*/){ // TEST

			//echo "--- " . $_SESSION['counter_questions'] . " ---";

			// стартуем таймер
			$_SESSION['DATEBEGIN'] = date('d.m.y H:i:s');

			$sotrud_dolj = $_SESSION['sotrud_dolj'];

			// TODO: задаем вопрос. берем ID вопроса из этого массива.

			// TODO: тут должен быть вывод вопроса в зависимости от его типа

			$testid = $_SESSION['tempmass'][$_SESSION['counter_questions']];

			$sql = <<<SQL
			SELECT ID, TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$testid'
SQL;
			$s_res = $db->go_result_once($sql);

			$temp_id = $s_res['ID'];

			//echo $temp_id;
						
			$question_text = $s_res['TEXT'];

			// берем ответы к этому вопросу
			$sql_ans = <<<SQL

			SELECT ID, TEXT, COMPETENCELEVELID FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_id'
SQL;
								
			$array_answers = $db->go_result($sql_ans);

			shuffle($array_answers);

			$_SESSION['counter_questions']++;
				
		}else{ // иначе переходим в commentAnswer и выводим результаты теста.

			die('<script>document.location.href= "'.lhost.'/commentAnswer.php?type_exam=2"</script>');
		}
	}

		$smarty->assign("error_", $error_);
		$smarty->assign("question", $s_res);//вопрос
		$smarty->assign("array_answers", $array_answers);//ответы

		$smarty->assign("typetest", 3);
		$smarty->assign("title", "Контроль компетентности");
		$smarty->display("questions.tpl.html");
?>