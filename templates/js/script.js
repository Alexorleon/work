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

	$("#type_submit").val("2");
	$("#tabnum").val(tab);
	$("#auth").submit();
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
function get_proposals(n, f){

	// если нажали отправить, то получаем значение сообщения из списка
	if(n == 1){
		
		if($("#tabnum").val() != ""){ // не пустое сообщение

			num = f.typemessage.selectedIndex;
			num++;

			$("#typemessage").val(num);
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
