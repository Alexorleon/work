<?php
	 // ������������
	�ontrol_competence($db);

	$smarty->assign("error_", $error_);

	$smarty->assign("typetest", 3);
	$smarty->assign("title", "�������� ��������������");
	$smarty->display("questions.tpl.html");
?>