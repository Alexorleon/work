<?php	
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
	if( isset($_SESSION['admin_access']) && $_SESSION['admin_access'] === TRUE){// к примеру пока проверим не на админа, а просто на авторизовавшегося, поле админ можно добавить в узерах, точнее USER_GROUP_ACCESS, если 1 - юзер, если 2 - админ, к примеру
	}else{
		//если не авторизованы, то выкидываем на ивторизацию
		die('<script>document.location.href= "'.lhost.'/login"</script>');
	}
	
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
				
				//unset($_GET['del_postid']);
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