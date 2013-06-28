window.onload = function(){
	loadLeftBlock();
}
addHandler(document, "contextmenu", function(){
	$("contextMenu").style.display = "none";
});
addHandler(document, "click", function(){
	$("contextMenu").style.display = "none";
});
function addHandler(object, event, handler, useCapture){
	if (object.addEventListener){
		object.addEventListener(event, handler, useCapture ? useCapture : false);
	}
	else if (object.attachEvent){
		object.attachEvent('on' + event, handler);
	}
}
// min functions
function $(block){
	return document.getElementById(block);
}
function $byClass(block){
	return document.getElementsByClassName(block);
}
function remove(block){
	block.parentNode.removeChild(block);
}
function spoil(block_id){
	var block = $(block_id);
	block.style.display = ("none" == block.style.display)? "block" : "none";
}
// right click menu
function defPosition(event){
	var x = y = 0;
	if (document.attachEvent != null){
		x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
		y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
	}
	else 
	if (!document.attachEvent && document.addEventListener){
		x = event.clientX + window.scrollX;
		y = event.clientY + window.scrollY;
	}
	return {x:x, y:y};
}
function menu(data, evt, el, type, id){
	evt = evt || window.event;
	evt.cancelBubble = true;
	var menu = $("contextMenu");
	var html = "";
	switch (data) {
		case (1) :
			var name = el.innerHTML;
			html = "<div id='menuName'>"+name+"</div>";
			html += "<div class='menuLink' onclick='viewMenu();'><img src='media/style/"+theme+"/images/icons/folder_page_add.png'/>Создать новый</div>";
			html += "<div class='menuLink' onclick='spoil(\""+type+"Block\");'><img src='media/style/"+theme+"/images/icons/arrow_down.png'/>Свернуть/развернуть</div>";
		break;
		case (2) :
			var name = el.innerHTML;
			html = "<div id='menuName'>"+name+"</div>";
			if (type != 'tv')
				html += "<div class='menuLink' onclick='viewCode(\""+type+"\","+id+",\""+name+"\");'><img src='media/style/"+theme+"/images/icons/save.png'/>Радактировать</div>";
			html += "<div class='menuLink' onclick='getConfig(\""+type+"\", \""+id+"\");'><img src='media/style/"+theme+"/images/icons/save.png'/>Настройки</div>";
			html += "<div class='menuLink' onclick='createCopy(\""+type+"\", \""+id+"\", \""+name+"\");'><img src='media/style/"+theme+"/images/icons/page_white_copy.png'/>Создать копию</div>";
			html += "<div class='menuLink' onclick='deleteDoc(\""+type+"\", \""+id+"\", \""+name+"\");'><img src='media/style/"+theme+"/images/icons/delete.png'/>Удалить</div>";
		break;
	}
	if (html){
		menu.innerHTML = html;
		menu.style.top = defPosition(evt).y + "px";
		menu.style.left = defPosition(evt).x + "px";
		menu.style.display = "";
	}
	return false;
}
// ajax
function createRequest(){
	if (window.XMLHttpRequest) req = new XMLHttpRequest();
	else if (window.ActiveXObject){
		try{
			req = new ActiveXObject('Msxml2.XMLHTTP');
		}catch (e){}
		try{
			req = new ActiveXObject('Microsoft.XMLHTTP');
		}catch (e){}
	}
	return req;
}
function printData(parameters, block){
	req = createRequest();
	if (req){
		req.open("POST", '', false);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send(parameters);
		if (req.status == 200)
			$(block).innerHTML = req.responseText;
	}
}
function printCode(parameters, tab_id, type){
	req = createRequest();
	if (req){
		req.open("POST", '', false);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send(parameters);
		if (req.status == 200){
			$('data_tabs').innerHTML += '<textarea id="'+tab_id+'" style="width: calc(100% - 6px);">'+req.responseText+'</textarea>';
			viewCM(tab_id, type);
		}
	}
}
function printConfig(parameters){
	req = createRequest();
	if (req){
		req.open("POST", '', false);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send(parameters);
		if (req.status == 200){
			$('data_menu').innerHTML = req.responseText;
		}
	}
}
function printResult(parameters){
	req = createRequest();
	if (req){
		req.open("POST", '', false);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send(parameters);
		if (req.status == 200)
			loadLeftBlock();
	}
}
//
function viewCM(tab_id, type){
	var mode = (type == 'snippet'||type == 'plugin')?'application/x-httpd-php-open':'htmlmixed';
	CodeMirror.defineMode("MODx-"+mode, function(config, parserConfig) {
		var mustacheOverlay = {
			token: function(stream, state) {
				var ch;
				if (stream.match("[[")) {
					while ((ch = stream.next()) != null)
						if (ch == "?" || (ch == "]"&& stream.next() == "]")) break;
					return "modxSnippet";
				}
				if (stream.match("{{")) {
					while ((ch = stream.next()) != null)
						if (ch == "}" && stream.next() == "}") break;
					stream.eat("}");
					return "modxChunk";
				}
				if (stream.match("[*")) {
					while ((ch = stream.next()) != null)
						if (ch == "*" && stream.next() == "]") break;
					stream.eat("]");
					return "modxTv";
				}
				if (stream.match("[+")) {
					while ((ch = stream.next()) != null)
						if (ch == "+" && stream.next() == "]") break;
					stream.eat("]");
					return "modxPlaceholder";
				}
				if (stream.match("[!")) {
					while ((ch = stream.next()) != null)
						if (ch == "?" || (ch == "!"&& stream.next() == "]")) break;
					return "modxSnippetNoCache";
				}
				if (stream.match("[(")) {
					while ((ch = stream.next()) != null)
						if (ch == ")" && stream.next() == "]") break;
					stream.eat("]");
					return "modxVariable";
				}
				if (stream.match("[~")) {
					while ((ch = stream.next()) != null)
						if (ch == "~" && stream.next() == "]") break;
					stream.eat("]");
					return "modxUrl";
				}
				if (stream.match("[^")) {
					while ((ch = stream.next()) != null)
						if (ch == "^" && stream.next() == "]") break;
					stream.eat("]");
					return "modxConfig";
				}
				if (stream.match("&")) {
					while ((ch = stream.next()) != null)
						if (ch == "=") break;
					stream.eat("=");
					return "attribute";
				}
				if (stream.match("!]")) {
					return "modxSnippet";
				}
				if (stream.match("]]")) {
					return "modxSnippetNoCache";
				}
				while (stream.next() != null && !stream.match("[[", false) && !stream.match("&", false) && !stream.match("{{", false) && !stream.match("[*", false) && !stream.match("[+", false) && !stream.match("[!", false) && !stream.match("[(", false) && !stream.match("[~", false) && !stream.match("[^", false) && !stream.match("!]", false) && !stream.match("]]", false)) {}
				return null;
			}
		};
		return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || mode), mustacheOverlay);
	});
	var config = {
		mode: "MODx-"+mode,
		theme: 'default',
		indentUnit: 4,
		tabSize: 4,
		lineNumbers: true,
		matchBrackets: true,
		lineWrapping: true,
		gutters: ["CodeMirror-linenumbers", "breakpoints"],
		styleActiveLine: true,
		indentWithTabs: true,
		extraKeys:{
			"Ctrl-Space": function(cm){
				var n = cm.getCursor().line;
				var info = cm.lineInfo(n);
				foldFunc(cm, n);
				cm.setGutterMarker(n, "breakpoints", info.gutterMarkers ? null : makeMarker("+"));
			}
		}
	};
	var foldFunc = CodeMirror.newFoldFunction(CodeMirror.tagRangeFinder);
	var myTextArea = $(tab_id);
	myCodeMirror = (CodeMirror.fromTextArea(myTextArea, config));
}
function createCopy(str, id, name){
	if(confirm("Bы уверены, что хотите создать копию "+name+"?"))
		printResult('from=ajax&func=createCopy&data='+str+'&DMid='+id);
}
function deleteDoc(str, id, name){
	if(confirm("Bы уверены, что хотите удалить "+name+"?"))
		printResult('from=ajax&func=delete&data='+str+'&DMid='+id);
}
function loadLeftBlock(){
	printData('from=ajax&func=printAll&data=doc', 'documentBlock');
	printData('from=ajax&func=printAll&data=chunk', 'chunkBlock');
	printData('from=ajax&func=printAll&data=tv', 'TVBlock');
	printData('from=ajax&func=printAll&data=snippet', 'snippetBlock');
	printData('from=ajax&func=printAll&data=plugin', 'pluginBlock');
	printData('from=ajax&func=printAll&data=template', 'templateBlock');
}
function closeTab(elem,name){
	if(confirm("Bы уверены, что хотите закрыть '"+name+"'?")){
		var data = $('data_'+elem.parentNode.id);
		remove(data);
		remove(elem.parentNode);
		var data_active = $byClass('CodeMirror cm-s-default CodeMirror-wrap');
		remove(data_active[0]);
		$('buttons').innerHTML = '';
	}
}
function viewCode(type, id, name){
	var tabs = $('tabs');
	var tab_id = 'tab_'+type+'_'+id;
	var tab = $(tab_id);
	var active = $byClass('active_tab');
	if (active[0] != undefined)
		active[0].className = 'tab';
	var data_active = $byClass('CodeMirror cm-s-default CodeMirror-wrap');
	if (data_active[0] != undefined)
		remove(data_active[0]);
	$('buttons').innerHTML = '<img src="/assets/modules/devmanager/images/save.png" onclick="saveData(\''+type+'\','+id+');" title="Сохранить"/>';
	if (tab != undefined){
		tab.className = 'active_tab';
		viewCM('data_'+tab_id, type);
	}else{
		tabs.innerHTML += '<div class="active_tab" id="'+tab_id+'"><div onclick="viewCode(\''+type+'\','+id+',\''+name+'\')" style="float:left;padding-top:3px;">'+name+'</div><img src="/assets/modules/devmanager/images/close.png" class="close_tab" onclick="closeTab(this,\''+name+'\');" title="Закрыть '+name+'"/></div>';
		printCode('from=ajax&func=printCode&data='+type+'&DMid='+id, 'data_'+tab_id, type);
	}
}
function viewMenu(){
	$('box').style.display = '';
}
function closeMenu(){
	$('box').style.display = 'none';
}
function getConfig(type, id){
	viewMenu();
	printConfig('from=ajax&func=printConfig&data='+type+'&DMid='+id);
	$('saveConfig').onclick = function(){
		saveConfig(type, id);
	}
}
function saveData(type, id){
	var content = encodeURIComponent(myCodeMirror.doc.getValue());
	var str = 'from=ajax&func=saveData&data='+type+'&DMid='+id+'&content='+content;
	printResult(str);
	$('data_tab_'+type+'_'+id).innerHTML = myCodeMirror.doc.getValue();
}
function saveConfig(type, id){
	var arr = $byClass('inputBox');
	var str = 'from=ajax&func=saveConfig&data='+type+'&DMid='+id;
	for (var i = 0, length = arr.length; i < length; i++){
		if (i in arr)
			str += '&'+arr[i].name+'='+encodeURIComponent(arr[i].value||arr[i].innerHTML);
	}
	printResult(str);
	closeMenu();
}