<?php
	// Пробное тестирование. Без записи в историю.
	
	if(isset($_GET['qtype'])){

		if($_GET['qtype'] == 3){ // пробное тестирование
		
			// если еще ни разу не отвечали, то требуются подготовительные действия
			if ($_SESSION['counter_questions'] == 0){
			
				// подготовка
				preparation($db);
				
				// задаем вопрос
				ask_question($db);
			}else{
			
				// Если количество уже заданных вопросов все еще меньше требуемого количества, задаем новый вопрос
				if($_SESSION['counter_questions'] < $_SESSION['numquestions']){
				
					// задаем вопрос
					ask_question($db);
				}else{ // иначе переходим в commentAnswer и выводим результаты теста.
		
					$_SESSION['counter_questions'] = 0;
					die('<script>document.location.href= "'.lhost.'/commentAnswer.php?type_exam=2"</script>');
				}
				// TODO: сюда уже не попадаем
			}
			
			$question_text = $_SESSION['question_text'];
			$array_answers = $_SESSION['array_answers'];
			
			$smarty->assign("question", $question_text);//вопрос
			$smarty->assign("array_answers", $array_answers);//ответы

			$smarty->assign("title", "Пробное тестирование");

		}elseif($_GET['qtype'] == 4){ // просто тестирование
		
			// если еще ни разу не отвечали, то требуются подготовительные действия
			if ($_SESSION['counter_questions'] == 0){
			
				// подготовка
				preparation($db);
				
				// задаем вопрос
				ask_question($db);
			}else{
			
				// Если количество уже заданных вопросов все еще меньше требуемого количества, задаем новый вопрос
				if($_SESSION['counter_questions'] < $_SESSION['numquestions']){
				
					// задаем вопрос
					ask_question($db);
				}else{ // иначе переходим в commentAnswer и выводим результаты теста.
		
					$_SESSION['counter_questions'] = 0;
					die('<script>document.location.href= "'.lhost.'/commentAnswer.php?type_exam=2"</script>');
				}
				// TODO: сюда уже не попадаем
			}
			
			$question_text = $_SESSION['question_text'];
			$array_answers = $_SESSION['array_answers'];
			
			$smarty->assign("question", $question_text);//вопрос
			$smarty->assign("array_answers", $array_answers);//ответы

			$smarty->assign("title", "Тестирование");
		}else{
			die("У меня не прописано, что делать");
		}
	}

	$smarty->assign("error_", $error_);
	$smarty->assign("typetest", 3);
	$smarty->display("questions.tpl.html");

	if ($_POST){

		$answer = $_GET['qtype'];

		if ($answer == 3){ // пробное тестирование
			
			//echo "trial testing";
		}elseif ($answer == 4){ // просто тестирование
			// пишем в историю
			// TODO: либо сразу пишем в историю, либо сохраняем список на потом. лучше сразу - частые но маленькие транзакции.
			//write_history();
		}else{
		}
	}
	
	// --- ФУНКЦИИ ---
	
	// подготовка
	function preparation(&$obj){
	
		$sotrud_dolj = $_SESSION['sotrud_dolj'];
		
		/*
		формируем равномерный массив вопросов по уровню их риска.
		для этого берем все вопросы по каждому риску. Это будет массив из ID вопросов.
		*/		
		// TODO: МОДУЛЬ 5 и ТИП ВОПРОСА текстовый
		
		// вопросы по смертельному риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=7 AND ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='8' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_death_risk = $obj->go_result($sql_ques);
		shuffle($array_death_risk);
		
		// вопросы по высокому риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=8 AND ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='8' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_high_risk = $obj->go_result($sql_ques);
		shuffle($array_high_risk);
		
		// вопросы по существенному риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=9 AND ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='8' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_sign_risk = $obj->go_result($sql_ques);
		shuffle($array_sign_risk);
		
		// формируем основной массив вопросов необходимого количества
		$tempcount = $_SESSION['numquestions']; // необходимое количество
		$final_array = array();
		
		// берем поочередно из каждого массива ID вопроса
		$count_i = 0;
		$b_dr = false;
		$b_hr = false;
		$b_sr = false;
		$count_ques = 0;
		do{
			
			more_question:
			
			if($count_ques >= $tempcount) break; // набрали нужное количество вопросов
			// если массив себя исчерпал, проходим мимо
			if(count($array_death_risk) <= $count_i){
			
				// берем значение из другого массива
			}else{
				// если еще не добавили
				if(!$b_dr){
					array_push($final_array, $array_death_risk[$count_i]);
					$b_dr = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			if(count($array_high_risk) <= $count_i){
			}else{
				if(!$b_hr){
					array_push($final_array, $array_high_risk[$count_i]);
					$b_hr = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			if(count($array_sign_risk) <= $count_i){
			}else{
				if(!$b_sr){
					array_push($final_array, $array_sign_risk[$count_i]);
					$b_sr = true;
					$count_ques++;
				}
			}
			
			// проверяем что все вложились или больше нечего добавить
			if($b_dr == true && $b_hr == true && $b_sr == true){ // все гуд, с каждого по вопросу
			
				$count_i++;
				$b_dr = false;
				$b_hr = false;
				$b_sr = false;
			}elseif($b_dr == false && $b_hr == false && $b_sr == false){ // опаньки, закончились вопросы в массивах
			
				// поэтому требуемое количество вопросов заменим на доступное
				$_SESSION['numquestions'] = count($final_array);
				break;
			}else{ // ага, кто то не вложидся, берем у других
				
				$count_i++;
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
		//die();
	}
	
	// задаем вопрос
	function ask_question(&$obj){
		
		// TODO: тут должен быть вывод вопроса в зависимости от его типа (свое оформление и запрос)
		
		// стартуем таймер
		//$_SESSION['DATEBEGIN'] = date('d.m.y H:i:s'); в пробном тесте не нужен

		$testid = $_SESSION['final_array'][$_SESSION['counter_questions']];
		//print_r($testid['ID']);
		$temp_testid = (int)$testid['ID'];

		$sql = <<<SQL
		SELECT TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'
SQL;
		$s_res = $obj->go_result_once($sql);
		//$temp_id = (int)$testid['ID'];
		$question_text = $s_res['TEXT'];

		// берем ответы к этому вопросу
		$sql_ans = <<<SQL
		SELECT ID, TEXT, COMPETENCELEVELID FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_testid'
SQL;
		$array_answers = $obj->go_result($sql_ans);

		shuffle($array_answers);

		$_SESSION['counter_questions']++;
		
		$_SESSION['question_text'] = $question_text;
		$_SESSION['array_answers'] = $array_answers;
		
		/*print_r($array_death_risk);
		echo "<br />";
		print_r($array_high_risk);
		echo "<br />";
		print_r($array_sign_risk);
		echo "<br />";
		print_r($final_array);*/
	}
	
	// пишем в историю
	function write_history(&$obj){

	}
?>