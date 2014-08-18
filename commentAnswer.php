<?php	
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';

//print_r(date('Y-m-d H:i:s')); 2014-08-15 14:02:19

	// как ответили (правильно или нет)
	$transitionOption = $_SESSION['transitionOption'];

	if ($transitionOption != 1){ // not good
		
		// так как ответили не правильно, то выводим комментарий и правильный ответ
		$temp_id = $_SESSION['ID_question'];
		$sql = <<<SQL
		SELECT COMMENTARY FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_id'
SQL;
		$s_res = $db->go_result_once($sql);

		$question_com = $s_res['COMMENTARY'];

		// правильный ответ
		$sql = <<<SQL
		SELECT TEXT FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_id' AND ALLANSWERS.COMPETENCELEVELID='21'
SQL;
		$s_res = $db->go_result_once($sql);

		$question_ans = $s_res['TEXT'];
	}else{

		$question_com = "Вы ответили правильно!";
		$question_ans = '';
	}

	//echo $transOption;
	
	if ($_POST){

		// выбираем вариант ответа
		if ($transitionOption == 1){
				die('<script>document.location.href= "'.lhost.'/index.php"</script>');
			}else{
				die('<script>document.location.href= "'.lhost.'/question.php"</script>');
			}
	}

	$smarty->assign("error_", $error_);

	$smarty->assign("question_com", $question_com);
	$smarty->assign("question_ans", $question_ans);
	$smarty->assign("transitionOption", $transitionOption);

	$smarty->assign("title", "Комментарий");
	$smarty->display("commentAnswer.tpl.html");
 ?>
