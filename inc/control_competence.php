<?php
	$typetest = 2;

	if (!empty($_POST)){

		$answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_NUMBER_INT); //$_POST['answer'];

		if ($answer == 1){

			//$_SESSION['qtype'] = 1;
			$_SESSION['counter_questions'] = 0;
			die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
		}else{

			//$_SESSION['qtype'] = 2;
			$_SESSION['counter_questions'] = 0;
			die('<script>document.location.href= "'.lhost.'/question.php?qtype=4"</script>');
		}
	}

	$smarty->assign("error_", $error_);

	$smarty->assign("typetest", $typetest);
	$smarty->assign("title", "Тип тестирования");
	$smarty->display("questions.tpl.html");
?>