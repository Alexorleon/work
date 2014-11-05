<?php
	// Пробное тестирование. Без записи в историю.
	
	if ($_POST){

		$answer = $_GET['qtype'];
		$comp_lvl = $_POST['comp_lvl_id'];
		$idans = $_POST['answ_id'];
		$numid = $_POST['numid'];

		if ($answer == 3){ // пробное тестирование
		
			// необходимо для закрашивания цветом
			if ($comp_lvl == 21){
				
				array_push($_SESSION['final_array_answers'], 'T');
			}else{
			
				array_push($_SESSION['final_array_answers'], 'F');
			}
			
			array_push($_SESSION['final_array_answers'], $_SESSION['array_answers'][$numid]['TEXT']);
			array_push($_SESSION['final_array_answers'], $_SESSION['array_answers'][$numid]['COMMENTARY']);
			//print_r($_SESSION['final_array_answers']);
			//die();
			
		}elseif ($answer == 4){ // тестирование с записью в историю
			
			// необходимо для закрашивания цветом
			if ($comp_lvl == 21){
				
				array_push($_SESSION['final_array_answers'], 'T');
			}else{
			
				array_push($_SESSION['final_array_answers'], 'F');
			}
			
			array_push($_SESSION['final_array_answers'], $_SESSION['array_answers'][$numid]['TEXT']);
			array_push($_SESSION['final_array_answers'], $_SESSION['array_answers'][$numid]['COMMENTARY']);
			
			// пишем в историю
			write_history($db, $idans);
		}else{
		}
	}
	
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
			
			$smarty->assign("sm_ID_question", $_SESSION['ID_question']);
			$smarty->assign("question", $question_text);//вопрос
			$smarty->assign("type_question", $_SESSION['type_question']);
			$smarty->assign("array_answers", $array_answers);//ответы

			$smarty->assign("title", "Пробное тестирование");

		}elseif($_GET['qtype'] == 4){ // просто тестирование
		
			// если еще ни разу не отвечали, то требуются подготовительные действия
			if ($_SESSION['counter_questions'] == 0){
			
				// подготовка
				preparation($db);
				
				// стартуем таймер
				$_SESSION['DATEBEGIN'] = date('d.m.y H:i:s');
				
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
			
			$smarty->assign("sm_ID_question", $_SESSION['ID_question']);
			$smarty->assign("question", $question_text);//вопрос
			$smarty->assign("type_question", $_SESSION['type_question']);
			$smarty->assign("array_answers", $array_answers);//ответы

			$smarty->assign("title", "Тестирование");
		}else{
			die("У меня не прописано, что делать");
		}
	}

	$smarty->assign("error_", $error_);
	$smarty->assign("typetest", 3);
	$smarty->display("questions.tpl.html");

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
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=7 AND ALLQUESTIONS.MODULEID='21' AND ALLQUESTIONS.TYPEQUESTIONSID='10' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_death_risk = $obj->go_result($sql_ques);
		shuffle($array_death_risk);
		
		// вопросы по высокому риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=8 AND ALLQUESTIONS.MODULEID='21' AND ALLQUESTIONS.TYPEQUESTIONSID='10' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_high_risk = $obj->go_result($sql_ques);
		shuffle($array_high_risk);
		
		// вопросы по существенному риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=9 AND ALLQUESTIONS.MODULEID='21' AND ALLQUESTIONS.TYPEQUESTIONSID='10' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_sign_risk = $obj->go_result($sql_ques);
		shuffle($array_sign_risk);
		
		// формируем основной массив вопросов необходимого количества
		$tempcount = $_SESSION['numquestions']; // необходимое количество
		$q_final_array = array(); // основной массив для вопросов
		
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
					array_push($q_final_array, $array_death_risk[$count_i]);
					$b_dr = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			if(count($array_high_risk) <= $count_i){
			}else{
				if(!$b_hr){
					array_push($q_final_array, $array_high_risk[$count_i]);
					$b_hr = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			if(count($array_sign_risk) <= $count_i){
			}else{
				if(!$b_sr){
					array_push($q_final_array, $array_sign_risk[$count_i]);
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
				$_SESSION['numquestions'] = count($q_final_array);
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
		$_SESSION['q_final_array'] = array(); // TODO: пока временно здесь - в нем хранятся ID
		$_SESSION['final_array_answers'] = array(); // основной массив для ответов - хранится текст
		$_SESSION['final_array_questions'] = array(); // хранится текст вопросов
		
		$_SESSION['final_array_complex_questions'] = array(); // хранится текст вопросов цепочек
		$_SESSION['bool_isComplexVideo'] = false; // флаг, что сейчас проходим видео цепочку
		$_SESSION['count_complex_question'] = 1; // счетчик для видео цепочки
		
		foreach ($q_final_array as $element){
		
			$_SESSION['q_final_array'][] = $element;
		}
		
		//print_r($_SESSION['q_final_array']);
		//die();
	}
	
	// задаем вопрос
	function ask_question(&$obj){
		
		// вывод вопроса в зависимости от его типа (свое оформление и запрос)
		if($_SESSION['bool_isComplexVideo'] == true){
		
			
		}else{
		
		}
		
		$testid = $_SESSION['q_final_array'][$_SESSION['counter_questions']];
		//print_r($testid['ID']);
		$temp_testid = (int)$testid['ID'];
		$_SESSION['global_temp_testid'] = $temp_testid; // запоминаем id вопроса
			
		// TODO: опять магические числа
		$sql = <<<SQL
		SELECT TYPEQUESTIONSID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'
SQL;
		$typeq_res = $obj->go_result_once($sql);

		// запоминаем тип вопроса
		$_SESSION['type_question'] = $typeq_res['TYPEQUESTIONSID'];
		$temp_type_question = $_SESSION['type_question'];
		
		if($temp_type_question == 8){ // текст
		
			$sql = <<<SQL
			SELECT ID, TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'
SQL;
			$s_res = $obj->go_result_once($sql);
			//$temp_id = (int)$testid['ID'];
			$question_text = $s_res['TEXT'];
					
			array_push($_SESSION['final_array_questions'], $question_text); // запоминаем вопрос TODO: iconv

			// берем ответы к этому вопросу
			$sql_ans = <<<SQL
			SELECT ID, TEXT, COMPETENCELEVELID, COMMENTARY FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_testid'
SQL;
			$array_answers = $obj->go_result($sql_ans);

			shuffle($array_answers);

			$_SESSION['counter_questions']++;
			
			$_SESSION['ID_question'] = $s_res['ID'];
			$_SESSION['question_text'] = $question_text;
			
			$_SESSION['array_answers'] = $array_answers;
			
		}elseif($temp_type_question == 9){ // простое видео
		
		}elseif($temp_type_question == 10){ // сложное видео
		
			$_SESSION['bool_isComplexVideo'] = true;
			
			$sql = <<<SQL
			SELECT TEXT, PROLOGVIDEO, CATALOG, EPILOGVIDEO FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'
SQL;
			$s_res1 = $obj->go_result_once($sql);
			$_SESSION['complex_question_text'] = $s_res1['TEXT'];
			$_SESSION['complex_question_prolog'] = $s_res1['PROLOGVIDEO'];
			$_SESSION['complex_question_epilog'] = $s_res1['EPILOGVIDEO'];
			$_SESSION['complex_question_catalog'] = $s_res1['CATALOG'];
					
			array_push($_SESSION['final_array_complex_questions'], $_SESSION['complex_question_text']); // запоминаем вопрос - заголовок цепочки

			ask_one_complexVideo($db);
			
		}elseif($temp_type_question == 21){ // простое фото
		
		}elseif($temp_type_question == 22){ // сложное фото
		
		}
		
		/*print_r($array_death_risk);
		echo "<br />";
		print_r($array_high_risk);
		echo "<br />";
		print_r($array_sign_risk);
		echo "<br />";
		print_r($q_final_array);*/
	}
	
	// задаем одно звено из видео цепочки
	function ask_one_complexVideo(&$obj){
	
		$temp_testid = $_SESSION['global_temp_testid'];
		$count = $_SESSION['count_complex_question'];
		
		// получаем вопрос к цепочке
		// TODO: ORDER BY POSITION ASC
		$sql_ques = <<<SQL
		SELECT ID, TITLE, SIMPLEVIDEO FROM stat.COMPLEXVIDEO WHERE COMPLEXVIDEO.COMPLEXVIDEOID='$temp_testid' 
		AND COMPLEXVIDEO.POSITION='$count'
SQL;
		$_SESSION['link_question_complex'] = $obj->go_result_once($sql_ques);
		
		// теперь нужно получить ответы !!!!!!!
		//array_push($_SESSION['final_array_questions'], $_SESSION['']);
		
		$_SESSION['count_complex_question']++;
	}
	
	// пишем в историю
	function write_history(&$obj, $tempAnsID){

		$tempID = $_SESSION['sotrud_id'];
		$tempcount = $_SESSION['counter_questions'];
		$tempcount--;
		$tempqu = (int)$_SESSION['q_final_array'][$tempcount]['ID'];
		$dateBegin = $_SESSION['DATEBEGIN'];
		$dateEnd = date('d.m.y H:i:s');

		$sql = <<<SQL
			INSERT INTO stat.ALLHISTORY (SOTRUD_ID, ALLQUESTIONSID, DATEBEGIN, DATEEND, ATTEMPTS, EXAMINERTYPE, DEL, ALLANSWERSID) VALUES 
			($tempID, 
			$tempqu, 
			to_date('$dateBegin', 'DD.MM.YYYY HH24:MI:SS'), 
			to_date('$dateEnd', 'DD.MM.YYYY HH24:MI:SS'), 
			0, 
			2, 
			'N', 
			'$tempAnsID')
SQL;
		$obj->go_query($sql);
	}
?>