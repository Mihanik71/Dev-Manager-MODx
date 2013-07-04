<?php
class dataDeveloperManager{
	private $modx;
	private $config = array();
	private $dbTable = array();
	
	public function __construct($modx){
		$this->modx = $modx;
		$this->config['dbname'] 	= $modx->db->config['dbase'];
		$this->config['dbprefix'] 	= $modx->db->config['table_prefix'];
		$this->dbTable['doc'] 		= $this->config['dbprefix'].'site_content';
		$this->dbTable['chunk'] 	= $this->config['dbprefix'].'site_htmlsnippets';
		$this->dbTable['snippet'] 	= $this->config['dbprefix'].'site_snippets';
		$this->dbTable['template'] 	= $this->config['dbprefix'].'site_templates';
		$this->dbTable['tv'] 		= $this->config['dbprefix'].'site_tmplvars';
		$this->dbTable['plugin'] 	= $this->config['dbprefix'].'site_plugins';
		$this->dbTable['categories']= $this->config['dbprefix'].'categories';
		Error_Reporting(E_ERROR);
	}
	public function __destruct(){
		unset($this->config);
		unset($this->dbTable);
		$this->modx->db->disconnect();
	}
	public function getConfig($type, $id, $format = 'html'){
		switch($type){
			case 'doc'		: $result = $this->selectData('*', $this->dbTable['doc'], $id); break;
			case 'chunk'	: $result = $this->selectData('*', $this->dbTable['chunk'], $id); break;
			case 'tv'		: $result = $this->selectData('*', $this->dbTable['tv'], $id); break;
			case 'snippet'	: $result = $this->selectData('*', $this->dbTable['snippet'], $id); break;
			case 'plugin'	: $result = $this->selectData('*', $this->dbTable['plugin'], $id); break;
			case 'template'	: $result = $this->selectData('*', $this->dbTable['template'], $id); break;
		}
		return ($format == 'json')?json_encode($result):$result;
	}
	public function create($type, $fields, $format = 'html'){
		switch($type){
			case 'doc': 
				$fields['content'] = addslashes($fields['content']);
				$result = $this->modx->db->insert($fields, $this->dbTable['doc']);
				break;
			case 'chunk': 
				$fields['snippet'] = addslashes($fields['snippet']);
				$result = $this->modx->db->insert($fields, $this->dbTable['chunk']); 
				break;
			case 'tv': 
				$result = $this->modx->db->insert($fields, $this->dbTable['tv']);
				break;
			case 'snippet': 
				$fields['snippet'] = addslashes($fields['snippet']);
				$result = $this->modx->db->insert($fields, $this->dbTable['snippet']);
				break;
			case 'plugin'	:
				$fields['plugincode'] = addslashes($fields['plugincode']);
				$result = $this->modx->db->insert($fields, $this->dbTable['plugin']);
				break;
			case 'template'	: 
				$fields['content'] = addslashes($fields['content']);
				$result = $this->modx->db->insert($fields, $this->dbTable['template']); 
				break;
		}
		$this->modx->clearCache();
		return ($format == 'json')?json_encode($result):$result;
	}
	public function createCopy($type, $id, $format = 'html'){
		$arr = $this->getConfig($type, $id);
		$arr['id'] = NULL;
		switch($type){
			case 'doc':
				$arr['alias'] = NULL;
				$arr['pagetitle'] = 'Duplicate of '.$arr['pagetitle'];
				break;
			case 'chunk':
			case 'tv':
			case 'snippet':
			case 'plugin':
				$arr['name'] = 'Duplicate of '.$arr['name'];
				break;
			case 'template':
				$arr['templatename'] = 'Duplicate of '.$arr['templatename'];
				break;
		}
		$result = $this->create($type, $arr);
		$this->modx->clearCache();
		return ($format == 'json')?json_encode($result):$result;
	}
	public function delete($type, $id, $format = 'html'){
		switch($type){
			case 'doc'	: $result = $this->modx->db->delete($this->dbTable['doc'], 'id = '.$id); break;
			case 'chunk'	: $result = $this->modx->db->delete($this->dbTable['chunk'], 'id = '.$id); break;
			case 'tv'	: $result = $this->modx->db->delete($this->dbTable['tv'], 'id = '.$id); break;
			case 'snippet'	: $result = $this->modx->db->delete($this->dbTable['snippet'], 'id = '.$id); break;
			case 'plugin'	: $result = $this->modx->db->delete($this->dbTable['plugin'], 'id = '.$id); break;
			case 'template'	: $result = $this->modx->db->delete($this->dbTable['template'], 'id = '.$id); break;
		}
		$this->modx->clearCache();
		return ($format == 'json')?json_encode($result):$result;
	}
	public function update($type, $fields, $id, $format = 'html'){
		switch($type){
			case 'doc'	: $result = $this->modx->db->update($fields, $this->dbTable['doc'], 'id = "'.$id .'"'); break;
			case 'chunk'	: $result = $this->modx->db->update($fields, $this->dbTable['chunk'], 'id = "'.$id .'"'); break;
			case 'tv'	: $result = $this->modx->db->update($fields, $this->dbTable['tv'], 'id = "'.$id .'"'); break;
			case 'snippet'	: $result = $this->modx->db->update($fields, $this->dbTable['snippet'], 'id = "'.$id .'"'); break;
			case 'plugin'	: $result = $this->modx->db->update($fields, $this->dbTable['plugin'], 'id = "'.$id .'"'); break;
			case 'template'	: $result = $this->modx->db->update($fields, $this->dbTable['template'], 'id = "'.$id .'"'); break;
		}
		$this->modx->clearCache();
		return ($format == 'json')?json_encode($result):$result;
	}
	public function getData($type, $id, $format = 'html'){
		switch($type){
			case 'doc'		: 
				$result = $this->selectData('content', $this->dbTable['doc'], $id);
				$result = $result['content'];
				break;
			case 'chunk'	: 
				$result = $this->selectData('snippet', $this->dbTable['chunk'], $id);
				$result = $result['snippet'];
				break;
			case 'snippet'	: 
				$result = $this->selectData('snippet', $this->dbTable['snippet'], $id);
				$result = $result['snippet'];
				break;
			case 'plugin'	: 
				$result = $this->selectData('plugincode', $this->dbTable['plugin'], $id);
				$result = $result['plugincode'];
				break;
			case 'template'	: 
				$result = $this->selectData('content', $this->dbTable['template'], $id);
				$result = $result['content'];
				break;
		}
		return ($format == 'json')?json_encode($result):$result;
	}
	public function getAll($type, $sort = 'id', $format = 'html'){
		switch($type){
			case 'doc'		: $result = $this->selectAll('id, pagetitle, longtitle, published', $this->dbTable['doc'], $sort); break;
			case 'chunk'	: $result = $this->selectAll('id, name, description, category', $this->dbTable['chunk'], $sort); break;
			case 'tv'		: $result = $this->selectAll('id, name, description, category', $this->dbTable['tv'], $sort); break;
			case 'snippet'	: $result = $this->selectAll('id, name, description, category', $this->dbTable['snippet'], $sort); break;
			case 'plugin'	: $result = $this->selectAll('id, name, description, locked, disabled, category', $this->dbTable['plugin'], $sort); break;
			case 'template'	: $result = $this->selectAll('id, templatename, description, category', $this->dbTable['template'], $sort); break;
		}
		return ($format == 'json')?json_encode($result):$result;
	}
	private function selectData($field, $table, $id){
		$result = $this->modx->db->select($field, $table, 'id = '.$id);
		return $this->modx->db->getRow($result);
	}
	private function selectAll($field, $table, $sort){
		$result = $this->modx->db->select($field, $table, '', $sort.' ASC');
		return $this->modx->db->makeArray($result);
	}
	public function selectCategories($format = 'html'){
		$rs = $this->modx->db->select('id,category', $this->dbTable['categories']);
		$qty = mysql_num_rows($rs)+1;
		for ($i = 1; $i < $qty; $i++) $result[$i] = mysql_fetch_assoc($rs);
		return ($format == 'json')?json_encode($result):$result;
	}
}
?>
