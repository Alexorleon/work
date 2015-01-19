<?php	
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 

	$empty_PE = (isset($_SESSION['your_PE_is_empty'])) ? 1 : 0;
	$empty_BigEx = (isset($_SESSION['your_BigEx_is_empty']))? 1 : 0;
	unset($_SESSION['your_PE_is_empty']);
	unset($_SESSION['your_BigEx_is_empty']);
	unset($_SESSION['type_question_chain']);
	/*$filename = md5(microtime() . rand(0, 9999));
	print_r($filename);
	echo "</br>";
	print_r("Временно не работает");
	die();*/

	$db = new db;
	$db->GetConnect();
	$error_='';

	// инициализация переменных по умолчанию
	$_SESSION['transitionOption'] = 1; 	// флаг правильности ответа
	$_SESSION['ID_question'] = 0; 		// ID вопроса
	$_SESSION['answer_attempt'] = 0; 	// количество попыток ответов на вопрос
	$_SESSION['first_answerid'] = 0; 	// первый неправильный ответ
	$_SESSION['counter_questions'] = 0; // счетчик заданных вопросов в контроле компетентности
	$_SESSION['bool_isComplexVideo'] = false; // флаг, что сейчас проходим видео цепочку
	$_SESSION['go_answer'] = false;
	$_SESSION['max_count_chain'] = 0; // количество звеньев в видео цепочке
		
	if (!empty($_POST)){
		
		$tabnum = filter_input(INPUT_POST, 'tabnum', FILTER_SANITIZE_NUMBER_INT);//$_POST['tabnum']; //получаем пост переменную табельного номера
		$type_submit =filter_input(INPUT_POST, 'type_submit', FILTER_SANITIZE_NUMBER_INT); //$_POST['type_submit'];
		
		// переход к поиску сотрудника
		if($type_submit == 4){

			die('<script>document.location.href= "'.lhost.'/search_employee"</script>');
		}
		
		$tabnum = trim(stripslashes(htmlspecialchars($tabnum)));
		
		$sql = <<<SQL
			select SOTRUD_K, SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, DOLJ_K, TABEL_SPUSK from stat.sotrud where TABEL_SPUSK='$tabnum' and DEL IS NULL and predpr_k=$predpr_k_glob
SQL;
		$s_res = $db->go_result_once($sql);

		if((empty($s_res))){

			die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
		}else{

			// запоминаем данные сотрудника
			$_SESSION['sotrud_id']=$s_res['SOTRUD_K'];
			$_SESSION['sotrud_fam']=$s_res['SOTRUD_FAM'];
			$_SESSION['sotrud_im']=$s_res['SOTRUD_IM'];
			$_SESSION['sotrud_otch']=$s_res['SOTRUD_OTCH'];
			$_SESSION['sotrud_dolj']=$s_res['DOLJ_K'];
			$_SESSION['sotrud_dolj_text']=""; // текст должности
			$_SESSION['sotrud_tabkadr']=$s_res['TABEL_SPUSK'];
			
			// если у этой дожности нет теста, назначим ей общий
			$temp_dolj_kod = $s_res['DOLJ_K'];
			$sql = <<<SQL
				SELECT ID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$temp_dolj_kod'
SQL;
			$test_availabilty = $db->go_result_once($sql);
			
			// TODO: магическое число. Транспорт подземный 66.
			if(empty($test_availabilty)){

				$sql = <<<SQL
					INSERT INTO stat.SPECIALITY_B (TESTNAMESID, DOLJNOSTKOD) VALUES('66', '$temp_dolj_kod')
SQL;
				$db->go_query($sql);
			}
			// переход на другую страницу, вместо header используем die.
			if($type_submit == "1"){
				die('<script>document.location.href= "'.lhost.'/index.php"</script>');
			}elseif ($type_submit == "2"){

				// запомнить какой экзаменатор. необходимо для записи в таблицу истории.
				$_SESSION['examinertype']="PE";
				die('<script>document.location.href= "'.lhost.'/question.php"</script>');
			}elseif ($type_submit == "3"){

				// запомнить какой экзаменатор. необходимо для записи в таблицу истории.
				// контроль компетентности (control competence) - CC. предсменный экзаменатор (pre-shift examiner) - PE.
				$_SESSION['examinertype']="CC";

				set_numquestions($db);
				
				die('<script>document.location.href= "'.lhost.'/check_comp.html"</script>');
			}else{

				die('<script>document.location.href= "'.lhost.'/index.php"</script>');
			}
		}
	}
	
	$smarty->assign("empty_PE", $empty_PE);
	$smarty->assign("empty_BigEx", $empty_BigEx);
	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Авторизация");
	$smarty->display("auth.tpl.html");

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