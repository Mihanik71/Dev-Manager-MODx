<?php
include('dataDeveloperManager.php');
include('resultDeveloperManager.php');
class DeveloperManager{
    /*Var*/
    private $parameters = array();
    private $view;
    /*Public*/
	public function __construct($modx, $parameters){
		$this->parameters = $parameters;
        $this->view = new viewDeveloperManager($modx);
        $this->data = new dataDeveloperManager($modx);
        $this->init();
	}
	public function __destruct(){
		unset($this->parameters);
        unset($view);
	}
    /*Private*/
    private function init(){
        if(isset($this->parameters['from']))
            if($this->parameters['from'] != 'ajax')
                echo $this->view->printPage();
            else
                $this->answer();
        else
            echo $this->view->printPage();
    }
	private function answer(){
		switch($this->parameters['func']){
			case 'printAll':
				echo (($this->parameters['cat'] == '1')&&($this->parameters['data'] != 'doc')) ? $this->printAllCategory($this->parameters['data'], $this->parameters['sort']) : $this->printAll($this->parameters['data'], $this->parameters['sort']);
				break;
			case 'createCopy':
				echo $this->data->createCopy($this->parameters['data'],$this->parameters['DMid']);
				break;
			case 'delete':
				echo $this->data->delete($this->parameters['data'],$this->parameters['DMid']);
				break;
			case 'printCode':
				echo htmlspecialchars($this->data->getData($this->parameters['data'],$this->parameters['DMid']));
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
				$this->data->clearCache();
				break;
		}
	}
	private function printAll($type, $par = 'id'){
        if($par=='name'){
			if($type =='doc') $par = 'pagetitle';
			if($type =='template') $par = 'templatename';
		}
		$arr = $this->data->getAll($type, $par);
        $result = $this->view->printAll($type,$arr);
		return $result;
	}
	private function printAllCategory($type, $par = 'id'){
		$arr = $this->data->getAll($type, $par);
		$category = $this->data->selectCategories();
		$result = $this->view->printAllCategory($type,$arr,$category);
		return $result;
	}
	private function printConfig($type, $id){
        $result = '';
		$arr = $this->data->getConfig($type, $id);
		switch($type){
			case 'doc': 
				$data_arr = $this->data->getAll('template');
                $result = $this->view->printConfig($type,$arr,$data_arr);
				break;
			case 'tv':
			case 'chunk':
			case 'snippet':
			case 'plugin':
			case 'template':
				$data_arr = $this->data->selectCategories();
                $result = $this->view->printConfig($type,$arr,$data_arr);
				break;
		}
		return $result;
	}
	private function saveConfig($type, $id){
        $fields = array();
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
		return $this->data->update($type, $fields, $id);
	}
	private function saveData($type, $id){
        $fields = array();
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
		return $this->data->update($type, $fields, $id);
	}
	private function createNew($type){
        $fields = array();
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
		return $this->data->create($type, $fields);
	}
}
