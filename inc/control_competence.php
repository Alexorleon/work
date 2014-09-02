<?php
	$typetest = 2;

	// выбираем вариант тестированиЯ (пробное или нет)
	if ($_POST){

		$answer = $_POST['answer'];

		if ($answer == "1"){ // пробное тестирование

			//$_SESSION['qtype'] = 1;
			die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
		}else{ // тестирование

			//$_SESSION['qtype'] = 2;
			die('<script>document.location.href= "'.lhost.'/question.php?qtype=4"</script>');
		}
	}

		$smarty->assign("error_", $error_);

		$smarty->assign("typetest", $typetest);
		$smarty->assign("title", "Контроль компетентности");
		$smarty->display("questions.tpl.html");
?>