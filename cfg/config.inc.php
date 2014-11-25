<?php
session_start();//чтобы работать с сессиями, на каждой странице их надо стартануть, поэтому они в общем файле, который вяжется к каждому файлигу
	require $_SERVER['DOCUMENT_ROOT']."/libs/Smarty.class.php";
	$smarty = new Smarty;
	
	define('INC_DIR',$_SERVER['DOCUMENT_ROOT'].'/inc/');
	define('lhost','http://'.$_SERVER['HTTP_HOST']);
	
	$smarty->force_compile = true;
	$smarty->debugging = false;
	$smarty->caching = false;// эш тоже выключить пока
	$smarty->cache_lifetime = 120;

	$smarty->assign('lhost','http://'.$_SERVER['HTTP_HOST']);
	$smarty->assign('tpl','http://'.$_SERVER['HTTP_HOST'].'/templates/');
	$smarty->assign('js','http://'.$_SERVER['HTTP_HOST'].'/templates/js/');
	$smarty->assign('photo_questions','http://'.$_SERVER['HTTP_HOST'].'/storage/photo_questions/');
	$smarty->assign('video_questions','http://'.$_SERVER['HTTP_HOST'].'/storage/video_questions/');
	$smarty->assign('picture','http://'.$_SERVER['HTTP_HOST'].'/files/img/');
	//zray_disable();
    require "/db.inc.php";
	require_once(INC_DIR."functions.inc.php");//функции
	require (INC_DIR."function.class.inc.php");//классы
	
	$temp_ub = explode(' ', user_browser());//проверяем версию браузера
	//print_r($temp_ub);
	if ($temp_ub[0]=='IE' && (int)$temp_ub[1]<9){
		$smarty->assign('title','Ваш браузер устарел');
		$smarty->display('old_browser.tpl.html'); 
		die(); 
	}
	
	// init
	
	// параметр для выборки по определенному предприятию
	/*
	10 - Кокс-Майнинг
	2 - СУЭК
	*/
	$predpr_k_glob = "2";
?>