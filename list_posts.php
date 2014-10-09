<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	
	// инициализация
	$_SESSION['add_or_edit_post'] = 0; // добавление
	
	if($_GET){
		
		if($_GET['del_postid']){
		
			if($_GET['del_postid'] != ''){
			
				$del_postid = $_GET['del_postid']; // id должности
				
				// удаляем должность
				$sql = <<<SQL
				DELETE FROM stat.DOLJNOST WHERE DOLJNOST.KOD='$del_postid'
SQL;
				$db->go_query($sql);
			}
		}
	}
	
	// получаем список всех должностей. 10 - кокс-майнинг
	$sql = <<<SQL
	SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K=10
SQL;
	$array_posts = $db->go_result($sql);
	
	
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_posts", $array_posts);

	$smarty->assign("title", "Список должностей");
	$smarty->display("list_posts.tpl.html");

	// --- ФУНКЦИИ ---

  ?>