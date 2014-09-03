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
		//$final_array = array('41','42','43');
		// берем поочередно из каждого массива ID вопроса
		$i = 0;
		$b_dr = false;
		$b_hr = false;
		$b_sr = false;
		$count_ques = 0;
		do{
			
			more_question:
			
			if($count_ques >= $tempcount) break; // набрали нужное количество вопросов
			// если массив себя исчерпал, проходим мимо
			if(count($array_death_risk) <= $i){
			
				// берем значение из другого массива
			}else{
				// если еще не добавили
				if(!$b_dr){
					array_push($final_array, $array_death_risk[$i]);
					$b_dr = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			if(count($array_high_risk) <= $i){
			}else{
				if(!$b_hr){
					array_push($final_array, $array_high_risk[$i]);
					$b_hr = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			if(count($array_sign_risk) <= $i){
			}else{
				if(!$b_sr){
					array_push($final_array, $array_sign_risk[$i]);
					$b_sr = true;
					$count_ques++;
				}
			}
			
			// проверяем что все вложились или больше нечего добавить
			if($b_dr == true && $b_hr == true && $b_sr == true){ // все гуд, с каждого по вопросу
			
				$i++;
				$b_dr = false;
				$b_hr = false;
				$b_sr = false;
			}elseif($b_dr == false && $b_hr == false && $b_sr == false){ // опаньки, закончились вопросы в массивах
			
				// поэтому требуемое количество вопросов заменим на доступное
				$_SESSION['numquestions'] = count($final_array);
				break;
			}else{ // ага, кто то не вложидся, берем у других
				
				$i++;
				$b_dr = false;
				$b_hr = false;
				$b_sr = false;
				goto more_question;
			}
		}while ($count_ques < $tempcount);

		// запоминаем подготовленный массив
		$_SESSION['final_array'] = array(); // TODO: пока здесь, временно
		foreach ($final_array as $element){
		
			$_SESSION['final_array'][] = $element;
		}
		//print_r($_SESSION['final_array']);
		//print_r($final_array);
		//die();
		// задаем вопрос
		//ask_question($db);
		
		
		// TODO: тут должен быть вывод вопроса в зависимости от его типа (свое оформление и запрос)

		// стартуем таймер
		//$_SESSION['DATEBEGIN'] = date('d.m.y H:i:s'); в пробном тесте не нужен

		$testid = $_SESSION['final_array'][$_SESSION['counter_questions']];
		//print_r($testid['ID']);
		$temp_testid = (int)$testid['ID'];

		$sql = <<<SQL
		SELECT TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'
SQL;
		$s_res = $db->go_result_once($sql);
		$temp_id = (int)$testid['ID'];
		$question_text = $s_res['TEXT'];

		// берем ответы к этому вопросу
		$sql_ans = <<<SQL
		SELECT ID, TEXT, COMPETENCELEVELID FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_id'
SQL;
		$array_answers = $db->go_result($sql_ans);

		shuffle($array_answers);

		$_SESSION['counter_questions']++;
		
		/*print_r($array_death_risk);
		echo "<br />";
		print_r($array_high_risk);
		echo "<br />";
		print_r($array_sign_risk);
		echo "<br />";
		print_r($final_array);*/

		//die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
	}else{

		// Если количество уже заданных вопросов все еще меньше требуемого количества, задаем новый вопрос
		if($_SESSION['counter_questions'] < $_SESSION['numquestions']){

			//echo "--- " . $_SESSION['counter_questions'] . " ---";

			//ask_question($db);
			
			
			// TODO: тут должен быть вывод вопроса в зависимости от его типа (свое оформление и запрос)

		// стартуем таймер
		//$_SESSION['DATEBEGIN'] = date('d.m.y H:i:s'); в пробном тесте не нужен

		$testid = $_SESSION['final_array'][$_SESSION['counter_questions']];
		//print_r("===".$testid);
		$temp_testid = (int)$testid['ID'];

		$sql = <<<SQL
		SELECT ID, TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'
SQL;
		$s_res = $db->go_result_once($sql);

		$temp_id = (int)$testid['ID'];
		
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
	$smarty->assign("question", $question_text);//вопрос
	$smarty->assign("array_answers", $array_answers);//ответы

	$smarty->assign("typetest", 3);
	$smarty->assign("title", "Контроль компетентности");
	$smarty->display("questions.tpl.html");
	
	// --- ФУНКЦИИ ---
	
	// задаем вопрос
	function ask_question(&$obj){
		
		// TODO: тут должен быть вывод вопроса в зависимости от его типа (свое оформление и запрос)

		// стартуем таймер
		//$_SESSION['DATEBEGIN'] = date('d.m.y H:i:s'); в пробном тесте не нужен

		/*$testid = $_SESSION['final_array'][$_SESSION['counter_questions']];
		//print_r("===".$testid);

		$sql = <<<SQL
		SELECT ID, TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$testid'
SQL;
		$s_res = $obj->go_result_once($sql);

		$temp_id = $s_res['ID'];
		
		$question_text = $s_res['TEXT'];

		// берем ответы к этому вопросу
		$sql_ans = <<<SQL
		SELECT ID, TEXT, COMPETENCELEVELID FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_id'
SQL;
		$array_answers = $obj->go_result($sql_ans);

		shuffle($array_answers);*/

		//$_SESSION['counter_questions']++;
	}
?>