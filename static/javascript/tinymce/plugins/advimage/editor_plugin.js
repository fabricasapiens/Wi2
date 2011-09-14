/**
 * $Id: editor_plugin_src.js 677 2008-03-07 13:52:41Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.AdvancedImagePlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceAdvImage', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1)
					return;

				var site_domain = url.substring(0,url.lastIndexOf("/wi3/")+1) + "wi3/";
				ed.windowManager.open({
					file : site_domain + 'tinymce/images',
					width : 480,
					height : 385,
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('image', {
				title : 'afbeelding invoegen',
				cmd : 'mceAdvImage'
			});
		},

		getInfo : function() {
			return {
				longname : 'Wi3 insert image',
				author : 'Willem Mulder',
				authorurl : 'http://fabricasapiens.nl',
				infourl : 'http://wi3.nl',
				version : '1.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('advimage', tinymce.plugins.AdvancedImagePlugin);
})();
