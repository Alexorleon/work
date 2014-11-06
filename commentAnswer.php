<?php	
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';

//print_r(date('Y-m-d H:i:s')); 2014-08-15 14:02:19

if(isset($_GET['type_exam'])){

	// это предсменный экзаменатор
	if($_GET['type_exam'] == 1){
		
		// как ответили (правильно или нет)
		$transitionOption = $_SESSION['transitionOption'];

		if ($transitionOption != 1){ // not good
	
			// так как ответили не правильно, то выводим комментарий и правильный ответ
			$temp_id = $_SESSION['ID_question'];
			$temp_idans = $_SESSION['first_answerid'];
			if (isset($_GET['q']))
                        {
                            $temp_id = $_GET['q'];
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
	}else if($_GET['type_exam'] == 2){

		$type_examiner = "CC";
		
		$question_com = "Статистика теста";
		$question_ans = '';
		$transitionOption = 1;
		
		// выводим статистику ответов
		/*$array_final_qu = array(); // основной массив для ответов
		
		foreach ($q_final_array as $element){
		
			$_SESSION['q_final_array'][] = $element;
		}*/
		/*$array_final_qu = array();
		for($count_i = 0; $count_i < count($_SESSION['q_final_array']); $count_i++){
		
			array_push($array_final_qu, $_SESSION['q_final_array'][$count_i]['ID']);
		}*/
		//print_r($_SESSION['final_array_questions']);
		//echo "<br />";
		//print_r($_SESSION['final_array_answers']);
		//die();
		//echo "<br />";
		$smarty->assign("array_final_qu", $_SESSION['final_array_questions']);
		$smarty->assign("array_final_an", $_SESSION['final_array_answers']);
	}else{
		
		die("У меня не прописано, что делать");
	}
}
	
	if ($_POST){

		// это предсменный экзаменатор
		if($_GET['type_exam'] == 1){
			// выбираем вариант ответа
			if ($transitionOption == 1){
                                
				die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
			}else{
			$returnto = (isset($_GET['q'])) ? "?q={$_GET['q']}" : ""; 
				die('<script>document.location.href= "'.lhost.'/question.php'.$returnto.'"</script>');
			}				
		}else if($_GET['type_exam'] == 2){ // это контроль компетентности
			
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
        if (isset($_GET['q']))
        {
            $idans = $_GET['q'];   
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
