// Docu : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('allbook');
	 
	tinymce.create('tinymce.plugins.allbook', {
		
		init : function(ed, url) {
		// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');

			ed.addCommand('allbook', function() {
				ed.windowManager.open({
					file : url + '/window.php',
					width : 500,
					height : 400,
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('allbook', {
				title : 'Code Colorer',
				cmd : 'allbook',
				image : url + '/allbook_img.png'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('allbook', n.nodeName == 'IMG');
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
					longname  : 'allbook',
					author 	  : 'Nick Remaslinnikov',
					authorurl : 'http://www.homolibere.info',
					infourl   : 'http://www.homolibere.info',
					version   : "0.1 beta"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('allbook', tinymce.plugins.allbook);
})();


