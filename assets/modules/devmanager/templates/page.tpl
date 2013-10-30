<!doctype html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="media/style/[+theme+]/style.css" />
	<link rel="stylesheet" type="text/css" href="../assets/modules/devmanager/style.css" />
	<script src="../assets/modules/devmanager/data.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../assets/modules/devmanager/cm/lib/codemirror.css">
	<script src="../assets/modules/devmanager/cm/lib/codemirror-compressed.js"></script>
	<script src="../assets/modules/devmanager/cm/addon-compressed.js"></script>
	<script src="../assets/modules/devmanager/cm/mode/htmlmixed-compressed.js"></script>
	<script src="../assets/modules/devmanager/cm/mode/php-compressed.js"></script>
	<script src="../assets/modules/devmanager/cm/emmet-compressed.js"></script>
	<script src="../assets/modules/devmanager/cm/searchcursor.js"></script>
	<script src="../assets/modules/devmanager/cm/dialog.js"></script>
	<script>
		var theme = '[+theme+]';
		var sorted = 'id';
		var cat = '0';
		var myCodeMirror, req;
		loadLeftBlock();
		top.mainMenu.hideTreeFrame();
		var arrSpoil = new Object();
	</script>

</head>
<body>
	<div class="left">
		<div class="category_panel">
			<img src="media/style/[+theme+]/images/tree/sitemap.png" onclick="viewCategory(this);" title="Показать категории"/>
			<img src="media/style/[+theme+]/images/icons/sort.png" onclick="sort(this);" title="Сортировать по имени"/>
			<img src="media/style/[+theme+]/images/icons/arrow_down.png" onclick="displayAllCategories('');" title="Развернуть всё"/>	
			<img src="media/style/[+theme+]/images/icons/arrow_up.png" onclick="displayAllCategories('none');" title="Свернуть всё"/>
			<img src="media/style/[+theme+]/images/icons/refresh.png" onclick="loadLeftBlock();" title="Обновить"/>
			<img src="media/style/[+theme+]/images/icons/trash.png" onclick="clearCache();" title="Очистить кэш"/>
		</div>
		<div class="cat">
			<div onclick ="spoil('docBlock');" oncontextmenu="return menu.view(1, event, this, 'doc');" class="category">Документы:</div>
			<div id="docBlock" class="spoilCategory" style="display:none;"></div>
			<div onclick = "spoil('chunkBlock');" oncontextmenu="return menu.view(1, event, this, 'chunk');" class="category">Чанки:</div>
			<div id="chunkBlock" class="spoilCategory" style="display:none;"></div>
			<div onclick = "spoil('tvBlock');" oncontextmenu="return menu.view(1, event, this, 'tv');" class="category">TV параметры:</div>
			<div id="tvBlock" class="spoilCategory" style="display:none;"></div>
			<div onclick = "spoil('snippetBlock');" oncontextmenu="return menu.view(1, event, this, 'snippet');" class="category">Сниппеты:</div>
			<div id="snippetBlock" class="spoilCategory" style="display:none;"></div>
			<div onclick = "spoil('pluginBlock');" oncontextmenu="return menu.view(1, event, this, 'plugin');" class="category">Плагины:</div>
			<div id="pluginBlock" class="spoilCategory" style="display:none;"></div>
			<div onclick = "spoil('templateBlock');" oncontextmenu="return menu.view(1, event, this, 'template');" class="category">Шаблоны:</div>
			<div id="templateBlock" class="spoilCategory" style="display:none;"></div>
		</div>
	</div>
	<div id="right">
		<div id="tabs"></div>
		<div id="buttons"></div>
		<div id="data_tabs"></div>
	</div>
	<div id="contextMenu" style="top:0; left:0;display:none;"></div>
	<div id="box" style="display:none;">
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