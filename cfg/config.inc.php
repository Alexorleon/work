<?php
session_start();
	require $_SERVER['DOCUMENT_ROOT']."/libs/Smarty.class.php";
	$smarty = new Smarty;
	
	define('INC_DIR',$_SERVER['DOCUMENT_ROOT'].'/inc/');
	define('lhost','http://'.$_SERVER['HTTP_HOST']);
	
	$smarty->force_compile = true;
	$smarty->debugging = false;
	$smarty->caching = false;//Кэш тоже выключить пока
	$smarty->cache_lifetime = 120;

	$smarty->assign('tpl','http://'.$_SERVER['HTTP_HOST'].'/templates/');
	$smarty->assign('js','http://'.$_SERVER['HTTP_HOST'].'/templates/js/');
	zray_disable();
    require "/db.inc.php";
	//require_once(INC_DIR."functions.inc.php");//функции
	require (INC_DIR."function.class.inc.php");//классы
?>