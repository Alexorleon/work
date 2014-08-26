<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
	
	// TODO: запрос всех файлов
	
	// после выбора варианта, попадаем сюда же и выводим инструктажи по выбору.
	if(isset($_GET['type_brif'])){

		// выводим только новые инструктажи
		if($_GET['type_brif'] == 1){
		
			$typedoc = "new";
			// TODO: 
		}elseif ($_GET['type_brif'] == 2){ // выводим все инструктажи
		
			$typedoc = "all";
			// TODO: 
		}else{
			$typedoc = '';
		}
	}else{
		$typedoc = '';
	}

	// выбираем вариант (новые или все)
	if ($_POST){

		$type_briefings = $_POST['type_briefings'];

		if ($type_briefings == 1){ // новые инструктажи


			die('<script>document.location.href= "'.lhost.'/video_briefings.php?type_brif=1"</script>');
		}elseif ($type_briefings == 2){ // все инструктажи


			die('<script>document.location.href= "'.lhost.'/video_briefings.php?type_brif=2"</script>');
		}else{
			
			if($_GET['type_brif'] != 0){

				// переходим обратно к выбору документов
				die('<script>document.location.href= "'.lhost.'/video_briefings.php?type_brif=0"</script>');
			}else{
			
				// переходим в лобби
				die('<script>document.location.href= "'.lhost.'/index.php"</script>');
			}
		}
	}
	
	// TODO: запросить у БД есть ли новые инструктажи. т.е. еще не прочитанные.

	
	$smarty->assign("error_", $error_);

	$smarty->assign("typedoc", $typedoc);
	$smarty->assign("title", "Нормативные инструктажи");
	$smarty->display("video_briefings.tpl.html");
?>