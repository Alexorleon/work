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

function addToField(n){
	$("#tabnum").val($("#tabnum").val()+n);
}

function del(){
	$("#tabnum").val($("#tabnum").val().substr(0,$("#tabnum").val().length-1));
}

// выбор варианта ответа
function textAnswer(ans){
	//alert(ans);
	$("#answer").val(ans);
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

