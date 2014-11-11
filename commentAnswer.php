<?php	
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';

//print_r(date('Y-m-d H:i:s')); 2014-08-15 14:02:19
$type_exam = filter_input(INPUT_GET, 'type_exam', FILTER_SANITIZE_NUMBER_INT);
if($type_exam)
{
    
	// это предсменный экзаменатор
	if($type_exam == 1){
		
		// как ответили (правильно или нет)
		$transitionOption = $_SESSION['transitionOption'];

		if ($transitionOption != 1){ // not good
	
			// так как ответили не правильно, то выводим комментарий и правильный ответ
			$temp_id = $_SESSION['ID_question'];
			$temp_idans = $_SESSION['first_answerid'];
			if (array_key_exists('q', $_GET)
                        {
                            $temp_id = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_NUMBER_INT);
                        }
			// получаем параметры неправильного ответа
			$sql = <<<SQL
			SELECT COMPETENCELEVELID, COMMENTARY, RISKLEVELID, FACTOR FROM stat.ALLANSWERS WHERE ALLANSWERS.ID='$temp_idans'
SQL;
			$s_res = $db->go_result_once($sql);
			$temp_copetence_id = $s_res['COMPETENCELEVELID'];
			$question_com = $s_res['COMMENTARY'];
			$temp_risklevel_id = $s_res['RISKLEVELID'];
			$factor_com = $s_res['FACTOR'];
			
			// получаем текст уровня компетенции
			$sql = <<<SQL
			SELECT ID, TITLE FROM stat.COMPETENCELEVEL WHERE COMPETENCELEVEL.ID='$temp_copetence_id'
SQL;
			$s_res = $db->go_result_once($sql);
			$competencelevel_title = $s_res['TITLE'];
			$competencelevel_id = $s_res['ID'];
			
			// получаем текст уровня риска
			$sql = <<<SQL
			SELECT TITLE FROM stat.RISKLEVEL WHERE RISKLEVEL.ID='$temp_risklevel_id'
SQL;
			$s_res = $db->go_result_once($sql);
			$risklevel_title = $s_res['TITLE'];
			
			// отдельно получаем правильный ответ
			$sql = <<<SQL
			SELECT TEXT FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_id' AND ALLANSWERS.COMPETENCELEVELID='21'
SQL;
			$s_res = $db->go_result_once($sql);

			$question_ans = $s_res['TEXT'];
		}else{

			$competencelevel_title = "Компетентен";
			$question_com = "Вы ответили правильно!";
			$risklevel_title = "";
			$factor_com = "";
			$question_ans = '';
			$competencelevel_id = 21;
		}
		
		$type_examiner = "PE";
	// это контроль компетентности
	}else if($type_exam == 2){

		$type_examiner = "CC";
		
		$question_com = "Статистика теста";
		$question_ans = '';
		$transitionOption = 1;
		$competencelevel_title = "";
		$risklevel_title = "";
		$factor_com = "";
		$competencelevel_id = 21;
			
		// выводим статистику ответов
		//print_r();
		//echo "<br />";
		//print_r();
		//die();
		//echo "<br />";
		
                
		$smarty->assign("final_array_txt_questions", $_SESSION['final_array_txt_questions']);
		$smarty->assign("final_array_txt_answers", $_SESSION['final_array_txt_answers']);
		
		$smarty->assign("final_array_sf_questions", $_SESSION['final_array_sf_questions']);
		$smarty->assign("final_array_sf_answers", $_SESSION['final_array_sf_answers']);
		
		$smarty->assign("final_array_cv_basic", $_SESSION['final_array_cv_basic']);
		$smarty->assign("final_array_cv_questions", $_SESSION['final_array_cv_questions']);
		$smarty->assign("final_array_cv_answers", $_SESSION['final_array_cv_answers']);
	}else{
		
		die("У меня не прописано, что делать");
	}
}
	
	if (!empty($_POST)){

		// это предсменный экзаменатор
		if($type_exam == 1){
			// выбираем вариант ответа
			if ($transitionOption == 1){
                                
				die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
			}else{
			$returnto = (isset($_GET['q'])) ? "?q={$_GET['q']}" : ""; 
				die('<script>document.location.href= "'.lhost.'/question.php'.$returnto.'"</script>');
			}				
		}else if($type_exam == 2){ // это контроль компетентности
			
			die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
        //var_dump($_POST);
        
	$smarty->assign("error_", $error_);

	$smarty->assign("competencelevel_title", $competencelevel_title);
	$smarty->assign("risklevel_title", $risklevel_title);
	$smarty->assign("factor_com", $factor_com);
	$smarty->assign("type_examiner", $type_examiner);
	$smarty->assign("competencelevel_id", $competencelevel_id);
	$smarty->assign("question_com", $question_com);
	$smarty->assign("question_ans", $question_ans);
	$smarty->assign("transitionOption", $transitionOption);
	
	$smarty->assign("title", "Комментарий");
        if (array_key_exists('q', $_GET))
        {
            $idans = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_NUMBER_INT);   
            $smarty->assign("idans", $idans);
        }
	$smarty->display("commentAnswer.tpl.html");
        
        /*
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
	}*/
 ?>
