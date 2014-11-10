<?php
	// Пробное тестирование. Без записи в историю.
	
	if (!empty($_POST)){

		$answer = filter_input(INPUT_GET, 'qtype', FILTER_SANITIZE_NUMBER_INT);//$_GET['qtype'];
		$comp_lvl = filter_input(INPUT_POST,'comp_lvl_id', FILTER_SANITIZE_NUMBER_INT); //$_POST['comp_lvl_id'];
		$idans = filter_input(INPUT_POST,'answ_id', FILTER_SANITIZE_NUMBER_INT); //$_POST['answ_id'];
		$numid = filter_input(INPUT_POST,'numid', FILTER_SANITIZE_NUMBER_INT); //$_POST['numid'];

		if ($answer == 3){ // пробное тестирование
		
			if($comp_lvl == 99901){ // показывали пролог
			
			}elseif($comp_lvl == 99902){ // показывали эпилог
			
			}else{
				// необходимо для закрашивания цветом
				$isCorrect = '';
				if ($comp_lvl == 21){
					
					$isCorrect = 'T';
				}else{
				
					$isCorrect = 'F';
				}
				
				$temp_type_question = $_SESSION['type_question'];
				
				switch($temp_type_question){
		
					case 8: // текст
					
						array_push($_SESSION['final_array_txt_answers'], $isCorrect);
						
						array_push($_SESSION['final_array_txt_answers'], $_SESSION['array_answers'][$numid]['TEXT']);
						array_push($_SESSION['final_array_txt_answers'], $_SESSION['array_answers'][$numid]['COMMENTARY']);
						array_push($_SESSION['final_array_txt_answers'], $_SESSION['array_answers'][$numid]['PRICE']);
						break;
						
					case 9: // простое видео
						
						break;
					
					case 10: // сложное видео
						
						array_push($_SESSION['final_array_cv_answers'], $isCorrect);
						
						array_push($_SESSION['final_array_cv_answers'], $_SESSION['link_answer_complex'][$numid]['TEXT']);
						array_push($_SESSION['final_array_cv_answers'], $_SESSION['link_answer_complex'][$numid]['COMMENTARY']);
						array_push($_SESSION['final_array_cv_answers'], $_SESSION['link_answer_complex'][$numid]['PRICE']);
						break;
						
					case 21: // простое фото
						
						array_push($_SESSION['final_array_sf_answers'], $isCorrect);
						
						array_push($_SESSION['final_array_sf_answers'], $_SESSION['array_answers'][$numid]['TEXT']);
						array_push($_SESSION['final_array_sf_answers'], $_SESSION['array_answers'][$numid]['COMMENTARY']);
						array_push($_SESSION['final_array_sf_answers'], $_SESSION['array_answers'][$numid]['PRICE']);
						break;
						
					case 22: // сложное фото
						
						break;
				}
			}
			
		}elseif ($answer == 4){ // тестирование с записью в историю
			
			if($comp_lvl == 99901){ // показывали пролог
			
			}elseif($comp_lvl == 99902){ // показывали эпилог
			
			}else{
				// необходимо для закрашивания цветом
				if ($comp_lvl == 21){
					
					array_push($_SESSION['final_array_answers'], 'T');
				}else{
				
					array_push($_SESSION['final_array_answers'], 'F');
				}
				
				array_push($_SESSION['final_array_answers'], $_SESSION['array_answers'][$numid]['TEXT']);
				array_push($_SESSION['final_array_answers'], $_SESSION['array_answers'][$numid]['COMMENTARY']);
				array_push($_SESSION['final_array_answers'], $_SESSION['array_answers'][$numid]['PRICE']);
				
				// TODO: записать в куки, либо записывать на временный файл на случай восстановления и записать в историю в последний момент
				// пишем в историю
				write_history($db, $idans);
			}
		}else{
		}
	}
	
	if(isset($_GET['qtype'])){

		if($_GET['qtype'] == 3){ // пробное тестирование
		
			// если еще ни разу не отвечали, то требуются подготовительные действия
			if ($_SESSION['counter_questions'] == 0){
			
				// это цепочка, подготовка уже не требуется
				if($_SESSION['bool_isComplexVideo'] == true){
				
					// задаем вопрос
					ask_question($db);
				}else{
				
					// подготовка
					preparation($db);
					
					// задаем вопрос
					ask_question($db);
				}
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
			
			if($_SESSION['type_question'] == 8){ // текст
			
				$question_text = $_SESSION['question_text'];
				$array_answers = $_SESSION['array_answers'];
				
				$smarty->assign("sm_ID_question", $_SESSION['ID_question']);
				$smarty->assign("question", $question_text);//вопрос
				$smarty->assign("type_question", $_SESSION['type_question']);
				$smarty->assign("array_answers", $array_answers);//ответы
				
				$smarty->assign("idans", $_SESSION['ID_question']);
				
			}elseif($_SESSION['type_question'] == 21){ // простое фото
			
				$question_text = $_SESSION['question_text'];
				$array_answers = $_SESSION['array_answers'];
				
				$smarty->assign("sm_ID_question", $_SESSION['ID_question']);
				$smarty->assign("question", $question_text);//вопрос
				$smarty->assign("type_question", $_SESSION['type_question']);
				$smarty->assign("type_question", $_SESSION['type_question']);
				$smarty->assign("array_answers", $array_answers);//ответы
				$smarty->assign("simplephoto", $_SESSION['simplephoto']);
				
				$smarty->assign("idans", $_SESSION['ID_question']);
				
			}elseif($_SESSION['type_question'] == 10){ // сложное видео
				
				$smarty->assign("complex_question_text", $_SESSION['complex_question_text']);
				$smarty->assign("complex_question_prolog", $_SESSION['complex_question_prolog']);
				$smarty->assign("complex_question_epilog", $_SESSION['complex_question_epilog']);
				$smarty->assign("complex_question_catalog", $_SESSION['complex_question_catalog']);
				$smarty->assign("type_question", $_SESSION['type_question']);
				
				if($_SESSION['type_question_chain'] == "QUESTION"){
					
					$smarty->assign("link_question_complex", $_SESSION['link_question_complex']);
					$smarty->assign("link_answer_complex", $_SESSION['link_answer_complex']);
					$smarty->assign("idans", $_SESSION['link_answer_complex'][0]['COMPLEXVIDEOID']);
				}else{
				
					$smarty->assign("link_question_complex", "");
					$smarty->assign("link_answer_complex", "");
					$smarty->assign("idans", "");
				}
				
				$smarty->assign("type_question_chain", $_SESSION['type_question_chain']);
			}
			
			$smarty->assign("counter_questions", $_SESSION['temp_count_ques']);
			$smarty->assign("count_complex_question", $_SESSION['count_complex_question']);
			
			// FIO
			$smarty->assign("sm_sotrud_fam", $_SESSION['sotrud_fam']);
			$smarty->assign("sm_sotrud_im", $_SESSION['sotrud_im']);
			$smarty->assign("sm_sotrud_otch", $_SESSION['sotrud_otch']);
			$smarty->assign("sm_sotrud_dolj", $_SESSION['sotrud_dolj']);
			$smarty->assign("sm_sotrud_tabel", $_SESSION['sotrud_tabkadr']);
			$smarty->assign("title", "Пробное тестирование");

		}elseif($_GET['qtype'] == 4){ // тестирование с записью в историю
		
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
		
		// простые фото вопросы
		// вопросы по смертельному риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=7 AND ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='21' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_death_risk_sf = $obj->go_result($sql_ques);
		shuffle($array_death_risk_sf);

		// вопросы по высокому риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=8 AND ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='21' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_high_risk_sf = $obj->go_result($sql_ques);
		shuffle($array_high_risk_sf);

		// вопросы по существенному риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=9 AND ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='21' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_sign_risk_sf = $obj->go_result($sql_ques);
		shuffle($array_sign_risk_sf);
		
		// видео цепочки
		// вопросы по смертельному риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=7 AND ALLQUESTIONS.MODULEID='21' AND ALLQUESTIONS.TYPEQUESTIONSID='10' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_death_risk_cv = $obj->go_result($sql_ques);
		shuffle($array_death_risk_cv);

		// вопросы по высокому риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=8 AND ALLQUESTIONS.MODULEID='21' AND ALLQUESTIONS.TYPEQUESTIONSID='10' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_high_risk_cv = $obj->go_result($sql_ques);
		shuffle($array_high_risk_cv);

		// вопросы по существенному риску
		$sql_ques = <<<SQL
			SELECT ID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.RISKLEVELID=9 AND ALLQUESTIONS.MODULEID='21' AND ALLQUESTIONS.TYPEQUESTIONSID='10' AND ALLQUESTIONS.ID IN 
			(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
			(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
		$array_sign_risk_cv = $obj->go_result($sql_ques);
		shuffle($array_sign_risk_cv);
		
		// если нет вопросов, выходим
		if(empty($array_death_risk_sf) and empty($array_high_risk_sf) and empty($array_sign_risk_sf) 
		and empty($array_death_risk_cv) and empty($array_high_risk_cv) and empty($array_sign_risk_cv)){
		
			die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
		}
		
		// формируем основной массив вопросов необходимого количества
		$tempcount = $_SESSION['numquestions']; // необходимое количество
		$q_final_array = array(); // основной массив для вопросов
		
		// берем поочередно из каждого массива ID вопроса
		$count_i = 0;
		$b_dr_sf = false;
		$b_hr_sf = false;
		$b_sr_sf = false;
		
		// видео цепочка
		$b_dr_cv = false;
		$b_hr_cv = false;
		$b_sr_cv = false;
		$count_ques = 0;
		do{
			
			more_question:
			
			if($count_ques >= $tempcount) break; // набрали нужное количество вопросов
			
			// фото
			// если массив себя исчерпал, проходим мимо
			if(count($array_death_risk_sf) <= $count_i){
			
				// берем значение из другого массива
			}else{
				// если еще не добавили
				if(!$b_dr_sf){
					array_push($q_final_array, $array_death_risk_sf[$count_i]);
					$b_dr_sf = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			// видео цепочка
			if(count($array_death_risk_cv) <= $count_i){
			
				// берем значение из другого массива
			}else{
				// если еще не добавили
				if(!$b_dr_cv){
					array_push($q_final_array, $array_death_risk_cv[$count_i]);
					$b_dr_cv = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			if(count($array_high_risk_sf) <= $count_i){
			}else{
				if(!$b_hr_sf){
					array_push($q_final_array, $array_high_risk_sf[$count_i]);
					$b_hr_sf = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			// видео цепочка
			if(count($array_high_risk_cv) <= $count_i){
			
				// берем значение из другого массива
			}else{
				// если еще не добавили
				if(!$b_hr_cv){
					array_push($q_final_array, $array_high_risk_cv[$count_i]);
					$b_hr_cv = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			if(count($array_sign_risk_sf) <= $count_i){
			}else{
				if(!$b_sr_sf){
					array_push($q_final_array, $array_sign_risk_sf[$count_i]);
					$b_sr_sf = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			// видео цепочка
			if(count($array_sign_risk_cv) <= $count_i){
			
				// берем значение из другого массива
			}else{
				// если еще не добавили
				if(!$b_sr_cv){
					array_push($q_final_array, $array_sign_risk_cv[$count_i]);
					$b_sr_cv = true;
					$count_ques++;
				}
			}
			
			// проверяем что все вложились или больше нечего добавить
			if($b_dr_sf == true && $b_hr_sf == true && $b_sr_sf == true && $b_dr_cv == true && $b_hr_cv == true && $b_sr_cv == true){ // все гуд, с каждого по вопросу
			
				$count_i++;
				$b_dr_sf = false;
				$b_hr_sf = false;
				$b_sr_sf = false;
				
				$b_dr_cv = false;
				$b_hr_cv = false;
				$b_sr_cv = false;
			}elseif($b_dr_sf == false && $b_hr_sf == false && $b_sr_sf == false && $b_dr_cv == false && $b_hr_cv == false && $b_sr_cv == false){ // опаньки, закончились вопросы в массивах
			
				// поэтому требуемое количество вопросов заменим на доступное
				$_SESSION['numquestions'] = count($q_final_array);
				break;
			}else{ // ага, кто то не вложидся, берем у других
				
				$count_i++;
				$b_dr_sf = false;
				$b_hr_sf = false;
				$b_sr_sf = false;
				
				$b_dr_cv = false;
				$b_hr_cv = false;
				$b_sr_cv = false;
				goto more_question;
			}
		}while ($count_ques < $tempcount);

		// основной массив всех сформированных вопросов
		$_SESSION['q_final_array'] = array(); // в нем хранятся ID
		
		foreach ($q_final_array as $element){
		
			$_SESSION['q_final_array'][] = $element;
		}
		
		/*print_r("array");
		echo "</br>";
		print_r($q_final_array);
		die();*/
		
		// подготовка финальных массивов каждого типа
		$_SESSION['final_array_txt_questions'] = array(); // хранятся текстовые вопросы
		$_SESSION['final_array_txt_answers'] = array(); // основной массив для ответов
		
		$_SESSION['final_array_sf_questions'] = array(); // хранится текст простых фото вопросов
		$_SESSION['final_array_sf_answers'] = array(); // основной массив для ответов
		
		$_SESSION['final_array_cv_basic'] = array(); // хранятся заголовки видео цепочек
		$_SESSION['final_array_cv_questions'] = array(); // хранятся вопросы видео цепочек
		$_SESSION['final_array_cv_answers'] = array(); // ответы цепочек
		$_SESSION['count_complex_question'] = 0; // счетчик для видео цепочки
		$_SESSION['temp_count_ques'] = 0; // количество заданных вопросов
		//die();
	}
	
	// задаем вопрос
	function ask_question(&$obj){
		
		// проверяем не цепочка ли сейчас
		if($_SESSION['bool_isComplexVideo'] == true){
		
			// если цепочка не закончилась, задаем следующее звено
			ask_one_complexVideo($obj);

		}else{
		
			$_SESSION['temp_count_ques']++;
			
			$testid = $_SESSION['q_final_array'][$_SESSION['counter_questions']];
			//print_r($testid['ID']);
			//die();
			
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
						
				array_push($_SESSION['final_array_txt_questions'], $question_text); // запоминаем вопрос TODO: iconv

				// берем ответы к этому вопросу
				$sql_ans = <<<SQL
				SELECT ID, TEXT, COMPETENCELEVELID, COMMENTARY, PRICE FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_testid'
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

				array_push($_SESSION['final_array_cv_basic'], $s_res1); // запоминаем заголовок для таблицы результатов
				
				$_SESSION['complex_question_text'] = $s_res1['TEXT']; // заголовок цепочки
				$_SESSION['complex_question_prolog'] = $s_res1['PROLOGVIDEO'];
				$_SESSION['complex_question_catalog'] = $s_res1['CATALOG'];
				$_SESSION['complex_question_epilog'] = $s_res1['EPILOGVIDEO'];
				
				ask_one_complexVideo($obj);
				
			}elseif($temp_type_question == 21){ // простое фото
			
				$sql = <<<SQL
				SELECT ID, TEXT, SIMPLEPHOTO FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'
SQL;
				$s_res = $obj->go_result_once($sql);
				//$temp_id = (int)$testid['ID'];
				$question_text = $s_res['TEXT'];
				
				// запоминаем имя картинки
				$_SESSION['simplephoto'] = $s_res['SIMPLEPHOTO'];
						
				array_push($_SESSION['final_array_sf_questions'], $question_text); // запоминаем вопрос TODO: iconv

				// берем ответы к этому вопросу
				$sql_ans = <<<SQL
				SELECT ID, TEXT, COMPETENCELEVELID, COMMENTARY, PRICE FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_testid'
SQL;
				$array_answers = $obj->go_result($sql_ans);

				shuffle($array_answers);

				$_SESSION['counter_questions']++;
				
				$_SESSION['ID_question'] = $s_res['ID'];
				$_SESSION['question_text'] = $question_text;
				
				$_SESSION['array_answers'] = $array_answers;
				
			}elseif($temp_type_question == 22){ // сложное фото
			
			}
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
	
		/*if($_SESSION['count_complex_question'] == 5){
		
			$_SESSION['counter_questions']++;
		}*/
		
		// если закончилась цепочка
		if($_SESSION['count_complex_question'] > 5){
		
			$_SESSION['count_complex_question'] = 1;
			$_SESSION['bool_isComplexVideo'] = false;
			$_SESSION['type_question_chain'] = "EPILOG";
			$_SESSION['counter_questions']++;
		}else{
		
			// если впервые, то показать пролог
			if($_SESSION['count_complex_question'] == 0){
			
				// информативная картинка перед видео цепочкой
				if(isset($_SESSION['type_question_chain'])){
				
					$_SESSION['count_complex_question']++;
					$_SESSION['type_question_chain'] = "PROLOG"; // что показывать из видео цепочки
				}else{
					
					$_SESSION['type_question_chain'] = "INFO"; // пред пролог
				}

			}else{
			
				$_SESSION['type_question_chain'] = "QUESTION";
				
				$temp_testid = $_SESSION['global_temp_testid'];
				$count = $_SESSION['count_complex_question'];
				
				$_SESSION['count_complex_question']++;
				
				// получаем вопрос к цепочке
				$sql_ques = <<<SQL
				SELECT ID, TITLE, SIMPLEVIDEO FROM stat.COMPLEXVIDEO WHERE COMPLEXVIDEO.COMPLEXVIDEOID='$temp_testid' 
				AND COMPLEXVIDEO.POSITION='$count' AND rownum=1
SQL;
				$_SESSION['link_question_complex'] = $obj->go_result_once($sql_ques);
				
				// для таблицы результатов
				array_push($_SESSION['final_array_cv_questions'], $_SESSION['link_question_complex']['TITLE']);
				array_push($_SESSION['final_array_cv_questions'], $_SESSION['link_question_complex']['SIMPLEVIDEO']);
				
				$temp_id_ques = $_SESSION['link_question_complex']['ID'];
				
				// получаем ответы
				$sql_ans = <<<SQL
				SELECT ID, TEXT, SIMPLEVIDEO, COMMENTARY, PRICE, COMPLEXVIDEOID FROM stat.ALLANSWERS WHERE ALLANSWERS.COMPLEXVIDEOID='$temp_id_ques'
SQL;
				$array_answers = $obj->go_result($sql_ans);
				
				shuffle($array_answers);
				
				$_SESSION['link_answer_complex'] = $array_answers;
			}
		}
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