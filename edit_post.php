<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){
		
	}
	
	if(isset($_GET['posttype'])){

		if($_GET['posttype'] == 0){ // это редактирование
		
			
		}else if($_GET['posttype'] == 1){ // это добавление нового
	
			
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	// получаем список всех должностей
	$sql = <<<SQL
	SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K=10
SQL;
	$array_posts = $db->go_result($sql);
	
	
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_posts", $array_posts);

	// TODO: через ИФ режактирование или создание новой
	$smarty->assign("title", "Редактирование должности");
	$smarty->display("edit_post.tpl.html");

	// --- ФУНКЦИИ ---

  ?>