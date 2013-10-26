<form>
	<table>
		<tr>
			<td>Заголовок:</td>
			<td><input name="pagetitle" type="text" maxlength="100" value="[+pagetitle+]" class="inputBox" style="width:300px;"></td>
		</tr>
		<tr>
			<td>Расширенный заголовок:</td>
			<td><input name="longtitle" type="text" maxlength="100" value="[+longtitle+]" class="inputBox" style="width:300px;"></td>
		</tr>
		<tr>
			<td>Описание:</td>
			<td><input name="description" type="text" maxlength="100" value="[+description+]" class="inputBox" style="width:300px;"></td>
		</tr>
		<tr>
			<td>Псевдоним:</td>
			<td><input name="alias" type="text" maxlength="100" value="[+alias+]" class="inputBox" style="width:300px;"></td>
		</tr>
		<tr>
			<td>Аннотация:</td>
			<td><textarea name="introtext" class="inputBox" rows="3">[+introtext+]</textarea></td>
		</tr>
		<tr>
			<td>Шаблон:</td>
			<td>[+template+]</td>
		</tr>
		<tr>
			<td>Тип содержимого:</td>
			<td>[+type+]</td>
		</tr>
		<tr>
			<td>Публиковать:</td>
			<td><input type="checkbox" id="published" name="published" class="inputBox" [+published+]></td>
		</tr>
	</table>
</form>