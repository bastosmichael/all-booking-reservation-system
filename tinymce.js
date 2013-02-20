function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function insertallbookcode() {

	var tagtext;
	var langname_ddb = document.getElementById('allbook_lang');
	var langname = langname_ddb.value;
	var linenumbers = document.getElementById('allbook_linenumbers').checked;
	var inst = tinyMCE.getInstanceById('content');
	var html = inst.selection.getContent();

	if (linenumbers)
		tagtext = '{' + langname;
	else
		tagtext = '{' + langname;



	window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext+'}'+html+'');
	tinyMCEPopup.editor.execCommand('mceRepaint');
	tinyMCEPopup.close();
	return;
}
