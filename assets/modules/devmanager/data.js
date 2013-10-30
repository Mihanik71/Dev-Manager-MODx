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
function saveSpoil(name){
	if(arrSpoil[name]) 
		arrSpoil[name] = 0; 
	else 
		arrSpoil[name] = 1;
}
function reSpoil(){
	for(var i in arrSpoil)
		if(1===arrSpoil[i])
			$(i).style.display = "block";
}
var ajaxClass = (function(){
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
	return function(par, func){
		var req = createRequest();
		if (req){
			req.open("POST", '', true);
			req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			req.send(par);
			req.onreadystatechange = function() {
				if (req.readyState != 4) return;
				if (req.status == 200)
					func(req.responseText);
			}
		}
	}
})();
function viewCM(id, type, lang){
	if(type != 'doc')
		var mode = (type == 'snippet'||type == 'plugin')?'application/x-httpd-php-open':'htmlmixed';
	else
		var mode = lang;
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
			},
			"Ctrl-S": function(cm) {
				saveData(type, id);
			}
		}
	};
	var foldFunc = CodeMirror.newFoldFunction(CodeMirror.tagRangeFinder);
	var myTextArea = $('data_tab_'+type+'_'+id);
	myCodeMirror = (CodeMirror.fromTextArea(myTextArea, config));
	myCodeMirror.focus();
	myCodeMirror.on("gutterClick", function(cm, n) {
		var info = cm.lineInfo(n);
		foldFunc(cm, n);
		cm.setGutterMarker(n, "breakpoints", info.gutterMarkers ? null : makeMarker("+"));
	});
	function makeMarker(str){
		var marker = document.createElement("div");
		marker.style.color = "#822";
		marker.innerHTML = str;
		return marker;
	}
	myCodeMirror.on("change", function(cm, n) {
		$('icon_tab_'+type+'_'+id).setAttribute('src', '../assets/modules/devmanager/images/stat2.png');
		$('data_tab_'+type+'_'+id).innerHTML = myCodeMirror.doc.getValue();
	});
}
function createCopy(str, id, name){
	if(confirm("Bы уверены, что хотите создать копию "+name+"?"))
		var ajax = new ajaxClass('from=ajax&func=createCopy&data='+str+'&DMid='+id, function(result){loadLeftBlock();});
}
function deleteDoc(str, id, name){
	if(confirm("Bы уверены, что хотите удалить "+name+"?")) 
		var ajax = new ajaxClass('from=ajax&func=delete&data='+str+'&DMid='+id, function(result){loadLeftBlock();});
}
var loadLeftBlockCounter = 0;
function loadLeftBlockCounts(n){
	loadLeftBlockCounter++;
	if(n===loadLeftBlockCounter){
		loadLeftBlockCounter = 0;
		reSpoil();
	}
}
function loadLeftBlock(){
	var ajax = new ajaxClass('from=ajax&func=printAll&data=doc&cat='+cat+'&sort='+sorted, function(result){$('docBlock').innerHTML = result;loadLeftBlockCounts(6);});
	var ajax = new ajaxClass('from=ajax&func=printAll&data=chunk&cat='+cat+'&sort='+sorted, function(result){$('chunkBlock').innerHTML = result;loadLeftBlockCounts(6);});
	var ajax = new ajaxClass('from=ajax&func=printAll&data=tv&cat='+cat+'&sort='+sorted, function(result){$('tvBlock').innerHTML = result;loadLeftBlockCounts(6);});
	var ajax = new ajaxClass('from=ajax&func=printAll&data=snippet&cat='+cat+'&sort='+sorted, function(result){$('snippetBlock').innerHTML = result;loadLeftBlockCounts(6);});
	var ajax = new ajaxClass('from=ajax&func=printAll&data=plugin&cat='+cat+'&sort='+sorted, function(result){$('pluginBlock').innerHTML = result;loadLeftBlockCounts(6);});
	var ajax = new ajaxClass('from=ajax&func=printAll&data=template&cat='+cat+'&sort='+sorted, function(result){$('templateBlock').innerHTML = result;reSpoil();loadLeftBlockCounts(6);});
}
function closeTab(elem, name){
	var stat = elem.parentNode.getElementsByClassName('icon_tab')[0].getAttribute('src');
	if (stat == '../assets/modules/devmanager/images/stat2.png'){
		if(confirm("Bы уверены, что хотите закрыть '"+name+"'?")){
			var data = $('data_'+elem.parentNode.id);
			remove(data);
			remove(elem.parentNode);
			var data_active = $byClass('CodeMirror cm-s-default CodeMirror-wrap');
			remove(data_active[0]);
			$('buttons').innerHTML = '';
		}
	}
	else{
		var data = $('data_'+elem.parentNode.id);
		remove(data);
		remove(elem.parentNode);
		var data_active = $byClass('CodeMirror cm-s-default CodeMirror-wrap');
		remove(data_active[0]);
		$('buttons').innerHTML = '';
	}	
}
function viewCode(type, id, name, lang){
	var tab = $('tab_'+type+'_'+id);
	var active = $byClass('active_tab');
	if (active[0] != undefined)
		active[0].className = 'tab';
	var data_active = $byClass('CodeMirror cm-s-default CodeMirror-wrap');
	if (data_active[0] != undefined)
		remove(data_active[0]);
	$('buttons').innerHTML = '<img src="media/style/'+theme+'/images/icons/save.png" onclick="saveData(\''+type+'\','+id+');" title="Сохранить (Ctrl + S)"/>'+
		'<img src="../assets/modules/devmanager/images/undo.png" onclick="myCodeMirror.undo();" title="Отменить (Ctrl + Z)"/>'+
		'<img src="../assets/modules/devmanager/images/redo.png" onclick="myCodeMirror.redo();" title="Повторить  (Ctrl + Y)"/>'+
		'<img src="../assets/modules/devmanager/images/search.png" onclick="search.doSearch(myCodeMirror);" title="Найти"/>'+
		'<img src="../assets/modules/devmanager/images/replase.png" onclick="search.replase(myCodeMirror);" title="Заменить"/>';
	if (tab != undefined){
		tab.className = 'active_tab';
		viewCM(id, type, lang);
	}else{
		$('tabs').innerHTML += '<div class="active_tab" id="'+'tab_'+type+'_'+id+'"><img src="../assets/modules/devmanager/images/stat.png" class="icon_tab" id="icon_tab_'+type+'_'+id+'" onclick="viewCode(\''+type+'\','+id+',\''+name+'\',\''+lang+'\');"/><div onclick="viewCode(\''+type+'\','+id+',\''+name+'\',\''+lang+'\');" style="float:left;padding-top:3px;" oncontextmenu="return menu.view(3, event, this, \''+type+'\', \''+id+'\');">'+name+'</div><img src="media/style/'+theme+'/images/icons/cancel.png" class="close_tab" onclick="closeTab(this,\''+name+'\');" title="Закрыть '+name+'"/></div>';	
		var ajax = new ajaxClass('from=ajax&func=printCode&data='+type+'&DMid='+id, function(result){
			$('data_tabs').innerHTML += '<textarea id="data_tab_'+type+'_'+id+'" style="width: calc(100% - 6px);">'+req.responseText+'</textarea>';
			viewCM(id, type, lang);
		});
	}
}
function saveData(type, id){
	var content = encodeURIComponent(myCodeMirror.doc.getValue());
	var ajax = new ajaxClass('from=ajax&func=saveData&data='+type+'&DMid='+id+'&content='+content, function(result){$('icon_tab_'+type+'_'+id).setAttribute('src', '../assets/modules/devmanager/images/stat.png');});
	$('data_tab_'+type+'_'+id).innerHTML = myCodeMirror.doc.getValue();
}
function displayAllCategories(display){
	var arr = $byClass('spoilCategory');
	for (var i = 0, length = arr.length; i < length; i++)
		if (i in arr)
			arr[i].style.display = display;
	arr = $byClass('categories_data');
	for (var i = 0, length = arr.length; i < length; i++)
		if (i in arr){
			arr[i].style.display = display;
			arrSpoil[arr[i].id] = (('none' == display)?0:1);
		}
	reSpoil();
}
function sort(elem){
	if(sorted == 'id'){sorted = 'name';this.title = 'Сортировать по ID';}
		else{sorted = 'id';this.title = 'Сортировать по имени';}
	loadLeftBlock();
}
function viewCategory(elem){
	if(cat == '1'){cat = '0';elem.title = 'Показать категории';}
		else{cat = '1';elem.title = 'Скрыть категории';}
	loadLeftBlock();
}
function clearCache(){
	var ajax = new ajaxClass('from=ajax&func=clearCache', function(result){loadLeftBlock();});
}
var searchClass = function(){
	var queryDialog = '<img src="../assets/modules/devmanager/images/search.png" style="display:block;margin-top:5px;float:left;margin-right:5px;">Поиск: <input type="text" style="width: 10em"/><span style="color: #888"></span>';
	var replaceQueryDialog = '<img src="../assets/modules/devmanager/images/search.png" style="display:block;margin-top:5px;float:left;margin-right:5px;"> Заменить: <input type="text" style="width: 10em"/>';
	var replacementQueryDialog = '<img src="../assets/modules/devmanager/images/replase.png" style="display:block;margin-top:5px;float:left;margin-right:5px;widht:16px;"> На: <input type="text" style="width: 10em"/>';
	var doReplaceConfirm = "Заменить? <button>Да</button> <button>Нет</button> <button>Отмена</button>";
	function confirmDialog(cm, text, shortText, fs){
		if(cm.openConfirm) cm.openConfirm(text, fs);
		else if(confirm(shortText)) fs[0]();
	}
	function dialog(cm, text, shortText, f) {
		if (cm.openDialog) cm.openDialog(text, f);
		else f(prompt(shortText, ""));
	}
	function searchOverlay(query) {
		if (typeof query == "string") return {token: function(stream){
			if (stream.match(query)) return "searching";
			stream.next();
			stream.skipTo(query.charAt(0)) || stream.skipToEnd();
		}};
		return {token: function(stream) {
		if (stream.match(query)) return "searching";
			while (!stream.eol()){
				stream.next();
				if (stream.match(query, false)) break;
			}
		}};
	}
	function SearchState() {
		this.posFrom = this.posTo = this.query = null;
		this.overlay = null;
	}
	function getSearchState(cm) {
		return cm.state.search || (cm.state.search = new SearchState());
	}
	function getSearchCursor(cm, query, pos) {
		return cm.getSearchCursor(query, pos, typeof query == "string" && query == query.toLowerCase());
	}
	function parseQuery(query) {
		var isRE = query.match(/^\/(.*)\/([a-z]*)$/);
		return isRE ? new RegExp(isRE[1], isRE[2].indexOf("i") == -1 ? "" : "i") : query;
	}
	this.findNext = findNext = function(cm, rev) {cm.operation(function() {
		var state = getSearchState(cm);
		var cursor = getSearchCursor(cm, state.query, rev ? state.posFrom : state.posTo);
		if (!cursor.find(rev)) {
			cursor = getSearchCursor(cm, state.query, rev ? CodeMirror.Pos(cm.lastLine()) : CodeMirror.Pos(cm.firstLine(), 0));
			if (!cursor.find(rev)) return;
		}
		cm.setSelection(cursor.from(), cursor.to());
		state.posFrom = cursor.from(); state.posTo = cursor.to();
	});}
	this.clearSearch = clearSearch = function(cm) {cm.operation(function() {
		var state = getSearchState(cm);
		if (!state.query) return;
		state.query = null;
		cm.removeOverlay(state.overlay);
	});}
	this.doSearch = doSearch = function(cm, rev){
		var state = getSearchState(cm);
		if (state.query) return findNext(cm, rev);
		cm.openDialog(queryDialog, function(query) {
			cm.operation(function() {
				if (!query || state.query) return;
				state.query = parseQuery(query);
				cm.removeOverlay(state.overlay);
				state.overlay = searchOverlay(query);
				cm.addOverlay(state.overlay);
				state.posFrom = state.posTo = cm.getCursor();
				findNext(cm, rev);
			});
		});
	}
	this.replase = replace = function(cm, all){
		dialog(cm, replaceQueryDialog, "Replace:", function(query) {
		  if (!query) return;
		  query = parseQuery(query);
		  dialog(cm, replacementQueryDialog, "Replace with:", function(text) {
			if (all) {
			  cm.operation(function() {
				for (var cursor = getSearchCursor(cm, query); cursor.findNext();) {
				  if (typeof query != "string") {
					var match = cm.getRange(cursor.from(), cursor.to()).match(query);
					cursor.replace(text.replace(/\$(\d)/, function(_, i) {return match[i];}));
				  } else cursor.replace(text);
				}
			  });
			} else {
			  clearSearch(cm);
			  var cursor = getSearchCursor(cm, query, cm.getCursor());
			  var advance = function() {
				var start = cursor.from(), match;
				if (!(match = cursor.findNext())) {
				  cursor = getSearchCursor(cm, query);
				  if (!(match = cursor.findNext()) ||
					  (start && cursor.from().line == start.line && cursor.from().ch == start.ch)) return;
				}
				cm.setSelection(cursor.from(), cursor.to());
				confirmDialog(cm, doReplaceConfirm, "Replace?",
							  [function() {doReplace(match);}, advance]);
			  };
			  var doReplace = function(match) {
				cursor.replace(typeof query == "string" ? text :
							   text.replace(/\$(\d)/, function(_, i) {return match[i];}));
				advance();
			  };
			  advance();
			}
		  });
		});
	}
};
var search = new searchClass();
var menuClass = function(){
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
	function addHandler(object, event, handler, useCapture){if (object.addEventListener){object.addEventListener(event, handler, useCapture ? useCapture : false);}else if (object.attachEvent){object.attachEvent('on' + event, handler);}}
	this.view = function(data, evt, el, type, id, lang){
		evt = evt || window.event;
		evt.cancelBubble = true;
		var menu = $("contextMenu");
		var name = el.innerHTML;
		var html = "<div id='menuName'>"+name+"</div>";
		switch (data) {
			case (1) :
				html += "<div class='menuLink' onclick='createDoc(\""+type+"\");'><img src='media/style/"+theme+"/images/icons/folder_page_add.png'/>Создать новый</div>";
				html += "<div class='menuLink' onclick='spoil(\""+type+"Block\");'><img src='media/style/"+theme+"/images/icons/arrow_down.png'/>Свернуть/развернуть</div>";
			break;
			case (2) :
				if (type != 'tv')
					html += "<div class='menuLink' onclick='viewCode(\""+type+"\","+id+",\""+name+"\",\""+lang+"\");'><img src='media/style/"+theme+"/images/icons/save.png'/>Редактировать</div>";
				html += "<div class='menuLink' onclick='box.getConfig(\""+type+"\", \""+id+"\");'><img src='media/style/"+theme+"/images/icons/information.png'/>Настройки</div>";
				switch (type){
					case ('template') :
						html += "<a class='menuLink' href='index.php?id="+id+"&amp;a=16' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Открыть</a>";
					break;
					case ('doc') :
						html += "<a class='menuLink' href='/index.php?id="+id+"' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Просмотр</a>";
					break;
					case ('tv') :
						html += "<a class='menuLink' href='index.php?id="+id+"&amp;a=301' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Открыть</a>";
					break;
					case ('chunk') :
						html += "<a class='menuLink' href='index.php?id="+id+"&amp;a=78' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Открыть</a>";
					break;
					case ('snippet') :
						html += "<a class='menuLink' href='index.php?id="+id+"&amp;a=22' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Открыть</a>";
					break;
					case ('plugin') :
						html += "<a class='menuLink' href='index.php?id="+id+"&amp;a=102' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Открыть</a>";
					break;
				}
				html += "<div class='menuLink' onclick='createCopy(\""+type+"\", \""+id+"\", \""+name+"\");'><img src='media/style/"+theme+"/images/icons/page_white_copy.png'/>Создать копию</div>";
				html += "<div class='menuLink' onclick='deleteDoc(\""+type+"\", \""+id+"\", \""+name+"\");'><img src='media/style/"+theme+"/images/icons/delete.png'/>Удалить</div>";
			break;
			case (3) :
				html += "<div class='menuLink' onclick='box.getConfig(\""+type+"\", \""+id+"\");'><img src='media/style/"+theme+"/images/icons/information.png'/>Настройки</div>";
				switch (type){
					case ('template') :
						html += "<a class='menuLink' href='index.php?id="+id+"&amp;a=16' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Открыть</a>";
					break;
					case ('doc') :
						html += "<a class='menuLink' href='/index.php?id="+id+"' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Просмотр</a>";
					break;
					case ('chunk') :
						html += "<a class='menuLink' href='index.php?id="+id+"&amp;a=78' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Открыть</a>";
					break;
					case ('snippet') :
						html += "<a class='menuLink' href='index.php?id="+id+"&amp;a=22' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Открыть</a>";
					break;
					case ('plugin') :
						html += "<a class='menuLink' href='index.php?id="+id+"&amp;a=102' target='_blank'><img src='media/style/"+theme+"/images/icons/page_white_magnify.png'/>Открыть</a>";
					break;
				}
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
	addHandler(document, "click", function(){$("contextMenu").style.display = "none";});
};
var menu = new menuClass();
var boxClass = function(){
	this.close = close = function(){$('box').style.display = 'none';}
	this.view = view = function(){$('box').style.display = '';}
	this.saveConfig = saveConfig = function(type, id){
		var arr = $byClass('inputBox');
		var str = 'from=ajax&func=saveConfig&data='+type+'&DMid='+id;
		for (var i = 0, length = arr.length; i < length; i++)
			if (i in arr)
				str += '&'+arr[i].name+'='+encodeURIComponent(arr[i].value||arr[i].innerHTML);
		if(type=='doc') str+='&published='+$('published').checked;
		if(type=='plugin') str+='&disabled='+$('disabled').checked;
		var ajax = new ajaxClass(str, function(result){loadLeftBlock();});
		close();
	}
	this.getConfig = getConfig = function(type, id){
		view();
		var ajax = new ajaxClass('from=ajax&func=printConfig&data='+type+'&DMid='+id, function(result){$('data_menu').innerHTML = result;});
		$('saveConfig').onclick = function(){saveConfig(type, id);}
	}
};
var box = new boxClass();
function createDoc(type){
	var ajax = new ajaxClass('from=ajax&func=create&data='+type, function(result){loadLeftBlock();box.getConfig(type,result);});
}