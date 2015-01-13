<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 

	unset($_SESSION['type_question_chain']);
	
	unset($_SESSION['final_array_txt_questions']); // хранятся текстовые вопросы
	unset($_SESSION['final_array_txt_answers']); // основной массив для ответов
	
	unset($_SESSION['final_array_sf_questions']); // хранится текст простых фото вопросов
	unset($_SESSION['final_array_sf_answers']); // основной массив для ответов
	
	unset($_SESSION['final_array_sv_questions']); // хранится текст простых видео вопросов
	unset($_SESSION['final_array_sv_answers']); // основной массив для ответов
	
	unset($_SESSION['final_array_cv_basic']); // хранятся заголовки видео цепочек
	unset($_SESSION['final_array_cv_questions']); // хранятся вопросы видео цепочек
	unset($_SESSION['final_array_cv_answers']); // ответы цепочек
	
	// инициализация переменных по умолчанию
	$_SESSION['count_complex_question'] = 0; // счетчик для видео цепочки
	$_SESSION['temp_count_ques'] = 0; // количество заданных вопросов
	
	$_SESSION['transitionOption'] = 1; 	// флаг правильности ответа
	$_SESSION['ID_question'] = 0; 		// ID вопроса
	$_SESSION['answer_attempt'] = 0; 	// количество попыток ответов на вопрос
	$_SESSION['first_answerid'] = 0; 	// первый неправильный ответ
	$_SESSION['counter_questions'] = 0; // счетчик заданных вопросов в контроле компетентности
	$_SESSION['bool_isComplexVideo'] = false; // флаг, что сейчас проходим видео цепочку
	$_SESSION['go_answer'] = false;
	$_SESSION['max_count_chain'] = 0; // количество звеньев в видео цепочке
	
	if ((!isset($_SESSION['sotrud_id'])) or (empty($_SESSION['sotrud_id'])))
	{
		die('<script>document.location.href= "'.lhost.'/auth.php"</script>');	
	}
	$db = new db;//Создаем
	$db->GetConnect();//ПРоверяем коннект
	$error_='';
	
	if (!empty($_POST)){
		
		$type_submit = filter_input(INPUT_POST, 'type_submit_main', FILTER_SANITIZE_NUMBER_INT); //$_POST['type_submit_main']; // по какой кнопке нажали. выбераем раздел.
		
		if ($type_submit == 1){ // нормативные документы
		
			die('<script>document.location.href= "'.lhost.'/documents.php?type_doc=1"</script>');
		}elseif ($type_submit == 2){ // контроль компетентности
			
			set_numquestions($db);
			
			die('<script>document.location.href= "'.lhost.'/question.php?qtype=2"</script>');
		}elseif ($type_submit == 3){ // видеоинструктажи

			die('<script>document.location.href= "'.lhost.'/documents.php?type_doc=2"</script>');
		}elseif ($type_submit == 4){ // предсменный экзаменатор
			
			die('<script>document.location.href= "'.lhost.'/question.php?qtype=1"</script>');
		}elseif ($type_submit == 5){ // Компьютерные модели несчастных случаев
		
			die('<script>document.location.href= "'.lhost.'/documents.php?type_doc=3"</script>');
		}elseif ($type_submit == 6){ // Предложения руководству
			
			die('<script>document.location.href= "'.lhost.'/proposals"</script>');
			//die('<script>document.location.href= "'.lhost.'/proposals.php?type_prop=0"</script>');
		}elseif ($type_submit == 7){ // Личные данные

			die('<script>document.location.href= "'.lhost.'/personal_data.php"</script>');
		}else{

			die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
		}
	}
	
	$temp_doljnost_kod = $_SESSION['sotrud_dolj'];
	$sql = "SELECT TEXT FROM stat.DOLJNOST WHERE DOLJNOST.KOD='$temp_doljnost_kod'";
	$sotrud_dolj_lobby = $db->go_result_once($sql);
	
	// проверяем на новые документы
	// TODO: можно и динамкой сделать
	$temp_sotrud_id = $_SESSION['sotrud_id'];
	$array_status_newdocs = array(0, 0, 0);
	
	// документ
	$sql = <<<SQL
		SELECT ID from stat.ALLTRAINING_B WHERE 
		ALLTRAINING_B.SOTRUDID='$temp_sotrud_id' AND ALLTRAINING_B.STATUS='1' AND ALLTRAINING_B.ALLTRAININGID IN 
		(SELECT ID FROM stat.ALLTRAINING WHERE ALLTRAINING.ALLTRAININGTYPEID='1')
SQL;
		$_1_res = $db->go_result($sql);
	
	if( !empty($_1_res)){
	
		$array_status_newdocs[0] = 1;
	}
	
	// видео
	$sql = <<<SQL
		SELECT ID from stat.ALLTRAINING_B WHERE 
		ALLTRAINING_B.SOTRUDID='$temp_sotrud_id' AND ALLTRAINING_B.STATUS='1' AND ALLTRAINING_B.ALLTRAININGID IN 
		(SELECT ID FROM stat.ALLTRAINING WHERE ALLTRAINING.ALLTRAININGTYPEID='2')
SQL;
		$_2_res = $db->go_result($sql);
	
	if( !empty($_2_res)){
	
		$array_status_newdocs[1] = 1;
	}
	
	// модель
	$sql = <<<SQL
		SELECT ID from stat.ALLTRAINING_B WHERE 
		ALLTRAINING_B.SOTRUDID='$temp_sotrud_id' AND ALLTRAINING_B.STATUS='1' AND ALLTRAINING_B.ALLTRAININGID IN 
		(SELECT ID FROM stat.ALLTRAINING WHERE ALLTRAINING.ALLTRAININGTYPEID='3')
SQL;
		$_3_res = $db->go_result($sql);

	if( !empty($_3_res)){
	
		$array_status_newdocs[2] = 1;
	}
	
	$smarty->assign("error_", $error_);
	
	$smarty->assign("sotrud_tabkadr", $_SESSION['sotrud_tabkadr']);
	$smarty->assign("sotrud_fam", $_SESSION['sotrud_fam']);
	$smarty->assign("sotrud_im", $_SESSION['sotrud_im']);
	$smarty->assign("sotrud_otch", $_SESSION['sotrud_otch']);
	$smarty->assign("sotrud_dolj", $sotrud_dolj_lobby['TEXT']);
	$smarty->assign("array_status_newdocs", $array_status_newdocs);

	$smarty->assign("title", "Главная");

	$smarty->display('main.tpl.html');
	
	// --- ФУНКЦИИ ---
	
	// узнаем сколько должно быть вопросов в тесте
	function set_numquestions(&$obj){

		$sql = <<<SQL
		SELECT SUM(AMOUNT) AS "AMOUNT" FROM stat.TESTPARAMETERS
SQL;
		$numq_res = $obj->go_result_once($sql);
		
		// запоминаем количество задаваемых вопросов
		$_SESSION['numquestions'] = $numq_res['AMOUNT'];

	}
?>