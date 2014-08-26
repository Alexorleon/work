<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
	
	// после выбора варианта, попадаем сюда же и выводим документы по выбору.
	if(isset($_GET['type_comp'])){

		// выводим только новые документы
		if($_GET['type_comp'] == 1){
		
			$typecomp = "new";
			// TODO: 
		}elseif ($_GET['type_comp'] == 2){ // выводим все документы
		
			$typecomp = "all";
			// TODO: 
		}else{
			$typecomp = '';
		}
	}else{
		$typecomp = '';
	}

	// выбираем вариант (новые или все)
	if ($_POST){

		$type_compmodel = $_POST['type_compmodel'];

		if ($type_compmodel == 1){ // новые документы


			die('<script>document.location.href= "'.lhost.'/comp_model.php?type_comp=1"</script>');
		}elseif ($type_compmodel == 2){ // все документы


			die('<script>document.location.href= "'.lhost.'/comp_model.php?type_comp=2"</script>');
		}else{
			
			if($_GET['type_comp'] != 0){

				// переходим обратно к выбору документов
				die('<script>document.location.href= "'.lhost.'/comp_model.php?type_comp=0"</script>');
			}else{
			
				// переходим в лобби
				die('<script>document.location.href= "'.lhost.'/index.php"</script>');
			}
		}
	}
	
	// TODO: запросить у БД есть ли новые документы. т.е. еще не прочитанные.
	
	$smarty->assign("error_", $error_);

	$smarty->assign("typecomp", $typecomp);
	$smarty->assign("title", "Компьютерные модели несчастных случаев");
	$smarty->display("comp_model.tpl.html");
?>