<?php

	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
	$db = new db;
	$db->GetConnect();
	$error_='';
	
	$type_question = filter_input(INPUT_POST, 'typequestion', FILTER_SANITIZE_NUMBER_INT);
	
	// TODO: магические числа
	// получаем необходимые данные по типу вопроса
	switch ($type_question) {
                case 8: //текстовый вопрос
		?>
                <table>
                    <tr>
			<td>Ответ 1:</td>
                        <td>
                            <textarea min-length=2 aria-required="true" name="text_answer[1]" maxlength="150" rows="2" cols="75" required style="resize: none;"  data-validation="length" data-validation-length="max150" data-validation-error-msg="Допустимая длина не более 150 символов!"></textarea>
                        </td>
                        
                    </tr>
                    <tr>
                        <td>Штраф за ответ:</td>
                        <td>
                            <input  class="admin_input_text"  type="text" size="2" name="answer_price[1]" data-validation="number" data-validation-allowing="integer,positive" data-validation-error-msg="Введите целое число, большее или равное нулю"/>
                        </td>
                    </tr>
                    <tr>
			<td>Ответ 2:</td>
                        <td><textarea name="text_answer[2]" maxlength="150" rows="2" cols="75" required style="resize: none;" data-validation="length" data-validation-length="max150" data-validation-error-msg="Допустимая длина не более 150 символов!"></textarea>
                            <!--<input class="field_text_edit" id="text_question" name="text_question" value="{$text_question}">-->
                        </td>
                        
                    </tr>
                    <tr>
                        <td>Штраф за ответ:</td>
                        <td>
                            <input class="admin_input_text" type="text" size="2" name="answer_price[2]" data-validation="number" data-validation-allowing="integer,positive" data-validation-error-msg="Введите целое число, большее или равное нулю"/>
                        </td>
                    </tr>
                </table>
                <?php
			break;
                case 9: //Видео-вопрос
                ?>
                    
                    <table>
                        <tr>
                            <td>
                                Видео:
                            </td>
                            <td>
                                <input type=file id=download_sv name=download_sv accept=video/mp4>
                            </td>
                        </tr>
                    <tr>
			<td>Ответ 1:</td>
                        <td>
                            <textarea name="text_answer[1]" maxlength="150" rows="2" cols="75" required style="resize: none;"  data-validation="length" data-validation-length="max150" data-validation-error-msg="Допустимая длина не более 150 символов!"></textarea>
                        </td>
                        
                    </tr>
                    <tr>
                        <td>Штраф за ответ:</td>
                        <td>
                            <input  class="admin_input_text"  type="text" size="2" name="answer_price[1]" data-validation="number" data-validation-allowing="integer,positive" data-validation-error-msg="Введите целое число, большее или равное нулю"/>
                        </td>
                    </tr>
                    <tr>
			<td>Ответ 2:</td>
                        <td><textarea name="text_answer[2]" maxlength="150" rows="2" cols="75" required style="resize: none;"  data-validation="length" data-validation-length="max150" data-validation-error-msg="Допустимая длина не более 150 символов!"></textarea>
                            <!--<input class="field_text_edit" id="text_question" name="text_question" value="{$text_question}">-->
                        </td>
                        
                    </tr>
                    <tr>
                        <td>Штраф за ответ:</td>
                        <td>
                            <input class="admin_input_text" type="text" size="2" name="answer_price[2]" data-validation="number" data-validation-allowing="integer,positive" data-validation-error-msg="Введите целое число, большее или равное нулю"/>
                        </td>
                    </tr>
                </table>
                <?php
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
		?>
                    
                    <table>
                        <tr>
                            <td>
                                Изображение:
                            </td>
                            <td>
                                <input type=file id=download_sf name=download_sf accept=image/png,image/jpeg>
                            </td>
                        </tr>
                    <tr>
			<td>Ответ 1:</td>
                        <td>
                            <textarea name="text_answer[1]" maxlength="150" rows="2" cols="75" required style="resize: none;"  data-validation="number" data-validation-allowing="integer,positive" data-validation-error-msg="Введите целое число, большее или равное нулю"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>Штраф за ответ:</td>
                        <td>
                            <input  class="admin_input_text"  type="text" size="2" name="answer_price[1]" data-validation="number" data-validation-allowing="integer,positive" data-validation-error-msg="Введите целое число, большее или равное нулю"/>
                        </td>
                    </tr>
                    <tr>
			<td>Ответ 2:</td>
                        <td>
                            <textarea name="text_answer[2]" maxlength="150" rows="2" cols="75" required style="resize: none;"  data-validation="number" data-validation-allowing="integer,positive" data-validation-error-msg="Введите целое число, большее или равное нулю"></textarea>
                        </td>
                        
                    </tr>
                    <tr>
                        <td>Штраф за ответ:</td>
                        <td>
                            <input class="admin_input_text" type="text" size="2" name="answer_price[2]" data-validation="number" data-validation-allowing="integer,positive" data-validation-error-msg="Введите целое число, большее или равное нулю"/>
                        </td>
                    </tr>
                </table>
                <?php	
			break;
		case 22://Фотоцепочка
		?>
                    <input type=label value='В разработке' readonly>
                <?php	
			break;
	}
	
	//die($type_question);
?>