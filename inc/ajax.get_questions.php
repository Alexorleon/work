<?php
//error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
$db = new db;
$db->GetConnect();
$error_='';

$type_question = filter_input(INPUT_POST, 'type_question', FILTER_SANITIZE_NUMBER_INT);
$download_sv = filter_input(INPUT_POST, 'download_sv', FILTER_SANITIZE_STRING);
$download_sf = filter_input(INPUT_POST, 'download_sf', FILTER_SANITIZE_STRING);
$text_answer = filter_input(INPUT_POST, 'text_answer', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
$answer_price = filter_input(INPUT_POST, 'answer_price', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);

// TODO: магические числа
// получаем необходимые данные по типу вопроса
    $smarty->assign('document_root', $_SERVER['DOCUMENT_ROOT']);
    $smarty->assign("download_sv", $download_sv);
    $smarty->assign("download_sf", $download_sf);
    $smarty->assign("text_answer", $text_answer);
    $smarty->assign("answer_price",$answer_price);
switch ($type_question)
{
    case 8: //текстовый вопрос
        $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.get_questions/simple_text.tpl.html");
    break;
    case 9: //Видео-вопрос
        $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.get_questions/simple_video.tpl.html");
    break;
    case 10: //Видеоцепочка
		?>
                   <!-- <input type=file id=download_prolog name=download_prolog accept=video/mp4>
                    <input type=file id=download_epilog name=download_epilog accept=video/mp4>
                    <br>
                    <p><input type=button id=btn_add_field name=btn_add_field value='Добавить вопрос' onclick=add_question()></p>-->
                    <div>
                        <strong>
                            В разработке
                        </strong>
                    </div>
                <?php	
    break;
    case 21: //Фото-вопрос
        $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.get_questions/simple_photo.tpl.html");
    break;
    case 22://Фотоцепочка
		?>
                    <input type=label value='В разработке' readonly>
                <?php	
    break;
}
?>