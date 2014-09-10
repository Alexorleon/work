<?php	

	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 

	$db = new db;
	$db->GetConnect();
	$error_='';
	//print_r($_GET);

	// в зависимости от экзаменатора выводим соответствующую инфу
	if(isset($_GET['qtype'])){

		// это предсменный экзаменатор
		if($_GET['qtype'] == 1){
			require_once($_SERVER['DOCUMENT_ROOT']."/inc/pre_shift_examiner.php");
			
		}else if($_GET['qtype'] == 2){// это контроль компетентности
			require_once($_SERVER['DOCUMENT_ROOT']."/inc/control_competence.php");
			
		}else if($_GET['qtype'] == 3){// это контроль компетентности - пробное тестирование
			require_once($_SERVER['DOCUMENT_ROOT']."/inc/testing.php");
			
		}else if($_GET['qtype'] == 4){// это контроль компетентности - просто тестирование
			//require_once($_SERVER['DOCUMENT_ROOT']."/inc/testing.php");
			require_once($_SERVER['DOCUMENT_ROOT']."/inc/testing.php");
			
		}else{

			//require_once($_SERVER['DOCUMENT_ROOT']."/inc/222.inc.php");
			die("У меня не прописано, что делать");
		}

	}

	//$smarty->assign('predpr', $s_res);
  ?>