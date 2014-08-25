<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 

	if ((!isset($_SESSION['sotrud_id'])) or (empty($_SESSION['sotrud_id'])))
	{
		die('<script>document.location.href= "'.lhost.'/auth.php"</script>');	
	}
	//$db = new db;//Создаем
	//$db->GetConnect();//ПРоверяем коннект
	$error_='';
	
	if ($_POST){
		
		$type_submit = $_POST['type_submit_main']; // по какой кнопке нажали. выбераем раздел.
		
		if ($type_submit == 1){
			echo "нормативные документы";

		
			//die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}elseif ($type_submit == 2){
			echo "контроль компетентности";

			
			//die('<script>document.location.href= "'.lhost.'/question.php"</script>');
		}elseif ($type_submit == 3){
			echo "видеонструктажи";
		
		
			//die('<script>document.location.href= "'.lhost.'/question.php"</script>');
		}elseif ($type_submit == 4){
			echo "предсменный экзаменатор";

			
			//die('<script>document.location.href= "'.lhost.'/question.php"</script>');
		}elseif ($type_submit == 5){
			echo "Компьютерные модели несчастных случаев";

		
			//die('<script>document.location.href= "'.lhost.'/question.php"</script>');
		}elseif ($type_submit == 6){
			echo "Предложения руководству";

			
			//die('<script>document.location.href= "'.lhost.'/question.php"</script>');
		}elseif ($type_submit == 7){
			echo "Личные данные";
		
		
			//die('<script>document.location.href= "'.lhost.'/check_comp.html"</script>');
		}else{

			die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
		}
	}
	
	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Главная");

	$smarty->display('main.tpl.html');
?>