<?php
	// Пробное тестирование. Без записи в историю.

	//echo "Пробное тестирование";

	if ($_POST){

		$answer = $_POST['answer'];

			// выбираем вариант ответа
			if ($answer == "21"){ // 21 это ID в таблице уровень компетенции. магическое число возможно нужно будет заменить.
				//echo "good"; // правильно


				//die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
			}else{ //не правильно
				//echo "not good";
				// TODO: либо сразу пишем в историю, либо сохраняем список на потом. лучше сразу - частые но маленькие транзакции.
				//die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
			}
	}

	// если еще ни разу не отвечали, то требуются подготовительные действия
	if ($_SESSION['counter_questions'] == 0){
		
		$sotrud_dolj = $_SESSION['sotrud_dolj'];
		
		/*
		формируем равномерный массив вопросов по уровню их риска.
		для этого берем все вопросы по каждому риску. Это будет массив из ID вопросов.
		*/
		
		// вопросы по смертельному риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=7 AND ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='8' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_death_risk = $db->go_result($sql_ques);
		shuffle($array_death_risk);
		
		// вопросы по высокому риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=8 AND ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='8' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_high_risk = $db->go_result($sql_ques);
		shuffle($array_high_risk);
		
		// вопросы по существенному риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=9 AND ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='8' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_sign_risk = $db->go_result($sql_ques);
		shuffle($array_sign_risk);
		
		// формируем основной массив вопросов необходимого количества
		$tempcount = $_SESSION['numquestions']; // необходимое количество
		$final_array = array();
		
		for ($i = 0; $i < $tempcount; $i++){
			
		}

		// TODO: задаем вопрос

		//$_SESSION['counter_questions']++;
		print_r($array_death_risk);
		echo "<br />";
		print_r($array_high_risk);
		echo "<br />";
		print_r($array_sign_risk);
		die();
		//die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
	}else{

		// Если количество уже заданных вопросов все еще меньше требуемого количества, задаем новый вопрос
		if($_SESSION['counter_questions'] < 3 /*$_SESSION['numquestions']*/){ // TODO: пока тестирование

			//echo "--- " . $_SESSION['counter_questions'] . " ---";

			// стартуем таймер
			$_SESSION['DATEBEGIN'] = date('d.m.y H:i:s');

			

			// TODO: задаем вопрос. берем ID вопроса из этого массива.
			// TODO: проверить количество задаваемых вопросов с размером массива

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
		
			$_SESSION['counter_questions'] = 0;
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