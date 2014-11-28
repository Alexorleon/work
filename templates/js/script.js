function go_auth(exam_type){
	$("#type_submit").val(exam_type);
	if (($.isNumeric($("#tabnum").val()) == true)){// && ($("#tabnum").val()!="")){
            $.ajax({
               type:'POST',
               data:{'tabnum': $("#tabnum").val()},
               url:"/inc/ajax.check_tabel.auth.php",
               error: function(req, text, error) {
				$("#auth" ).submit(); //Щито поделать, обойдемся проверкой на серваке
				},
               success: function(data)
               {
                   if (data=="1")
                   {
                       $("#auth" ).submit();
                   }
                   else
                   {
                       $("#tabnum").toastmessage('showErrorToast', 'Такого номера не существует');
                   }
               }
            }); 
	}else{
		$("#tabnum").toastmessage('showErrorToast', 'Введите корректный табельный номер');
                return false;
	}
}

// предсменный экзаменатор через поиск сотрудника
function go_pre_examiner_fromsearch(tab){

	window.location="./auth.php?tab="+tab;
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

//Personal reports by data
function get_report(n,dt)
{
    $('#pers_date').val(dt);
    $('#reptype').val(n);
    $('#mainpage').submit();
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
			alert("Введите сообщение!");
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
		alert("Введите сообщение!");
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

		$("#postname").val();
		$("#edit_post").submit();
	}else{
		
		alert("Введите название должности!");
	}
}

// add or edit employees
function get_employee(f){

	if(($("#employeesur").val() == "") || ($("#employeename").val() == "") || ($("#employeepat").val() == "") || ($("#employeetabel").val() == "")){ // не пустые значения

		alert("Введите все данные!");		
	}else{
	
		num = f.type_doljnost.selectedIndex;
		post_id = f.type_doljnost.options[num].value;

		$("#employee_hidden_id").val();
		$("#employeesur").val();
		$("#employeename").val();
		$("#employeepat").val();
		$("#type_doljnost").val(post_id);
		$("#employeetabel").val();
		$("#edit_employee").submit();
	}
}

// add or edit tests
function get_test(status, f){

	if(($("#testname").val() == "") || ($("#testpenalty").val() == "")){ // не пустые значения

		//alert("Введите все данные!");
		alert($("#chb_t_knowledge").val());
	}else{
	
		switch(status){
		
			case 'add_test':
			break;
		
			case 'save':
			
				//$("#status_edit_test").val(status);
				$("#cur_test_id").val();
			break;
		
			case 'add_question':
			
				//$("#status_edit_test").val(status);
			break;
			
			case 'exit':
			
				//$("#status_edit_test").val(status);
			break;
		}
		
		$("#status_edit_test").val(status);
		$("#testname").val();
		$("#testpenalty").val();
		$("#edit_test").submit();
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
	//$("#del_postid").val();
	//$("#list_posts").submit();
	
    /*if(document.pressed == 'xxx'){
	
        document.mydoc.action ="xxx.asp";
    }else{
		if (document.pressed == 'yyy'){
		
            document.mydoc.action ="yyy.asp";
        }
	}
 
    return true;*/
}