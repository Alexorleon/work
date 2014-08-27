<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
	
	if(isset($_GET['type_doc'])){

		// выводим только новые документы
		if($_GET['type_doc'] == 1){
		
			print_r("ddddddddddddddddddddddd");
			// TODO: 
		}elseif ($_GET['type_doc'] == 2){ // выводим все документы
		
			print_r("vvvvvvvvvvvvvvvvvvvvv");
			// TODO: 
		}elseif ($_GET['type_doc'] == 3){ // выводим все документы
		
			print_r("mmmmmmmmmmmm");
			// TODO:
		}else{
		}
	}else{
	}
		
	if ($_POST){

		$type_documents = $_POST['type_documents'];

		if ($type_documents == 0){

			// переходим в лобби
			die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}
	}
	
	// TODO: получить историю отправленных сообщений (и показать прочитанные)
	
	$smarty->assign("error_", $error_);

	// TODO: заголовок тоже через иф
	$smarty->assign("title", "Предложения руководству");
	$smarty->display("documents.tpl.html");
?>