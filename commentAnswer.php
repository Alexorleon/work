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
			if (array_key_exists('q', $_GET))
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

                $final_price = 0;
		foreach($_SESSION['final_array_txt_answers'] as $answer)
                {
                    $final_price += $answer['Price'];
                }
                foreach($_SESSION['final_array_sf_answers'] as $answer)
                {
                    $final_price += $answer['Price'];
                }
                foreach($_SESSION['final_array_cv_answers'] as $answer)
                {
                    foreach($answer as $subanswer)
                    {
                        $final_price += $subanswer['Price'];
                    }
                }
                
                $sql_module = "SELECT ID, TITLE FROM stat.MODULE";
                $res_module = $db->go_result($sql_module);
                
                
                foreach ($res_module as $mkey=>$module)
                {
                    $module_price = 0;
                    foreach($_SESSION['final_array_txt_answers'] as $key=>$answer)
                    {
                        if ($_SESSION['final_array_txt_questions'][$key]['Module'] == $module['ID'])
                        {
                            $module_price += $answer['Price'];
                        }
                    }
                    foreach($_SESSION['final_array_sf_answers'] as $key=>$answer)
                    {
                        if ($_SESSION['final_array_sf_questions'][$key]['Module'] == $module['ID'])
                        {
                            $module_price += $answer['Price'];
                        }
                    }
                    foreach($_SESSION['final_array_cv_answers'] as $key=>$answer)
                    {
                        foreach($answer as $subkey=>$subanswer)
                        {
                            if ($_SESSION['final_array_cv_questions'][$key][$subkey]['Module'] == $module['ID'])
                            {
                                $module_price += $subanswer['Price'];
                            }
                        }
                    }
                    $res_module[$mkey]['Price'] = $module_price;
                }
                
		$smarty->assign("final_array_txt_questions", $_SESSION['final_array_txt_questions']);
		$smarty->assign("final_array_txt_answers", $_SESSION['final_array_txt_answers']);
		
		$smarty->assign("final_array_sf_questions", $_SESSION['final_array_sf_questions']);
		$smarty->assign("final_array_sf_answers", $_SESSION['final_array_sf_answers']);
		
		$smarty->assign("final_array_cv_basic", $_SESSION['final_array_cv_basic']);
		$smarty->assign("final_array_cv_questions", $_SESSION['final_array_cv_questions']);
		$smarty->assign("final_array_cv_answers", $_SESSION['final_array_cv_answers']);
		
                $smarty->assign("final_price", $final_price);
                $smarty->assign("modules", $res_module);
                
		$smarty->assign("sotrud_fam", $_SESSION['sotrud_fam']);
		$smarty->assign("sotrud_im", $_SESSION['sotrud_im']);
		$smarty->assign("sotrud_otch", $_SESSION['sotrud_otch']);
		$smarty->assign("sotrud_dolj", $_SESSION['sotrud_dolj']);
		$smarty->assign("sotrud_tabkadr", $_SESSION['sotrud_tabkadr']);
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
			$returnto = (array_key_exists('q', $_GET)) ? "?q=".filter_input(INPUT_GET, 'q', FILTER_SANITIZE_NUMBER_INT) : ""; 
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
 ?>
