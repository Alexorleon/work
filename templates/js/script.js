function go_auth(){
	$("#type_submit").val("1");
	if (($.isNumeric($("#tabnum").val()) == true)){// && ($("#tabnum").val()!="")){
		$("#auth" ).submit();
	}else{
		alert("Введите корректный табельный номер");
	}
}

// предсменный экзаменатор
function go_pre_examiner(){
	//document.location.href = './questions.php';
	$("#type_submit").val("2");
	if (($.isNumeric($("#tabnum").val()) == true)){// && ($("#tabnum").val()!="")){
		$("#auth" ).submit();
	}else{
		alert("Введите корректный табельный номер");
	}
}

// предсменный экзаменатор через поиск сотрудника
function go_pre_examiner_fromsearch(tab){

	window.location="./auth.php?tab="+tab;
}

// контроль компетентности
function go_check_comp(){
	//document.location.href = './questions.php';
	$("#type_submit").val("3");
	if (($.isNumeric($("#tabnum").val()) == true)){// && ($("#tabnum").val()!="")){
		$("#auth" ).submit();
	}else{
		alert("Введите корректный табельный номер");
	}
}

// переходим к поиску по фамилии сотрудника
function go_search_employee(){

	$("#type_submit").val("4");
}

function addToField(n){
	$("#tabnum").val($("#tabnum").val()+n);
}

function delAll(){
	$("#tabnum").val('');
}

function del(){
	$("#tabnum").val($("#tabnum").val().substr(0,$("#tabnum").val().length-1));
}

// выбор варианта ответа
function textAnswer(ans, idans, i){
	$("#numid").val(i);
	$("#comp_lvl_id").val(ans);
	$("#answ_id").val(idans);
	$("#questions").submit();
}

// вариант для кнопки (повторить вопрос или выйти)
function transitionOption(tr){
	$("#commentAns").val(tr);
	$("#commentAnswer").submit();
}

// выбор варианта прохождения теста (пробный или нет)
function typeTest(ans){
	//alert(ans);
	$("#answer").val(ans);
	$("#questions").submit();
}

// index.php главное меню
function go_from_main(n){

	$("#type_submit_main").val(n);
	$("#mainpage" ).submit();
}

// personal data
function get_personal(n){
	$("#type_personal").val(n);
	$("#personalPage").submit();
}

// proposals
function get_proposals(n){

	// если нажали отправить, то получаем значение сообщения из списка
	if(n == 1){
		
		if($("#tabnum").val() != ""){ // не пустое сообщение

			var div = document.getElementById("span_selectBox");
			var spans = div.getElementsByTagName("span");

			/*проверяем выбирали ли значение из списка*/
			if($("#bool_typemessage").val() == 0){
				$("#typemessage").val(1);
			}else{
				$("#typemessage").val();
			}
			
			$("#tabnum").val();
			
			$("#type_proposals").val(n);
			$("#proposalsPage").submit();
		}else{
			alert("Введите сообщение");
		}
	}else{

		$("#type_proposals").val(0);
	}
}

// найти сотрудника по фамилии
function search_employee(){

	if($("#tabnum").val() != ""){ // не пустое сообщение

		$("#tabnum").val();

		$("#search_employeePage").submit();
	}else{
		alert("Введите сообщение");
	}
}

// documents
function get_documents(n, t){
	$("#type_documents").val(n);
	$("#type_doc").val(t);
	$("#documentsPage").submit();
}

// add or edit posts
function get_post(f){

	if($("#postname").val() != ""){ // не пустое сообщение

		num = f.type_specialty.selectedIndex;
		post_id = f.type_specialty.options[num].value;

		$("#type_specialty").val(post_id);
		$("#postname").val();
		$("#edit_post").submit();
	}else{
		
		alert("Введите сообщение");
	}
}

/*
-------------------------------------- BEGIN таймер --------------------------------------
*/
	function go_timer(){
		//console.log("go_timer");	
		intervalId = setInterval("get_sotrud(0);", 5000);
		//clearInterval(intervalId);
	}
	/*function some_func(i){
		console.log(i);
	}*/
	function stop_timers(){
		//console.log("stop_timers");
		clearInterval(intervalId);
	}
	function stop_timers_to(sec){
		//console.log("stop_timers_to_15sec");
		clearInterval(intervalId);
		//intervalId2 = setInterval("go_timer()", 15000);
		setTimeout("go_timer()", sec*1000)
	}
/*
-------------------------------------- END таймер --------------------------------------
*/

/*Выпадающий список*/
function enableSelectBoxes(){
    $('div.selectBox').each(function(){
        $(this).children('span.selected').html($(this).children('div.selectOptions').children('span.selectOption:first').html());
        $(this).attr('value',$(this).children('div.selectOptions').children('span.selectOption:first').attr('value'));
 
        $(this).children('span.selected,span.selectArrow').click(function(){
            if($(this).parent().children('div.selectOptions').css('display') == 'none')
            {
                $(this).parent().children('div.selectOptions').css('display','block');
            }
            else
            {
                $(this).parent().children('div.selectOptions').css('display','none');
            }
        });
 
        $(this).find('span.selectOption').click(function(){
            $(this).parent().css('display','none');
			
			/*запоминаем что выбрали*/
			$("#typemessage").val($(this).attr('value'));
			$("#bool_typemessage").val(1);
			
            $(this).closest('div.selectBox').attr('value',$(this).attr('value'));
            $(this).parent().siblings('span.selected').html($(this).html());
        });
    });
}

/* Кнопка удаления в таблице */
function deleteInTable(n)
{
	alert(n);
    /*if(document.pressed == 'xxx'){
	
        document.mydoc.action ="xxx.asp";
    }else{
		if (document.pressed == 'yyy'){
		
            document.mydoc.action ="yyy.asp";
        }
	}
 
    return true;*/
}