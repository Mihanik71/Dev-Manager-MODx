<?php
include('dataDeveloperManager.php');
class DeveloperManager extends dataDeveloperManager{
	private $parameters = array();
	
	public function __construct($modx, $parameters){
		parent::__construct($modx);
		$this->config['theme'] 	= $modx->config['manager_theme'];
		$this->config['basePath'] 	= $modx->config['base_path'];
		$this->config['modulePath'] = $modx->config['base_path'].'assets/modules/devmanager/';
		$this->parameters = $parameters;
	}
	public function __destruct(){
		parent::__destruct();
		unset($this->parameters);
	}
	public function init(){
		if($this->parameters['from'] != 'ajax')
			$this->printPage();
		else
			$this->answer();
	}
	private function answer(){
		switch($this->parameters['func']){
			case 'printAll':
					echo (($this->parameters['cat'] == '1')&&($this->parameters['data'] != 'doc')) ? $this->printAllCategory($this->parameters['data'], $this->parameters['sort']) : $this->printAll($this->parameters['data'], $this->parameters['sort']);
				break;
			case 'createCopy':
				echo $this->createCopy($this->parameters['data'],$this->parameters['DMid']);
				break;
			case 'delete':
				echo $this->delete($this->parameters['data'],$this->parameters['DMid']);
				break;
			case 'new':
				echo $this->delete($this->parameters['data']);
				break;
			case 'printCode':
				echo htmlspecialchars($this->getData($this->parameters['data'],$this->parameters['DMid']));
				break;
			case 'printConfig':
				echo $this->printConfig($this->parameters['data'],$this->parameters['DMid']);
				break;
			case 'saveConfig':
				echo $this->saveConfig($this->parameters['data'],$this->parameters['DMid']);
				break;
			case 'saveData':
				echo $this->saveData($this->parameters['data'],$this->parameters['DMid']);
				break;
			case 'create':
				echo $this->createNew($this->parameters['data']);
				break;
			case 'clearCache':
				echo $this->clearCache();
				break;
		}
	}
	private function printPage(){
		echo <<< HEREDOC
<!doctype html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="media/style/{$this->config['theme']}/style.css" />
	<link rel="stylesheet" type="text/css" href="../assets/modules/devmanager/style.css" />
	<script src="../assets/modules/devmanager/data.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../assets/modules/devmanager/cm/lib/codemirror.css">
	<link rel="stylesheet" href="../assets/modules/devmanager/cm/theme/default.css">
	<script src="../assets/modules/devmanager/cm/lib/codemirror-compressed.js"></script>
	<script src="../assets/modules/devmanager/cm/addon-compressed.js"></script>
	<script src="../assets/modules/devmanager/cm/mode/htmlmixed-compressed.js"></script>
	<script src="../assets/modules/devmanager/cm/mode/php-compressed.js"></script>
	<script src="../assets/modules/devmanager/cm/emmet-compressed.js"></script>
	<script src="../assets/modules/devmanager/cm/searchcursor.js"></script>
	<script src="../assets/modules/devmanager/cm/dialog.js"></script>
	<script>
		var theme = '{$this->config['theme']}';
		var sorted = 'id';
		var cat = '0';
		var myCodeMirror, req;
	</script>
</head>
<body>
	<div class="left">
		<div class="category_panel">
			<img src="media/style/{$this->config['theme']}/images/tree/sitemap.png" onclick="viewCategory(this);" title="Показать категории"/>
			<img src="media/style/{$this->config['theme']}/images/icons/sort.png" onclick="sort(this);" title="Сортировать по имени"/>
			<img src="media/style/{$this->config['theme']}/images/icons/arrow_down.png" onclick="viewAllCategories();" title="Развернуть всё"/>	
			<img src="media/style/{$this->config['theme']}/images/icons/arrow_up.png" onclick="spoilAllCategories();" title="Свернуть всё"/>
			<img src="media/style/{$this->config['theme']}/images/icons/refresh.png" onclick="loadLeftBlock();" title="Обновить"/>
			<img src="media/style/{$this->config['theme']}/images/icons/trash.png" onclick="clearCache();" title="Очистить кэш"/>
		</div>
		<div class="cat">
			<div onclick ="spoil('docBlock');" oncontextmenu="return menu.view(1, event, this, 'doc');" class="category">Документы:</div>
			<div id="docBlock" class="spoilCategory" style="display:none;">
			</div>
			<div onclick = "spoil('chunkBlock');" oncontextmenu="return menu.view(1, event, this, 'chunk');" class="category">Чанки:</div>
			<div id="chunkBlock" class="spoilCategory" style="display:none;">
			</div>
			<div onclick = "spoil('tvBlock');" oncontextmenu="return menu.view(1, event, this, 'tv');" class="category">TV параметры:</div>
			<div id="tvBlock" class="spoilCategory" style="display:none;">
			</div>
			<div onclick = "spoil('snippetBlock');" oncontextmenu="return menu.view(1, event, this, 'snippet');" class="category">Сниппеты:</div>
			<div id="snippetBlock" class="spoilCategory" style="display:none;">
			</div>
			<div onclick = "spoil('pluginBlock');" oncontextmenu="return menu.view(1, event, this, 'plugin');" class="category">Плагины:</div>
			<div id="pluginBlock" class="spoilCategory" style="display:none;">
			</div>
			<div onclick = "spoil('templateBlock');" oncontextmenu="return menu.view(1, event, this, 'template');" class="category">Шаблоны:</div>
			<div id="templateBlock" class="spoilCategory" style="display:none;"></div>
		</div>
	</div>
	<div id="right">
		<div id="tabs">
		</div>
		<div id="buttons"></div>
		<div id="data_tabs">
		</div>
	</div>
	<div id="contextMenu" style="top:0; left:0;display:none;"></div>
	<div id="box"  style="display:none;">
		<div class="bg"></div>
		<div class="menu">
			<div class="content">
				<div class="data" id="data_menu"></div>
				<div class="actionButtons">
					<a src="#" id="saveConfig">Сохранить</a>
					<a src="#" onclick="box.close();">Отмена</a>
				</div>
			</div>
			<div class="close"><img src="../assets/modules/devmanager/images/close_2.png" onclick="box.close();" style="width: 100%;" title="Закрыть"/></div>
		</div>
	</div>
</body>
</html>
HEREDOC;
	}
	private function printAll($type, $par = 'id'){
		$result = '';
		$arr = $this->getAll($type, $par);
		switch($type){
			case 'doc': 
				foreach ($arr as $i){
					$menu = 'oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\');"';
					switch($i['contentType']){
						case 'text/css':
							$img ='<img src="media/style/'.$this->config['theme'].'/images/tree/application_css.png" style="top: 5px;position: relative;margin-right:5px;" title="Контекстное меню" '.$menu.'/>';
						break;
						case 'text/xml':
							$img ='<img src="media/style/'.$this->config['theme'].'/images/tree/application_xml.png" style="top: 5px;position: relative;margin-right:5px;" title="Контекстное меню" '.$menu.'/>';
						break;
						case 'text/javascript':
							$img ='<img src="media/style/'.$this->config['theme'].'/images/tree/application_js.png" style="top: 5px;position: relative;margin-right:5px;" title="Контекстное меню" '.$menu.'/>';
						break;
						case 'text/html':
							$img ='<img src="media/style/'.$this->config['theme'].'/images/tree/application_html.png" style="top: 5px;position: relative;margin-right:5px;" title="Контекстное меню" '.$menu.'/>';
						break;
					}
					$dis = ($i['published'] == '0')?'disabled':'';
					$result .= $img.'<a href="#" style="margin:0;background:none;padding:0;" class="'.$dis.'" onclick="viewCode(\''.$type.'\','.$i['id'].',\''.$i['pagetitle'].'\');return false;" title="'.$i['longtitle'].'" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\');">'.$i['pagetitle'].'</a></br>';
				}
				break;
			case 'tv':
				foreach ($arr as $i)
					$result .= '<a href="#" title="'.$i['description'].'" onclick="box.getConfig(\''.$type.'\', \''.$i['id'].'\');" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\');">'.$i['name'].'</a></br>';
				break;
			case 'chunk':
			case 'snippet':
				foreach ($arr as $i)
					$result .= '<a href="#" onclick="viewCode(\''.$type.'\','.$i['id'].',\''.$i['name'].'\');return false;" title="'.$i['description'].'" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\');">'.$i['name'].'</a></br>';
				break;
			case 'plugin':
				foreach ($arr as $i){
					$dis = ($i['disabled'] == '1')?'disabled':'';
					$result .= '<a href="#" class="'.$dis.'" onclick="viewCode(\''.$type.'\','.$i['id'].',\''.$i['name'].'\');return false;" title="'.$i['description'].'" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\');">'.$i['name'].'</a></br>';
				}
				break;
			case 'template':
				foreach ($arr as $i)
					$result .= '<a href="#" onclick="viewCode(\''.$type.'\','.$i['id'].',\''.$i['templatename'].'\');return false;" title="'.$i['description'].'" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\');">'.$i['templatename'].'</a></br>';
			break;
		}
		return $result;
	}
	private function printAllCategory($type, $par = 'id'){
		$arr = $this->getAll($type, $par);
		$category = $this->selectCategories();
		$arr_str = array();
		switch($type){
			case 'tv':
				foreach ($arr as $i)
					$arr_str[$i['category']] .= '<a href="#" title="'.$i['description'].'" onclick="getConfig(\''.$type.'\', \''.$i['id'].'\');" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\');">'.$i['name'].'</a></br>';
				break;
			case 'chunk':
			case 'snippet':
				foreach ($arr as $i)
					$arr_str[$i['category']] .= '<a href="#" onclick="viewCode(\''.$type.'\','.$i['id'].',\''.$i['name'].'\');return false;" title="'.$i['description'].'" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\');">'.$i['name'].'</a></br>';
				break;
			case 'plugin':
				foreach ($arr as $i){
					$dis = ($i['disabled'] == '1')?'disabled':'';
					$arr_str[$i['category']] .= '<a href="#" class="'.$dis.'" onclick="viewCode(\''.$type.'\','.$i['id'].',\''.$i['name'].'\');return false;" title="'.$i['description'].'" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\');">'.$i['name'].'</a></br>';
				}
				break;
			case 'template':
				foreach ($arr as $i)
					$arr_str[$i['category']] .= '<a href="#" onclick="viewCode(\''.$type.'\','.$i['id'].',\''.$i['templatename'].'\');return false;" title="'.$i['description'].'" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\');">'.$i['templatename'].'</a></br>';
			break;
		}
		foreach ($arr_str as $key => $value){
			$result .= '<div onclick="spoil(\''.$type.'_category_'.$key.'\');" class="categories">'.$category[$key]['category'].'</div><div id="'.$type.'_category_'.$key.'" style="display:none;" class="categories_data">'.$value.'</div>';
		}
		return $result;
	}
	private function printConfig($type, $id, $par = 'id'){
		$result = '';
		$arr = $this->getConfig($type, $id);
		switch($type){
			case 'doc': 
				$template_arr = $this->getAll('template');
				$template = '<select name="template" class="inputBox" style="width:300px;"><option value="0">(blank)</option>';
				foreach($template_arr as $value){
					if($arr['template'] == $value['id'])
						$template.= '<option value="'.$value['id'].'" selected=selected>'.$value['templatename'].'</option>';
					else
						$template.= '<option value="'.$value['id'].'">'.$value['templatename'].'</option>';
				}
				$template .= '</select>';
				$result ='
				<form><table><tr>
					<td>Заголовок:</td>
					<td><input name="pagetitle" type="text" maxlength="100" value="'.$arr['pagetitle'].'" class="inputBox" style="width:300px;"></td>
				</tr>
				<tr>
					<td>Расширенный заголовок:</td>
					<td><input name="longtitle" type="text" maxlength="100" value="'.$arr['longtitle'].'" class="inputBox" style="width:300px;"></td>
				</tr>
				<tr>
					<td>Описание:</td>
					<td><input name="description" type="text" maxlength="100" value="'.$arr['description'].'" class="inputBox" style="width:300px;"></td>
				</tr>
				<tr>
					<td>Псевдоним:</td>
					<td><input name="alias" type="text" maxlength="100" value="'.$arr['alias'].'" class="inputBox" style="width:300px;"></td>
				</tr>
				<tr>
					<td>Аннотация:</td>
					<td><textarea name="introtext" class="inputBox" rows="3"></textarea>'.$arr['introtext'].'</td>
				</tr>
				<tr>
					<td>Шаблон:</td>
					<td>'.$template.'</td>
				</tr>
				</table></form>
				';	
				break;
			case 'tv':
				$cat_arr = $this->selectCategories();
				$cat = '<select name="category" class="inputBox" style="width:300px;"><option value="0"> </option>';
				foreach($cat_arr as $value){
					if($arr['category'] == $value['id'])
						$cat.= '<option value="'.$value['id'].'" selected=selected>'.$value['category'].'</option>';
					else
						$cat.= '<option value="'.$value['id'].'">'.$value['category'].'</option>';
				}
				$cat .= '</select>';
				$result ='
				<form><table><tr>
					<td>Название:</td>
					<td><input name="name" type="text" maxlength="100" value="'.$arr['name'].'" class="inputBox" style="width:140px;"></td>
				</tr>
				<tr>
					<td>Заголовок:</td>
					<td><input name="caption" type="text" maxlength="255" value="'.$arr['caption'].'" class="inputBox" style="width:300px;"></td>
				</tr>
				<tr>
					<td>Описание:</td>
					<td><input name="description" type="text" maxlength="255" value="'.$arr['description'].'" class="inputBox" style="width:300px;"></td>
				</tr>
				<tr>
					<td>Тип ввода:</td>
					<td><input name="type" type="text" maxlength="50" value="'.$arr['type'].'" class="inputBox" style="width:150px;"></td>
				</tr>
				<tr>
					<td>Возможные значения:</td>
					<td><textarea name="elements" maxlength="65535" class="inputBox">'.$arr['elements'].'</textarea></td>
				</tr>
				<tr>
					<td>По умолчанию:</td>
					<td><textarea name="default_text" maxlength="65535" class="inputBox">'.$arr['default_text'].'</textarea></td>
				</tr>
				<tr>
					<td>Отображение:</td>
					<td><input name="display" type="text" maxlength="50" value="'.$arr['display'].'" class="inputBox" style="width:150px;"></td>
				</tr>
				<tr>
					<td>Категория:</td>
					<td>'.$cat.'</td>
				</tr></table></form>
				';
				break;
			case 'chunk':
			case 'snippet':
				$cat_arr = $this->selectCategories();
				$cat = '<select name="category" class="inputBox" style="width:300px;"><option value="0"> </option>';
				foreach($cat_arr as $value){
					if($arr['category'] == $value['id'])
						$cat.= '<option value="'.$value['id'].'" selected=selected>'.$value['category'].'</option>';
					else
						$cat.= '<option value="'.$value['id'].'">'.$value['category'].'</option>';
				}
				$cat .= '</select>';
				$result ='
				<form><table><tr>
					<td>Название:</td>
					<td><input name="name" type="text" maxlength="100" value="'.$arr['name'].'" class="inputBox" style="width:140px;"></td>
				</tr>
				<tr>
					<td>Описание:</td>
					<td><input name="description" type="text" maxlength="255" value="'.$arr['description'].'" class="inputBox" style="width:300px;"></td>
				</tr>
				<tr>
					<td>Категория:</td>
					<td>'.$cat.'</td>
				</tr></table></form>
				';
				break;
			case 'plugin':
				$cat_arr = $this->selectCategories();
				$cat = '<select name="category" class="inputBox" style="width:300px;"><option value="0"> </option>';
				foreach($cat_arr as $value){
					if($arr['category'] == $value['id'])
						$cat.= '<option value="'.$value['id'].'" selected=selected>'.$value['category'].'</option>';
					else
						$cat.= '<option value="'.$value['id'].'">'.$value['category'].'</option>';
				}
				$cat .= '</select>';
				$result ='
				<form><table><tr>
					<td>Название:</td>
					<td><input name="name" type="text" maxlength="100" value="'.$arr['name'].'" class="inputBox" style="width:140px;"></td>
				</tr>
				<tr>
					<td>Описание:</td>
					<td><input name="description" type="text" maxlength="255" value="'.$arr['description'].'" class="inputBox" style="width:300px;"></td>
				</tr>
				<tr>
					<td>Категория:</td>
					<td><input name="category" type="text" maxlength="5" value="'.$arr['category'].'" class="inputBox" style="width:50px;"></td>
				</tr>
				<tr>
					<td>Конфигурация:</td>
					<td>'.$cat.'</td>
				</tr></table></form>
				';
				break;
			case 'template':
				$cat_arr = $this->selectCategories();
				$cat = '<select name="category" class="inputBox" style="width:300px;"><option value="0"> </option>';
				foreach($cat_arr as $value){
					if($arr['category'] == $value['id'])
						$cat.= '<option value="'.$value['id'].'" selected=selected>'.$value['category'].'</option>';
					else
						$cat.= '<option value="'.$value['id'].'">'.$value['category'].'</option>';
				}
				$cat .= '</select>';
				$result ='<form><table><tr>
					<td>Название:</td>
					<td><input name="templatename" type="text" maxlength="100" value="'.$arr['templatename'].'" class="inputBox" style="width:140px;"></td>
				</tr>
				<tr>
					<td>Описание:</td>
					<td><input name="description" type="text" maxlength="255" value="'.$arr['description'].'" class="inputBox" style="width:300px;"></td>
				</tr>
				<tr>
					<td>Категория:</td>
					<td>'.$cat.'</td>
				</tr></table></form>
				';
				break;
		}
		return $result;
	}
	private function saveConfig($type, $id){
		switch($type){
			case 'doc': 
					if(isset($this->parameters['pagetitle']))$fields['pagetitle'] = addslashes($this->parameters['pagetitle']);
					if(isset($this->parameters['description']))$fields['description'] = addslashes($this->parameters['description']);
					if(isset($this->parameters['alias']))$fields['alias'] = addslashes($this->parameters['alias']);
					if(isset($this->parameters['introtext']))$fields['introtext'] = addslashes($this->parameters['introtext']);
					if(isset($this->parameters['template']))$fields['template'] = addslashes($this->parameters['template']);
				break;
			case 'tv':
					if(isset($this->parameters['name']))$fields['name'] = addslashes($this->parameters['name']);
					if(isset($this->parameters['caption']))$fields['caption'] = addslashes($this->parameters['caption']);
					if(isset($this->parameters['description']))$fields['description'] = addslashes($this->parameters['description']);
					if(isset($this->parameters['type']))$fields['type'] = addslashes($this->parameters['type']);
					if(isset($this->parameters['elements']))$fields['elements'] = addslashes($this->parameters['elements']);
					if(isset($this->parameters['default_text']))$fields['default_text'] = addslashes($this->parameters['default_text']);
					if(isset($this->parameters['display']))$fields['display'] = addslashes($this->parameters['display']);
					if(isset($this->parameters['category']))$fields['category'] = addslashes($this->parameters['category']);
				break;
			case 'chunk':
			case 'snippet':
					if(isset($this->parameters['name']))$fields['name'] = addslashes($this->parameters['name']);
					if(isset($this->parameters['description']))$fields['description'] = addslashes($this->parameters['description']);
					if(isset($this->parameters['category']))$fields['category'] = addslashes($this->parameters['category']);
			case 'plugin':
					if(isset($this->parameters['name']))$fields['name'] = addslashes($this->parameters['name']);
					if(isset($this->parameters['description']))$fields['description'] = addslashes($this->parameters['description']);
					if(isset($this->parameters['category']))$fields['category'] = addslashes($this->parameters['category']);
					if(isset($this->parameters['properties']))$fields['properties'] = addslashes($this->parameters['properties']);
				break;
			case 'template':
					if(isset($this->parameters['templatename']))$fields['templatename'] = addslashes($this->parameters['templatename']);
					if(isset($this->parameters['description']))$fields['description'] = addslashes($this->parameters['description']);
					if(isset($this->parameters['category']))$fields['category'] = addslashes($this->parameters['category']);
				break;
		}
		return $this->update($type, $fields, $id);
	}
	private function saveData($type, $id){
		switch($type){
			case 'doc'		: 
				if(isset($this->parameters['content']))$fields['content'] = addslashes($this->parameters['content']);
				break;
			case 'chunk'	: 
			case 'snippet'	: 
				if(isset($this->parameters['content']))$fields['snippet'] = addslashes($this->parameters['content']);
				break;
			case 'plugin'	: 
				if(isset($this->parameters['content']))$fields['plugincode'] = addslashes($this->parameters['content']);
				break;
			case 'template'	: 
				if(isset($this->parameters['content']))$fields['content'] = addslashes($this->parameters['content']);
				break;
		}
		return $this->update($type, $fields, $id);
	}
	private function createNew($type){
		switch($type){
			case 'doc'		: 
				$fields['pagetitle'] = 'New';
				break;
			case 'chunk'	: 
			case 'snippet'	: 
			case 'plugin'	:
			case 'tv'		:
				$fields['name'] = 'New';
				break;
			case 'template'	: 
				$fields['templatename'] = 'New';
				break;
		}
		return $this->create($type, $fields);
	}
}
?>