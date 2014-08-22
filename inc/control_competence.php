<?php
	//echo "контроль компетентности";
	$typetest = 2;

	// выбираем вариант тестировани€ (пробное или нет)
	if ($_POST){

		$answer = $_POST['answer'];

		if ($answer == "1"){ // ѕробное тестирование

			//echo "ѕробное тестирование";
			//$_SESSION['qtype'] = 1;
			die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
		}else{ //“естирование

			//echo "тестирование";
			//$_SESSION['qtype'] = 2;
			die('<script>document.location.href= "'.lhost.'/question.php?qtype=4"</script>');
		}
	}

		$smarty->assign("error_", $error_);

		$smarty->assign("typetest", $typetest);
		$smarty->assign("title", " онтроль компетентности");
		$smarty->display("questions.tpl.html");
		
		// попадаем сюда после выбора теста. чтобы остатьс€ на этой же странице.
?>