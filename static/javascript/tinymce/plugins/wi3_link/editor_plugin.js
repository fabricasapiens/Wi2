/**
 * $Id: editor_plugin_src.js 677 2008-03-07 13:52:41Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.wi3_link', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('wi3_link', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1)
					return;

				var site_domain = wi3.urlof.wi3;
				ed.windowManager.open({
					file : site_domain + 'tinymce/insertlink',
					width : 480,
					height : 385,
					inline : 1
				}, {
					plugin_url : url
				});
			});
			
    		var site_domain = wi3.urlof.wi3;

			// Register buttons
			ed.addButton('wi3_link', {
				title : 'link invoegen',
				image : site_domain + 'static/javascript/tinymce/plugins/wi3_link/wi3_link.gif',
				cmd : 'wi3_link'
			});
		},

		getInfo : function() {
			return {
				longname : 'Wi3 insert link',
				author : 'Willem Mulder',
				authorurl : 'http://fabricasapiens.nl',
				infourl : 'http://wi3.nl',
				version : '1.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wi3_link', tinymce.plugins.wi3_link);
})();
