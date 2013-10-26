<?php
if(IN_MANAGER_MODE!='true' && !$modx->hasPermission('exec_module')) die('<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the MODX Content Manager instead of accessing this file directly.');

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
		unset($this->parameters);
	}
    private function processTemplate($tpl,$params){
        $tpl = file_get_contents($this->config['modulePath'].'templates/'.$tpl);
        foreach($params as $key=>$value){
            $tpl = str_replace('[+'.$key.'+]', $value, $tpl);
        }
        return $tpl;
    }
    private function printPage(){
        echo $this->processTemplate('page.tpl',array('theme'=>$this->config['theme']));
    }
    public function init(){
        if(isset($this->parameters['from']))
            if($this->parameters['from'] != 'ajax')
                $this->printPage();
            else
                $this->answer();
        else
            $this->printPage();
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
	private function printAll($type, $par = 'id'){
		$result = '';
		if($par=='name'){
			if($type =='doc') $par = 'pagetitle';
			if($type =='template') $par = 'templatename';
		}
		$arr = $this->getAll($type, $par);
		switch($type){
			case 'doc': 
				foreach ($arr as $i){
					if($i['contentType']=='text/html')
						$i['contentType'] = 'htmlmixed';
					$menu = 'oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\',\''.$i['contentType'].'\');"';
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
						default:
							$img ='<img src="media/style/'.$this->config['theme'].'/images/tree/application_html.png" style="top: 5px;position: relative;margin-right:5px;" title="Контекстное меню" '.$menu.'/>';
						break;
					}
					$dis = ($i['published'] == '0')?'disabled':'';
					$result .= $img.'<a href="#" style="margin:0;background:none;padding:0;" class="'.$dis.'" onclick="viewCode(\''.$type.'\','.$i['id'].',\''.$i['pagetitle'].'\',\''.$i['contentType'].'\');return false;" title="'.$i['longtitle'].'" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\',\''.$i['contentType'].'\');">'.$i['pagetitle'].'</a></br>';
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
				$types = array('application/rss+xml','application/pdf','application/vnd.ms-word','application/vnd.ms-excel','text/html','text/css','text/xml','text/javascript','text/plain','application/json');
				$type ='<select name="contentType" class="inputBox" style="width:300px">';
				foreach($types as $value){
					if($arr['contentType'] == $value)
						$type.= '<option value="'.$value.'" selected=selected>'.$value.'</option>';
					else
						$type.= '<option value="'.$value.'">'.$value.'</option>';
				}
				$type .= '</select>';
				$published = ($arr['published']=='0')?'':'checked';
				$params = array(
					'pagetitle'=>$arr['pagetitle'],
					'longtitle'=>$arr['longtitle'],
					'description'=>$arr['description'],
					'introtext'=>$arr['introtext'],
					'alias'=>$arr['alias'],
					'template'=>$template,
					'type'=>$type,
					'published'=>$published
				);
				echo $this->processTemplate('config/doc.tpl',$params);
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
				$cat .= '</select>';
				$types = array('text','textarea','textareamini','richtext','dropdown','listbox','listbox-multiple','option','checkbox','image','file','url','email','number','date','custom_tv');
				$type ='<select name="type" class="inputBox" style="width:300px">';
				foreach($types as $value){
					if($arr['type'] == $value)
						$type.= '<option value="'.$value.'" selected=selected>'.$value.'</option>';
					else
						$type.= '<option value="'.$value.'">'.$value.'</option>';
				}
				$type .= '</select>';
				$params = array(
					'name'=>$arr['name'],
					'caption'=>$arr['caption'],
					'description'=>$arr['description'],
					'type'=>$type,
					'elements'=>$arr['elements'],
					'default_text'=>$arr['default_text'],
					'display'=>$arr['display'],
					'cat'=>$cat
				);
				echo $this->processTemplate('config/tv.tpl',$params);
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
				$params = array(
					'name'=>$arr['name'],
					'description'=>$arr['description'],
					'cat'=>$cat
				);
				echo $this->processTemplate('config/snippet.tpl',$params);
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
				$published = ($arr['disabled']=='0')?'':'checked';
				$params = array(
					'name'=>$arr['name'],
					'description'=>$arr['description'],
					'type'=>$type,
					'cat'=>$cat,
					'properties'=>$arr['properties'],
					'published'=>$published
				);
				echo $this->processTemplate('config/plugin.tpl',$params);
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
				$params = array(
					'templatename'=>$arr['templatename'],
					'description'=>$arr['description'],
					'cat'=>$cat
				);
				echo $this->processTemplate('config/template.tpl',$params);
				break;
		}
		return $result;
	}
	private function slashesArr($arr,$parameters){
		$fields = array();
		foreach($arr as $value){
			if(isset($this->parameters[$value]))
				$fields[$value] = addslashes($this->parameters[$value]);
		}
		return $fields;
	}
	private function saveConfig($type, $id){
		switch($type){
			case 'doc':
					if(isset($this->parameters['pagetitle']))$fields['pagetitle'] = addslashes($this->parameters['pagetitle']);
					if(isset($this->parameters['description']))$fields['description'] = addslashes($this->parameters['description']);
					if(isset($this->parameters['alias']))$fields['alias'] = addslashes($this->parameters['alias']);
					if(isset($this->parameters['introtext']))$fields['introtext'] = addslashes($this->parameters['introtext']);
					if(isset($this->parameters['template']))$fields['template'] = addslashes($this->parameters['template']);
					if(isset($this->parameters['contentType']))$fields['contentType'] = addslashes($this->parameters['contentType']);
					if(isset($this->parameters['published']))$fields['published'] = ($this->parameters['published'] == 'true')?'1':'0';
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
					if(isset($this->parameters['disabled']))$fields['disabled'] = ($this->parameters['disabled'] == 'true')?'1':'0';
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
