<?php
//Выводит гуй для редактирования/добавления вопросов
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
$db = new db;
$db->GetConnect();
$error_='';

$type_question = filter_input(INPUT_POST, 'type_question', FILTER_SANITIZE_NUMBER_INT); //Тип вопроса
$download_sv = filter_input(INPUT_POST, 'download_sv', FILTER_SANITIZE_STRING); //Видео к простому вопросу
$download_sf = filter_input(INPUT_POST, 'download_sf', FILTER_SANITIZE_STRING); //Фото к простому вопросу
$text_answer = filter_input(INPUT_POST, 'text_answer', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY); //Текст вопроса
$answer_price = filter_input(INPUT_POST, 'answer_price', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY); //Штраф за неверный ответ (в простом вопросе).... Стоп, что это за хуйня

$chain_questions_title = filter_input(INPUT_POST, 'chain_title', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY); //Массив подвопросов к видеоцепочке
$chain_questions_position = filter_input(INPUT_POST, 'chain_position', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY); //Массив порядковых номеров для подвопросов
$chain_questions_video = filter_input(INPUT_POST, 'chain_video', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY); //Массив видео к подвопросам
$chain_questions_ids = filter_input(INPUT_POST, 'chain_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY); //Массив ID вопросов

$post_json_refresh = filter_input(INPUT_POST, 'json_refresh', FILTER_SANITIZE_STRING); //А тут мы берем хидден-переменную с мега-json-ом внутри. Нет времени объяснять, беги в edit_questions.php смотреть $question_data!

if ($post_json_refresh && $post_json_refresh!='') //Если у нас уже есть такой JSON для данного вопроса (мы переключались между типами вопроса и т.п.)
{
    $question_data = json_decode(urldecode($post_json_refresh), true); //Декодируем и парсим json
}
else //Фактически, данный кусок нужен только для первоначального посещения
{
    $question_data = json_decode(urldecode(filter_input(INPUT_POST, 'question_data', FILTER_SANITIZE_STRING)), true);
    
}

if (count($text_answer)) //Если у нас есть ответы к вопросу - забиваем их в массив
{
    $question_data['answers'][0]['TEXT'] = $text_answer[0];
    $question_data['answers'][0]['PRICE'] = $answer_price[0];
    $question_data['answers'][1]['TEXT'] = $text_answer[1];
    $question_data['answers'][1]['PRICE'] = $answer_price[1];
    $question_data['answers'][2]['TEXT'] = $text_answer[2];
    $question_data['answers'][2]['PRICE'] = $answer_price[2];
}

if ($chain_questions_title && !empty($chain_questions_title)) //Аналогичная штука для подвопросов видеоцепочки
{
    $question_data['chain_questions'] = array();
    foreach($chain_questions_title as $key=>$ch_title)
    {
        $question_data['chain_questions'][$key] = array();
        $question_data['chain_questions'][$key]['TITLE'] = $ch_title;
        $question_data['chain_questions'][$key]['ID'] = $chain_questions_ids[$key];
        $question_data['chain_questions'][$key]['POSITION'] = $chain_questions_position[$key];
        $question_data['chain_questions'][$key]['SIMPLEVIDEO'] = $chain_questions_video[$key];
    }
}

$json_data =  urlencode(json_encode($question_data)); //Сериализуем в json и кодируем получившееся, дабы впихнуть в хидден инпут
// TODO: магические числа
// получаем необходимые данные по типу вопроса
$smarty->assign('document_root', $_SERVER['DOCUMENT_ROOT']);
$smarty->assign("download_sv", $download_sv);
$smarty->assign("download_sf", $download_sf);
$smarty->assign("text_answer", $text_answer);
$smarty->assign("answer_price",$answer_price);
$smarty->assign("question_data", $question_data);
$smarty->assign("json_data", $json_data);

switch ($type_question) //Выбираем, какой гуй отрисовать, в зависимости от типа вопроса
{
    case 8: //текстовый вопрос
        $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.get_questions/simple_text.tpl.html");
    break;
    case 9: //Видео-вопрос
        $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.get_questions/simple_video.tpl.html");
    break;
    case 10: //Видеоцепочка
        $sql ="SELECT ID, TITLE FROM stat.RISKLEVEL ORDER BY ID";
	$array_risklevel = $db->go_result($sql); //В данном случае нам нужен списочек уровней риска
        $smarty->assign("array_risklevel",$array_risklevel);
	$smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.get_questions/complex_video.tpl.html");
    break;
    case 21: //Фото-вопрос
        $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.get_questions/simple_photo.tpl.html");
    break;
    case 22://Сложное фото (не готово)
        ?>
            <input type=label value='В разработке' readonly>
        <?php	
    break;
}
?>