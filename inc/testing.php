<?php
	// Пробное тестирование. Без записи в историю.
	
	if (!empty($_POST)){

		$answer = filter_input(INPUT_GET, 'qtype', FILTER_SANITIZE_NUMBER_INT);//$_GET['qtype'];
		$comp_lvl = filter_input(INPUT_POST,'comp_lvl_id', FILTER_SANITIZE_NUMBER_INT); //$_POST['comp_lvl_id'];
		$idans = filter_input(INPUT_POST,'answ_id', FILTER_SANITIZE_NUMBER_INT); //$_POST['answ_id'];
		$numid = filter_input(INPUT_POST,'numid', FILTER_SANITIZE_NUMBER_INT); //$_POST['numid'];

		if($comp_lvl == 99901){ // показывали пролог

		}elseif($comp_lvl == 99902){ // показывали эпилог
		
		}elseif($comp_lvl == 99903){ // показывали ответ
		
		}elseif($comp_lvl == 99904){ // прервать сдачу теста
		
			// обнуляем все данные для правильного выхода
			$_SESSION['go_answer'] = false;
			$_SESSION['counter_questions'] = 999;
			$_SESSION['counter_questions'] = $_SESSION['numquestions'];
			$answer = 3;
			
		}else{
			// необходимо для закрашивания цветом
			$isCorrect = '';
			if ($comp_lvl == 21){
				
				$isCorrect = 'T';
			}else{
			
				$isCorrect = 'F';
			}
			
			$temp_type_question = $_SESSION['type_question'];
			
			// рассчитываем время ответа
			$time_dateEnd = time();
			$time_date = $time_dateEnd - $_SESSION['TIME_DATEBEGIN'];
			
			switch($temp_type_question){
	
				case 8: // текст
                    $_SESSION['final_array_txt_answers'][] = array();
                    $ans_key = array_end_key($_SESSION['final_array_txt_answers']);
					$_SESSION['final_array_txt_answers'][$ans_key]['Correct'] = $isCorrect;
					$_SESSION['final_array_txt_answers'][$ans_key]['Text'] = $_SESSION['array_answers'][$numid]['TEXT'];
					$_SESSION['final_array_txt_answers'][$ans_key]['Comment'] = $_SESSION['array_answers'][$numid]['COMMENTARY'];
					$_SESSION['final_array_txt_answers'][$ans_key]['Price'] = $_SESSION['array_answers'][$numid]['PRICE'];
					
					$_SESSION['final_array_txt_answers'][$ans_key]['ID'] = $_SESSION['ID_question']; // id вопроса
					$_SESSION['final_array_txt_answers'][$ans_key]['ID_answer'] = $_SESSION['array_answers'][$numid]['ID'];
					
					$_SESSION['final_array_txt_answers'][$ans_key]['time'] = $time_date;
                    break;
					
				case 9: // простое видео
					
					$_SESSION['final_array_sv_answers'][] = array();
					$ans_key = array_end_key($_SESSION['final_array_sv_answers']);
					$_SESSION['final_array_sv_answers'][$ans_key]['Correct'] = $isCorrect;
					$_SESSION['final_array_sv_answers'][$ans_key]['Text'] = $_SESSION['array_answers'][$numid]['TEXT'];
					$_SESSION['final_array_sv_answers'][$ans_key]['Comment'] = $_SESSION['array_answers'][$numid]['COMMENTARY'];
					$_SESSION['final_array_sv_answers'][$ans_key]['Price'] = $_SESSION['array_answers'][$numid]['PRICE'];
					
					$_SESSION['final_array_sv_answers'][$ans_key]['ID'] = $_SESSION['ID_question']; // id вопроса
					$_SESSION['final_array_sv_answers'][$ans_key]['ID_answer'] = $_SESSION['array_answers'][$numid]['ID'];
					
					$_SESSION['final_array_sv_answers'][$ans_key]['time'] = $time_date;
					break;
				
				case 10: // сложное видео
					$basic_key = array_end_key($_SESSION['final_array_cv_answers']);
					$_SESSION['final_array_cv_answers'][$basic_key][] = array();
					$ans_key = array_end_key($_SESSION['final_array_cv_answers'][$basic_key]);
					$_SESSION['final_array_cv_answers'][$basic_key][$ans_key]['Correct'] = $isCorrect;
					
					$_SESSION['final_array_cv_answers'][$basic_key][$ans_key]['Text'] = $_SESSION['link_answer_complex'][$numid]['TEXT'];
					$_SESSION['final_array_cv_answers'][$basic_key][$ans_key]['Comment'] = $_SESSION['link_answer_complex'][$numid]['COMMENTARY'];
					$_SESSION['final_array_cv_answers'][$basic_key][$ans_key]['Price'] = $_SESSION['link_answer_complex'][$numid]['PRICE'];
					
					$_SESSION['chain_answer_cv'] = $_SESSION['link_answer_complex'][$numid]['SIMPLEVIDEO'];

					$_SESSION['final_array_cv_answers'][$basic_key][$ans_key]['ID'] = $_SESSION['link_question_complex']['ID']; // id вопроса
					$_SESSION['final_array_cv_answers'][$basic_key][$ans_key]['ID_answer'] = $_SESSION['link_answer_complex'][$numid]['ID'];
					
					$_SESSION['final_array_cv_answers'][$basic_key][$ans_key]['time'] = $time_date;
					break;
					
				case 21: // простое фото
					
					$_SESSION['final_array_sf_answers'][] = array();
					$ans_key = array_end_key($_SESSION['final_array_sf_answers']);
					$_SESSION['final_array_sf_answers'][$ans_key]['Correct'] = $isCorrect;
					$_SESSION['final_array_sf_answers'][$ans_key]['Text'] = $_SESSION['array_answers'][$numid]['TEXT'];
					$_SESSION['final_array_sf_answers'][$ans_key]['Comment'] = $_SESSION['array_answers'][$numid]['COMMENTARY'];
					$_SESSION['final_array_sf_answers'][$ans_key]['Price'] = $_SESSION['array_answers'][$numid]['PRICE'];
					
					$_SESSION['final_array_sf_answers'][$ans_key]['ID'] = $_SESSION['ID_question']; // id вопроса
					$_SESSION['final_array_sf_answers'][$ans_key]['ID_answer'] = $_SESSION['array_answers'][$numid]['ID'];
					
					$_SESSION['final_array_sf_answers'][$ans_key]['time'] = $time_date;
					break;
					
				case 22: // сложное фото
					
					break;
			}
		}
	}

	// показать ответ
	if($_SESSION['go_answer'] == true){
	
		$_SESSION['go_answer'] = false;
		$_SESSION['type_question_chain'] = "ANSWER";
		
	}else{
	
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

				// тут смотрим с записью или нет
				if ($answer == 3){ // пробное тестирование
				
				}elseif ($answer == 4){ // тестирование с записью в историю
				
					write_history($db);
				}
				
				$_SESSION['counter_questions'] = 0;
				die('<script>document.location.href= "'.lhost.'/commentAnswer.php?type_exam=2"</script>');
			}
			// сюда уже не попадаем
		}
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
			
		}elseif($_SESSION['type_question_chain'] == "ANSWER"){
		
			// показываем видео ответ
			$smarty->assign("chain_answer_cv", $_SESSION['chain_answer_cv']);
			$smarty->assign("idans", "");
		}else{
		
			$smarty->assign("link_question_complex", "");
			$smarty->assign("link_answer_complex", "");
			$smarty->assign("idans", "");
		}
		
		$smarty->assign("type_question_chain", $_SESSION['type_question_chain']);
		
	}elseif($_SESSION['type_question'] == 9){ // простое видео
	
		$question_text = $_SESSION['question_text'];
		$array_answers = $_SESSION['array_answers'];
		
		$smarty->assign("sm_ID_question", $_SESSION['ID_question']);
		$smarty->assign("question", $question_text);//вопрос
		$smarty->assign("type_question", $_SESSION['type_question']);
		$smarty->assign("array_answers", $array_answers);//ответы
		$smarty->assign("simplevideo", $_SESSION['simplevideo']);
		
		$smarty->assign("idans", $_SESSION['ID_question']);
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
	
	$smarty->assign("max_count_chain", $_SESSION['max_count_chain']);

	$smarty->assign("error_", $error_);
	$smarty->assign("typetest", 3);
	$smarty->display("questions.tpl.html");

	// --- ФУНКЦИИ ---
	
	// подготовка
	function preparation(&$obj){
		
		// подготовка финальных массивов каждого типа
		$_SESSION['final_array_txt_questions'] = array(); // хранятся текстовые вопросы
		$_SESSION['final_array_txt_answers'] = array(); // основной массив для ответов
		
		$_SESSION['final_array_sf_questions'] = array(); // хранится текст простых фото вопросов
		$_SESSION['final_array_sf_answers'] = array(); // основной массив для ответов
		
		$_SESSION['final_array_sv_questions'] = array(); // хранится текст простых видео вопросов
		$_SESSION['final_array_sv_answers'] = array(); // основной массив для ответов
		
		$_SESSION['final_array_cv_basic'] = array(); // хранятся заголовки видео цепочек
		$_SESSION['final_array_cv_questions'] = array(); // хранятся вопросы видео цепочек
		$_SESSION['final_array_cv_answers'] = array(); // ответы цепочек
		$_SESSION['count_complex_question'] = 0; // счетчик для видео цепочки
		$_SESSION['temp_count_ques'] = 0; // количество заданных вопросов
		
		$_SESSION['DATEBEGIN'] = time(); // общее время для СС в истории
		//die();
		
		$sotrud_dolj = $_SESSION['sotrud_dolj'];
		
		/*
		также необходимо сформировать количество и типы вопросов к тесту.
		данная информация берется из таблицы testparameters
		*/
		
		// модули
		$array_module_knowledge = array(); // текст, фото
		$array_module_skills = array(); // видео цепочки
		$array_module_experiences = array(); // простое видео
		$array_module_firsthelp = array(); // видео цепочки
		
		// получаем массив модулей
		$sql_module =
			"SELECT ID FROM stat.MODULE";
		$array_modules = $obj->go_result($sql_module);
		
		// получаем массив вопросов по всем рискам
		// , RISKLEVELID, TYPEQUESTIONSID
		/*$sql_test_parameters =
			"SELECT ID, MODULEID FROM stat.ALLQUESTIONS WHERE 
			ALLQUESTIONS.MODULEID IN (SELECT MODULEID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE='1' AND TESTPARAMETERS.TESTNAMESID IN (SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj')) AND 
			ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE='1' AND TESTPARAMETERS.TESTNAMESID IN (SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj')) AND 
			ALLQUESTIONS.ID IN (SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN (SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))";
		$array_test_parameters = $obj->go_result($sql_test_parameters);*/
		
		// получаем массив вопросов по всем рискам
		// , RISKLEVELID, TYPEQUESTIONSID
		$sql_test_parameters =
			"SELECT ID, MODULEID FROM stat.ALLQUESTIONS WHERE 
			ALLQUESTIONS.MODULEID IN (SELECT MODULEID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.AMOUNT != 0) AND 
			ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.AMOUNT != 0) AND 
			ALLQUESTIONS.ID IN (SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN (SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))";
		$array_test_parameters = $obj->go_result($sql_test_parameters);
		
		//$array_modules[$count_module]['ID'];
		// TODO: магические числа
		$count_index = 0;
		for($count_test_parameters = 0; $count_test_parameters < count($array_test_parameters); $count_test_parameters++){
	
			if($array_test_parameters[$count_test_parameters]['MODULEID'] == 5){
			
				$array_module_knowledge[$count_index] = $array_test_parameters[$count_test_parameters]['ID'];
				$count_index++;
			}
		}
		
		$count_index = 0;
		
		for($count_test_parameters = 0; $count_test_parameters < count($array_test_parameters); $count_test_parameters++){
	
			if($array_test_parameters[$count_test_parameters]['MODULEID'] == 21){
			
				$array_module_skills[$count_index] = $array_test_parameters[$count_test_parameters]['ID'];
				$count_index++;
			}
		}
		
		$count_index = 0;
		
		for($count_test_parameters = 0; $count_test_parameters < count($array_test_parameters); $count_test_parameters++){
	
			if($array_test_parameters[$count_test_parameters]['MODULEID'] == 22){
			
				$array_module_experiences[$count_index] = $array_test_parameters[$count_test_parameters]['ID'];
				$count_index++;
			}
		}
		
		$count_index = 0;
		
		for($count_test_parameters = 0; $count_test_parameters < count($array_test_parameters); $count_test_parameters++){
	
			if($array_test_parameters[$count_test_parameters]['MODULEID'] == 61){
			
				$array_module_firsthelp[$count_index] = $array_test_parameters[$count_test_parameters]['ID'];
				$count_index++;
			}
		}
		
		// если нет вопросов, выходим
		if(empty($array_module_knowledge) and empty($array_module_skills) and empty($array_module_experiences) and empty($array_module_experiences)){
                        $_SESSION['your_BigEx_is_empty'] = 1;
			die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
		}
		
		/*print_r($array_module_knowledge);
		print_r($array_module_skills);
		print_r($array_module_experiences);
		print_r($array_module_firsthelp);
		die();*/
		shuffle($array_module_knowledge);
		shuffle($array_module_skills);
		shuffle($array_module_experiences);
		shuffle($array_module_firsthelp);
		
		// просто обнуляем и в дальнейшем используем как счетчик количества недостающих вопросов
		//$count_index = 0;
		
		// количество задаваемых вопросов
		$count_module_knowledge = 0;
		$count_module_skills = 0;
		$count_module_experiences = 0;
		$count_module_firsthelp = 0;
		
		// определяем недостающее количество вопросов каждого модуля кроме основного - знания
		// получаем количество вопросов по модулю - проверка умений
		// TODO: магические числа
		$sql = <<<SQL
		SELECT SUM(AMOUNT) AS "AMOUNT" FROM stat.TESTPARAMETERS WHERE MODULEID=21
SQL;
		$numq_skills = $obj->go_result_once($sql)['AMOUNT'];
		
		if(count($array_module_skills) < $numq_skills){
		
			$add_num = $numq_skills - count($array_module_skills);
			//$count_index += $add_num;
			$count_module_skills = count($array_module_skills); // задаем сколько есть
		}else{
		
			$count_module_skills = $numq_skills; // задаем нужное количество
		}
		
		// получаем количество вопросов по модулю - проверка опыта
		$sql = <<<SQL
		SELECT SUM(AMOUNT) AS "AMOUNT" FROM stat.TESTPARAMETERS WHERE MODULEID=22
SQL;
		$numq_experiences = $obj->go_result_once($sql)['AMOUNT'];
		
		if(count($array_module_experiences) < $numq_experiences){
		
			$add_num = $numq_experiences - count($array_module_experiences);
			//$count_index += $add_num;
			$count_module_experiences = count($array_module_experiences); // задаем сколько есть
		}else{
		
			$count_module_experiences = $numq_experiences; // задаем нужное количество
		}
		
		// получаем количество вопросов по модулю - ПП
		$sql = <<<SQL
		SELECT SUM(AMOUNT) AS "AMOUNT" FROM stat.TESTPARAMETERS WHERE MODULEID=61
SQL;
		$numq_firsthelp = $obj->go_result_once($sql)['AMOUNT'];
		
		if(count($array_module_firsthelp) < $numq_firsthelp){
		
			$add_num = $numq_firsthelp - count($array_module_firsthelp);
			//$count_index += $add_num;
			$count_module_firsthelp = count($array_module_firsthelp); // задаем сколько есть
		}else{
		
			$count_module_firsthelp = $numq_firsthelp; // задаем нужное количество
		}
		
		$tempcount = $_SESSION['numquestions']; // необходимое количество вопросов
		
		// определяем сколько нужно вопросов из модуля - знания
		$need_knowledge_questions = ($tempcount - ($count_module_skills + $count_module_experiences + $count_module_firsthelp));
		
		if(count($array_module_knowledge) < $need_knowledge_questions){
		
			$count_module_knowledge = count($array_module_knowledge);
		}else{
		
			$count_module_knowledge = $need_knowledge_questions;
		}
		
		$count_all_questions = $count_module_knowledge + $count_module_skills + $count_module_experiences + $count_module_firsthelp;
		
		// если получившееся общее количество вопросов меньше нужного количества, возьмем сколько есть
		if($count_all_questions < $tempcount){
		
			$_SESSION['numquestions'] = $count_all_questions;
		}
		
		// формируем основной массив вопросов необходимого количества
		$q_final_array = array(); // основной массив для вопросов
		
		/*print_r($array_module_knowledge);
		print_r($array_module_skills);
		print_r($array_module_experiences);
		print_r($array_module_firsthelp);*/
		
		for($count_tempindex = 0; $count_tempindex < $count_module_knowledge; $count_tempindex++){
	
			array_push($q_final_array, $array_module_knowledge[$count_tempindex]);
		}
		
		for($count_tempindex = 0; $count_tempindex < $count_module_skills; $count_tempindex++){
	
			array_push($q_final_array, $array_module_skills[$count_tempindex]);
		}
		
		for($count_tempindex = 0; $count_tempindex < $count_module_experiences; $count_tempindex++){
	
			array_push($q_final_array, $array_module_experiences[$count_tempindex]);
		}
		
		for($count_tempindex = 0; $count_tempindex < $count_module_firsthelp; $count_tempindex++){
	
			array_push($q_final_array, $array_module_firsthelp[$count_tempindex]);
		}
		
		/*// берем поочередно из каждого массива ID вопроса
		$count_i = 0;
		$b_module_knowledge = false;
		$b_module_skills = false;
		$b_module_experiences = false;
		$b_module_firsthelp = false;
		
		$count_ques = 0;
		do{
			
			more_question:
			
			if($count_ques >= $tempcount) break; // набрали нужное количество вопросов
			
			// знания
			// если массив себя исчерпал, проходим мимо
			if(count($array_module_knowledge) <= $count_i){
			
				// берем значение из другого массива
			}else{
				// если еще не добавили
				if(!$b_module_knowledge){
					array_push($q_final_array, $array_module_knowledge[$count_i]);
					$b_module_knowledge = true;
					$count_ques++;
				}
			}
						
			if($count_ques >= $tempcount) break;
			
			// умения
			if(count($array_module_skills) <= $count_i){
			}else{
				if(!$b_module_skills){
					array_push($q_final_array, $array_module_skills[$count_i]);
					$b_module_skills = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			// опыт
			if(count($array_module_experiences) <= $count_i){
			
				// берем значение из другого массива
			}else{
				// если еще не добавили
				if(!$b_module_experiences){
					array_push($q_final_array, $array_module_experiences[$count_i]);
					$b_module_experiences = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			// первая помощь
			if(count($array_module_firsthelp) <= $count_i){
			}else{
				if(!$b_module_firsthelp){
					array_push($q_final_array, $array_module_firsthelp[$count_i]);
					$b_module_firsthelp = true;
					$count_ques++;
				}
			}
			
			if($count_ques >= $tempcount) break;
			
			// проверяем что все вложились или больше нечего добавить
			if($b_module_knowledge == true && $b_module_skills == true && $b_module_experiences == true && $b_module_firsthelp == true ){ // все гуд
			
				$count_i++;
				$b_module_knowledge = false;
				$b_module_skills = false;
				$b_module_experiences = false;
				$b_module_firsthelp = false;
				
			}elseif($b_module_knowledge == false && $b_module_skills == false && $b_module_experiences == false && $b_module_firsthelp == false){ // опаньки, закончились вопросы в массивах
			
				// поэтому требуемое количество вопросов заменим на доступное
				$_SESSION['numquestions'] = count($q_final_array);
				break;
			}else{ // ага, кто то не вложидся, берем у других
				
				$count_i++;
				$b_module_knowledge = false;
				$b_module_skills = false;
				$b_module_experiences = false;
				$b_module_firsthelp = false;
				
				goto more_question;
			}
		}while ($count_ques < $tempcount);*/

		// основной массив всех сформированных вопросов
		$_SESSION['q_final_array'] = array(); // в нем хранятся ID
		
		foreach ($q_final_array as $element){
		
			$_SESSION['q_final_array'][] = $element;
		}
		
		/*print_r("array");
		echo "</br>";
		print_r($q_final_array);
		die();*/
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
			
			$temp_testid = (int)$testid;
			$_SESSION['global_temp_testid'] = $temp_testid; // запоминаем id вопроса
				
			// TODO: опять магические числа
			$sql = "SELECT TYPEQUESTIONSID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'";
			$typeq_res = $obj->go_result_once($sql);

			// запоминаем тип вопроса
			$_SESSION['type_question'] = $typeq_res['TYPEQUESTIONSID'];
			$temp_type_question = $_SESSION['type_question'];
		
			if($temp_type_question == 8){ // текст
			
				$sql = "SELECT ID, TEXT, MODULEID as MID
                                        FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'";
				$s_res = $obj->go_result_once($sql);
				
				//$temp_id = (int)$testid['ID'];
				$question_text = $s_res['TEXT'];
                                
				$_SESSION['final_array_txt_questions'][] = array();
				$q_key = array_end_key($_SESSION['final_array_txt_questions']);
				
				$_SESSION['final_array_txt_questions'][$q_key]['Text'] = $question_text;
				$_SESSION['final_array_txt_questions'][$q_key]['Module'] = $s_res['MID'];
				//array_push($_SESSION['final_array_txt_questions'], $question_text); // запоминаем вопрос TODO: iconv

				// берем ответы к этому вопросу
				$sql_ans ="SELECT ID, TEXT, COMPETENCELEVELID, COMMENTARY, PRICE
                                           FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_testid'";
				$array_answers = $obj->go_result($sql_ans);

				shuffle($array_answers);

				$_SESSION['counter_questions']++;
				
				$_SESSION['ID_question'] = $s_res['ID'];
				$_SESSION['question_text'] = $question_text;
				
				$_SESSION['array_answers'] = $array_answers;
                                
                $_SESSION['ID_module'] = $s_res['MID'];
				
				$_SESSION['TIME_DATEBEGIN'] = time();
				
			}elseif($temp_type_question == 9){ // простое видео
			
				$sql ="SELECT ID, TEXT, SIMPLEVIDEO, MODULEID as MID
                                        FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'";
				$s_res = $obj->go_result_once($sql);
				//$temp_id = (int)$testid['ID'];
				$question_text = $s_res['TEXT'];
				
				// запоминаем имя видео
				$_SESSION['simplevideo'] = $s_res['SIMPLEVIDEO'];
						
				$_SESSION['final_array_sv_questions'][] = array();
                                
				$q_key = array_end_key($_SESSION['final_array_sv_questions']);
				$_SESSION['final_array_sv_questions'][$q_key]['Text'] = $question_text;
				$_SESSION['final_array_sv_questions'][$q_key]['Module'] = $s_res['MID'];
				//array_push($_SESSION['final_array_sf_questions'], $question_text); // запоминаем вопрос TODO: iconv

				// берем ответы к этому вопросу
				$sql_ans = "SELECT ID, TEXT, COMPETENCELEVELID, COMMENTARY, PRICE
                                            FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_testid'";
				$array_answers = $obj->go_result($sql_ans);

				shuffle($array_answers);

				$_SESSION['counter_questions']++;
				
				$_SESSION['ID_question'] = $s_res['ID'];
				$_SESSION['question_text'] = $question_text;
				
				$_SESSION['array_answers'] = $array_answers;
				
				$_SESSION['TIME_DATEBEGIN'] = time();
				
			}elseif($temp_type_question == 10){ // сложное видео		

				$_SESSION['bool_isComplexVideo'] = true;

				$sql = "SELECT ID, TEXT, PROLOGVIDEO, CATALOG, EPILOGVIDEO, MODULEID AS MID
                                        FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'";

				$s_res1 = $obj->go_result_once($sql);

				array_push($_SESSION['final_array_cv_basic'], $s_res1); // запоминаем заголовок для таблицы результатов
				$_SESSION['final_array_cv_answers'][] = array();
                                $_SESSION['final_array_cv_questions'][] = array();
				$_SESSION['complex_question_text'] = $s_res1['TEXT']; // заголовок цепочки
				$_SESSION['complex_question_prolog'] = $s_res1['PROLOGVIDEO'];
				$_SESSION['complex_question_catalog'] = $s_res1['CATALOG'];
				$_SESSION['complex_question_epilog'] = $s_res1['EPILOGVIDEO'];

				// определяем сколько под вопросов в видео цепочке
				$sql = <<<SQL
				SELECT MAX(POSITION) AS "max" FROM stat.COMPLEXVIDEO WHERE COMPLEXVIDEO.COMPLEXVIDEOID='$temp_testid'
SQL;
				$res_count_chain = $obj->go_result_once($sql);
				
				$_SESSION['max_count_chain'] = $res_count_chain['max'];

				$_SESSION['complex_question_mid'] = $s_res1['MID'];  //ID модуля
				
				ask_one_complexVideo($obj);
				
			}elseif($temp_type_question == 21){ // простое фото
			
				$sql ="SELECT ID, TEXT, SIMPLEPHOTO, MODULEID as MID
                                        FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_testid'";
				$s_res = $obj->go_result_once($sql);
				//$temp_id = (int)$testid['ID'];
				$question_text = $s_res['TEXT'];
				
				// запоминаем имя картинки
				$_SESSION['simplephoto'] = $s_res['SIMPLEPHOTO'];
						
				$_SESSION['final_array_sf_questions'][] = array();
                                
				$q_key = array_end_key($_SESSION['final_array_sf_questions']);
				$_SESSION['final_array_sf_questions'][$q_key]['Text'] = $question_text;
				$_SESSION['final_array_sf_questions'][$q_key]['Module'] = $s_res['MID'];
				//array_push($_SESSION['final_array_sf_questions'], $question_text); // запоминаем вопрос TODO: iconv

				// берем ответы к этому вопросу
				$sql_ans = "SELECT ID, TEXT, COMPETENCELEVELID, COMMENTARY, PRICE
                                            FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_testid'";
				$array_answers = $obj->go_result($sql_ans);

				shuffle($array_answers);

				$_SESSION['counter_questions']++;
				
				$_SESSION['ID_question'] = $s_res['ID'];
				$_SESSION['question_text'] = $question_text;
				
				$_SESSION['array_answers'] = $array_answers;
				
				$_SESSION['TIME_DATEBEGIN'] = time();
				
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

		// если закончилась цепочка
		if($_SESSION['count_complex_question'] > $_SESSION['max_count_chain']){
		
			$_SESSION['count_complex_question'] = 0;
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
				$sql_ques = "SELECT ID, TITLE, SIMPLEVIDEO FROM stat.COMPLEXVIDEO WHERE COMPLEXVIDEO.COMPLEXVIDEOID='$temp_testid' 
				AND COMPLEXVIDEO.POSITION='$count' AND rownum=1";
				$_SESSION['link_question_complex'] = $obj->go_result_once($sql_ques);
				
				// для таблицы результатов

				$basic_key = array_end_key($_SESSION['final_array_cv_questions']);
				$_SESSION['final_array_cv_questions'][$basic_key][] = array();
				
				$q_key = array_end_key($_SESSION['final_array_cv_questions'][$basic_key]);
				$_SESSION['final_array_cv_questions'][$basic_key][$q_key]['Text'] = $_SESSION['link_question_complex']['TITLE'];
				$_SESSION['final_array_cv_questions'][$basic_key][$q_key]['Video'] = $_SESSION['link_question_complex']['SIMPLEVIDEO'];
				$_SESSION['final_array_cv_questions'][$basic_key][$q_key]['Module'] = $_SESSION['complex_question_mid'];

				//array_push($_SESSION['final_array_cv_questions'], $_SESSION['link_question_complex']['TITLE']);
				//array_push($_SESSION['final_array_cv_questions'], $_SESSION['link_question_complex']['SIMPLEVIDEO']);
				
				$temp_id_ques = $_SESSION['link_question_complex']['ID'];
				
				// получаем ответы
				$sql_ans ="SELECT ID, TEXT, SIMPLEVIDEO, COMMENTARY, PRICE, COMPLEXVIDEOID, COMPETENCELEVELID
                                           FROM stat.ALLANSWERS WHERE ALLANSWERS.COMPLEXVIDEOID='$temp_id_ques'";
				$array_answers = $obj->go_result($sql_ans);
				
				shuffle($array_answers);
				
				$_SESSION['link_answer_complex'] = $array_answers;
				
				$_SESSION['go_answer'] = true;
				
				$_SESSION['TIME_DATEBEGIN'] = time();
			}
		}
	}
	
	// пишем в историю
	function write_history(&$obj){

		$tempID = $_SESSION['sotrud_id'];
		$beginDate = date('d.m.Y H:i:s', $_SESSION['DATEBEGIN']);

		// TODO: начать транзакцию
		// записываем всю сдачу в историю
		for($count = 0; $count < count($_SESSION['final_array_txt_answers']); $count++){

		}
		
		for($count = 0; $count < count($_SESSION['final_array_sf_answers']); $count++){
		
			$temp_qid = $_SESSION['final_array_sf_answers'][$count]['ID'];
			$temp_ansid = $_SESSION['final_array_sf_answers'][$count]['ID_answer'];
			$date = $_SESSION['final_array_sf_answers'][$count]['time'];
			
			$sql = "INSERT INTO stat.ALLHISTORY (SOTRUD_ID, ALLQUESTIONSID, DATEBEGIN, DATEEND, ATTEMPTS, EXAMINERTYPE, DEL, ALLANSWERSID) VALUES 
			($tempID, 
			$temp_qid, 
			to_date('$beginDate', 'DD.MM.YYYY HH24:MI:SS'), 
			'$date', 
			0, 
			2, 
			'N', 
			'$temp_ansid')";
			$obj->go_query($sql);
		}
		
		// сложное видео
		foreach($_SESSION['final_array_cv_basic'] as $basic_key=>$basic)
		{
			for($count = 0; $count < count($_SESSION['final_array_cv_answers'][$basic_key]); $count++){
					
				$temp_qid = $_SESSION['final_array_cv_answers'][$basic_key][$count]['ID'];
				$sql_cv = "SELECT COMPLEXVIDEOID as QID FROM stat.COMPLEXVIDEO WHERE ID='$temp_qid'";
				$temp_qid = $obj->go_result_once($sql_cv)['QID'];
				$temp_ansid = $_SESSION['final_array_cv_answers'][$basic_key][$count]['ID_answer'];
				$date = $_SESSION['final_array_cv_answers'][$basic_key][$count]['time'];

				$sql = "INSERT INTO stat.ALLHISTORY (SOTRUD_ID, ALLQUESTIONSID, DATEBEGIN, DATEEND, ATTEMPTS, EXAMINERTYPE, DEL, ALLANSWERSID) VALUES 
				($tempID, 
				$temp_qid, 
				to_date('$beginDate', 'DD.MM.YYYY HH24:MI:SS'), 
				'$date', 
				0, 
				2, 
				'N', 
				'$temp_ansid')";
				$obj->go_query($sql);
			}
		}
		
		// простое видео
		for($count = 0; $count < count($_SESSION['final_array_sv_answers']); $count++){

			$temp_qid = $_SESSION['final_array_sv_answers'][$count]['ID'];
			$temp_ansid = $_SESSION['final_array_sv_answers'][$count]['ID_answer'];
			$date = $_SESSION['final_array_sv_answers'][$count]['time'];
			
			$sql = "INSERT INTO stat.ALLHISTORY (SOTRUD_ID, ALLQUESTIONSID, DATEBEGIN, DATEEND, ATTEMPTS, EXAMINERTYPE, DEL, ALLANSWERSID) VALUES 
			($tempID, 
			$temp_qid, 
			to_date('$beginDate', 'DD.MM.YYYY HH24:MI:SS'), 
			'$date', 
			0, 
			2, 
			'N', 
			'$temp_ansid')";
			$obj->go_query($sql);
		}
	}
        
    function array_end_key($array)
    {
        end($array);
        return key($array);
    }
?>