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
		
		if ($type_submit == 1){ // нормативные документы
		
			die('<script>document.location.href= "'.lhost.'/documents.php?type_doc=1"</script>');
		}elseif ($type_submit == 2){ // контроль компетентности
			
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
	
	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Главная");

	$smarty->display('main.tpl.html');
?>