/**
 * @author Jo Dalle Nogare based on original codehighlighting  by Nawaf M Al Badia
 * @version 1.1 20 Sept 2008
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('dphighlight');

	tinymce.create('tinymce.plugins.dpHighlight', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {

			ed.addCommand('mceadddpHighlight', function() {
				ed.windowManager.open({
					file : url + '/dphighlight.htm',
					width : 530 + ed.getLang('dphighlight.delta_width', 0),
					height : 500 + ed.getLang('dphighlight.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register example button
			ed.addButton("dphighlight", {
				title : "dphighlight.desc",
				cmd : "mceadddpHighlight",
				image : url + "/img/dphighlight.gif"
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('dphighlight', n.nodeName == 'IMG');
			});
		},
			/**
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'dpHighlight',
				author : 'Jo Dalle Nogare',
				authorurl : 'http://xarigami.com/project/xarigami_dphighlight',
				infourl : 'http://xarigami.com/project/xarigami_dphighlight',
				version : "1.1 Xarigami"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('dphighlight', tinymce.plugins.dpHighlight);
})();