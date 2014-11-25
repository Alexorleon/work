<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
$db = new db;
$db->GetConnect();
$error_='';

$date_begin = filter_input(INPUT_POST, 'date_begin', FILTER_SANITIZE_STRING);
$date_end = filter_input(INPUT_POST, 'date_end', FILTER_SANITIZE_STRING);
$sid = filter_input(INPUT_POST, 'sid', FILTER_SANITIZE_NUMBER_INT);
if ($date_begin && $date_end)
{
    $sql = "SELECT TO_CHAR(ALLHISTORY.DATEBEGIN, 'DD.MM.YYYY HH24:MI:SS') AS DATEBEGIN,
            ALLQUESTIONS.TEXT AS QTEXT, ALLQUESTIONS.SIMPLEPHOTO AS PHOTO, ALLQUESTIONS.SIMPLEVIDEO AS VIDEO,
            ALLANSWERS.TEXT AS ATEXT, ALLANSWERS.PRICE AS PRICE, ALLANSWERS.COMMENTARY AS COMMENTARY, ALLANSWERS.FACTOR as FACTOR,
            MODULE.TITLE AS MTITLE
            FROM stat.ALLHISTORY, stat.ALLQUESTIONS, stat.ALLANSWERS, stat.MODULE
            WHERE (SOTRUD_ID='$sid' AND EXAMINERTYPE='1' AND DEL='N')
                AND (ALLQUESTIONS.ID = ALLHISTORY.ALLQUESTIONSID AND ALLHISTORY.DATEBEGIN>=to_date('$date_begin', 'DD.MM.YYYY HH24:MI:SS') AND ALLHISTORY.DATEBEGIN<=to_date('$date_end', 'DD.MM.YYYY HH24:MI:SS'))
                AND ALLANSWERS.ID=ALLHISTORY.ALLANSWERSID AND MODULE.ID=ALLQUESTIONS.MODULEID
            ORDER BY ALLHISTORY.DATEBEGIN, MODULE.ID";
    $PEResults = $db->go_result($sql);
    
    if (count($PEResults)>0)
    {
        $price = 0;
        $wrongs = 0;
        $amount = count($PEResults);
    ?>
<table class="simple-little-table" id="test_results_dir" cellspacing='0'>
    <tr>
        <th>Дата</th>
        <th>Модуль</th>
        <th>Вопрос</th>
        <th>Ответ</th>
        <th>Комментарий</th>
        <th>Штраф</th>
    </tr>
    <?php
    foreach($PEResults as $result)
    {
        if ($result['PRICE']!=0)
        {
            $cls = "class=wrong_ans";
            $comment = $result['COMMENTARY']." ".$result['FACTOR'];
            $wrongs++;
        }
        else
        {
            $cls="";
            $comment = "Отвечено верно";
        }
    ?>
    <tr <?=$cls?>>
        <td>
            <?=$result['DATEBEGIN']?>
        </td>
         <td>
            <?=$result['MTITLE']?>
        </td>
        <td>
            <?=$result['QTEXT']?>
            <?php
            if ($result['PHOTO']!='')
            {
            ?>
            <br><a class="fancybox" rel="group" href="/storage/photo_questions/<?=$result['PHOTO']?>">[Посмотреть фото]</a>
            <?php
            }
            ?>
        </td>
        <td>
            <?=$result['ATEXT']?>
        </td>
        <td>
            <?=$comment?>
        </td>
        <td>
            <?=$result['PRICE']?>
        </td>
    </tr>
    <?php
    $price+=$result['PRICE'];
    }
    ?>
</table>
<div>
<br>
<canvas id="peChart" width="800" height="400"></canvas>
</div>
<script>
    var ctx = $('#peChart').get(0).getContext("2d");
    var chartOptions = {segmentShowStroke : true, animationSteps : 1};
    var data = [
        {
            value: <?=$wrongs?>,
            color:"#F7464A",
            highlight: "#8E2323",
            label: "Неправильные ответы"
        },
        {
            value: <?=($amount-$wrongs)?>,
            color: "#A9D0F5",
            highlight: "#005E9A",
            label: "Правильные ответы"
        }
    ];
    var pieChart = new Chart(ctx).Pie(data, chartOptions);
</script>
    <?php
    }
    else
    {
    ?>
<div>
    Не найдено данных в указанный период! Убеитесь, что правильно выбрали даты.
</div>
    <?php
    }
}