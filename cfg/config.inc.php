<?php
session_start();
	require $_SERVER['DOCUMENT_ROOT']."/libs/Smarty.class.php";
	$smarty = new Smarty;
	
	define('INC_DIR',$_SERVER['DOCUMENT_ROOT'].'/inc/');
	define('lhost','http://'.$_SERVER['HTTP_HOST']);
	
	$smarty->force_compile = true;
	$smarty->debugging = false;
	$smarty->caching = false;//��� ���� ��������� ����
	$smarty->cache_lifetime = 120;

	$smarty->assign('lhost','http://'.$_SERVER['HTTP_HOST']);
	$smarty->assign('tpl','http://'.$_SERVER['HTTP_HOST'].'/templates/');
	$smarty->assign('js','http://'.$_SERVER['HTTP_HOST'].'/templates/js/');
	$smarty->assign('photo','http://'.$_SERVER['HTTP_HOST'].'/storage/photo/');
	zray_disable();
    require "/db.inc.php";
	require_once(INC_DIR."functions.inc.php");//�������
	require (INC_DIR."function.class.inc.php");//������
	
	$temp_ub = explode(' ', user_browser());//��������� ������ ��������
	//print_r($temp_ub);
	if ($temp_ub[0]=='IE' && (int)$temp_ub[1]<10){
		$smarty->assign('title','��� ������� �������');
		$smarty->display('old_browser.tpl.html'); 
		die(); 
	}
?>