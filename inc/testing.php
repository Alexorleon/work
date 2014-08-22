<?php
	 // тестирование
	сontrol_competence($db);

	$smarty->assign("error_", $error_);

	$smarty->assign("typetest", 3);
	$smarty->assign("title", "Контроль компетентности");
	$smarty->display("questions.tpl.html");
?>