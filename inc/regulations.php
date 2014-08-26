<?php
	//require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	//$db = new db;
	//$db->GetConnect();
	//$error_='';
	
	die("Fuck php");
	// выбираем вариант (новые или все)
	/*if ($_POST){

		$type_regulations = $_POST['type_regulations'];

		if ($type_regulations == 1){ // новые документы


			die('<script>document.location.href= "'.lhost.'/regulations.php?type_reg=1"</script>');
		}elseif ($type_regulations == 2){ // все документы


			die('<script>document.location.href= "'.lhost.'/regulations.php?type_reg=2"</script>');
		}else{
			
			// переходим назад в лобби
			die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}
	}*/
	
	// TODO: запросить у БД есть ли новые документы. т.е. еще не прочитанные.

	// после выбора варианта, попадаем сюда же и выводим документы по выбору.
	/*if(isset($_GET['type_reg'])){

		// выводим только новые документы
		if($_GET['type_reg'] == 1){
		
			$typedoc = "new";
		}elseif ($_GET['type_reg'] == 2){ // выводим все документы
		
			$typedoc = "all";
		}else{
			print_r("aaaaaaaaaaaaa");
			$typedoc = '';
		}
	}else{
	print_r("bbbbbbbbbbbb");
		$typedoc = '';
	}*/

	//$smarty->assign("error_", $error_);

	//$smarty->assign("typedoc", $typedoc);
	//$smarty->assign("title", "Нормативные документы");
	//$smarty->display("regulations.tpl.html");
?>