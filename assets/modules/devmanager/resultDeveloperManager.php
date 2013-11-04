<?
class viewDeveloperManager{
    /*Var*/
    private $config = array();
    /*Public*/
    public function __construct($modx){
        $this->config['theme'] 	= $modx->config['manager_theme'];
        $this->config['basePath'] 	= $modx->config['base_path'];
        $this->config['modulePath'] = $modx->config['base_path'].'assets/modules/devmanager/';
    }
    public function __destruct(){
        unset($this->config);
    }
    public function printPage(){
        return $this->processTemplate('page.tpl',array('theme'=>$this->config['theme']));
    }
    public function printAll($type,$arr){
        $result = '';
        switch($type){
            case 'doc':
                $result = $this->view_tree($arr,0);
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
    public function printAllCategory($type,$arr,$category){
        $result = '';
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
            $catName = ($category[$key]['category'])?$category[$key]['category']:'Без категории';
            $result .= '<div onclick="spoil(\''.$type.'_category_'.$key.'\');saveSpoil(\''.$type.'_category_'.$key.'\');" class="categories">'.$catName.'</div><div id="'.$type.'_category_'.$key.'" style="display:none;" class="categories_data">'.$value.'</div>';
        }
        return $result;
    }
    public function printConfig($type,$arr,$data_arr){
        $result = '';
        switch($type){
            case 'doc':
                $template = '<select name="template" class="inputBox" style="width:300px;"><option value="0">(blank)</option>';
                foreach($data_arr as $value){
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
                        $type.= "<option value='$value' selected=selected>$value</option>";
                    else
                        $type.= "<option value='$value'>$value</option>";
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
                $result = $this->processTemplate('config/doc.tpl',$params);
                break;
            case 'tv':
                $cat = '<select name="category" class="inputBox" style="width:300px;"><option value="0"> </option>';
                foreach($data_arr as $value){
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
                $result = $this->processTemplate('config/tv.tpl',$params);
                break;
            case 'chunk':
            case 'snippet':
                $cat = '<select name="category" class="inputBox" style="width:300px;"><option value="0"> </option>';
                foreach($data_arr as $value){
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
                $result = $this->processTemplate('config/snippet.tpl',$params);
                break;
            case 'plugin':
                $cat = '<select name="category" class="inputBox" style="width:300px;"><option value="0"> </option>';
                foreach($data_arr as $value){
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
                $result = $this->processTemplate('config/plugin.tpl',$params);
                break;
            case 'template':
                $cat = '<select name="category" class="inputBox" style="width:300px;"><option value="0"> </option>';
                foreach($data_arr as $value){
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
                $result = $this->processTemplate('config/template.tpl',$params);
                break;
        }
        return $result;
    }
    /*Private*/
    private function view_tree($arr, $pid){
        $str = '';
        foreach ($arr as $key => $value){
            if($value['parent']== $pid){
                if($value['isfolder']){
                    $str .= '<img align="absmiddle" style="cursor: pointer;position:relative;margin-left:-5px;" src="media/style/'.$this->config['theme'].'/images/tree/plusnode.gif" onclick="saveSpoil(\'content_tree_doc_block_'.$value['id'].'\');spoil(\'content_tree_doc_block_'.$value['id'].'\');this.src =($(\'content_tree_doc_block_'.$value['id'].'\').style.display == \'none\')?\'media/style/'.$this->config['theme'].'/images/tree/plusnode.gif\':\'media/style/'.$this->config['theme'].'/images/tree/minusnode.gif\'">';
                    $str .= $this->getDocBlock($value);
                    $str .= "<div id='content_tree_doc_block_".$value['id']."' style='display:none;margin-left:18px;'>";
                    $next_level = $this->view_tree($arr,$value['id']);
                    $str .= ($next_level)?$next_level:'<span class="empty">Пусто</span>';
                    $str .= "</div>";
                }else{
                    $str .= $this->getDocBlock($value);
                }
            }
            unset($arr[$key]);
        }
        return $str;
    }
    private function getDocBlock($i){
        $type = 'doc';
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
        return $img.'<a href="#" style="margin:0;background:none;padding:0;" class="'.$dis.'" onclick="viewCode(\''.$type.'\','.$i['id'].',\''.$i['pagetitle'].'\',\''.$i['contentType'].'\');return false;" title="'.$i['longtitle'].'" oncontextmenu="return menu.view(2, event, this, \''.$type.'\', \''.$i['id'].'\',\''.$i['contentType'].'\');">'.$i['pagetitle'].'</a></br>';
    }
    private function processTemplate($tpl,$params){
        $tpl = file_get_contents($this->config['modulePath'].'templates/'.$tpl);
        foreach($params as $key=>$value)
            $tpl = str_replace('[+'.$key.'+]', $value, $tpl);
        return $tpl;
    }
}