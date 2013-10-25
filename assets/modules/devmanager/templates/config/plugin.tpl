<form>
	<table>
		<tr>
			<td>Название:</td>
			<td>
				<input name="name" type="text" maxlength="100" value="[+name+]" class="inputBox" style="width:140px;">
			</td>
		</tr>
		<tr>
			<td>Описание:</td>
			<td>
				<input name="description" type="text" maxlength="255" value="[+description+]" class="inputBox" style="width:300px;">
			</td>
		</tr>
		<tr>
			<td>Категория:</td>
			<td>[+cat+]</td>
		</tr>
		<tr>
			<td>Конфигурация:</td>
			<td>
				<textarea name="properties" class="inputBox">[+properties+]</textarea>
			</td>
		</tr>
		<tr>
			<td>Плагин отключен:</td>
			<td>
				<input type="checkbox" id="disabled" name="disabled" class="inputBox" [+published+]>
			</td>
		</tr>
	</table>
</form>