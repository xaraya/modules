/* DEPRECATED */

/* jQuery Accessibility Plugin (ability) - A jQuery plugin to provide accessibility functions
 * Author: Tane Piper (digitalspaghetti@gmail.com) 
 * Website: http://code.google.com/p/ability/
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * 
 * === Changelog ===
 * Version 1.0 (20/07/2007)
 * Initial version.
 * Modifies text size to one of 4 CSS styles
 * Change page Style
 * Reset function
 */
(function($) {
	
	function switchStyleSheet(stylename, settings) {
		$('link[@rel*=stylesheet]').each(function(){
			this.disabled = true;
			if (jQuery(this).attr('href') == settings.styledir + stylename) this.disabled = false;
			
			if (settings.savecookie == true){
				jQuery.cookie('style', stylename, 365);
			}
		});
	}
	
	function switchTextSize(size, settings) {
		jQuery('body').removeClass().addClass(size);
		if (settings.savecookie == true){
			jQuery.cookie('textsize', size, 365);
		}
	}
	
	function reset(settings) {
		
		if (settings.textsizer == true) {
			jQuery('body').removeClass();
			jQuery.cookie('textsize', null, {expires: -1});
		}
		
		if (settings.switcher == true) {
			switchStyleSheet(settings.defaultcss, settings);
			jQuery.cookie('style', null, {expires: -1});
		}
	}
	
	$.fn.extend({
	/* ability: function(settings)
	 * The constructor method
	 * Example: $().ability();
	 */
		ability: function(settings) {
			var version = "0.1";
			/* Default Settings*/	
			settings = jQuery.extend({
				textsizer: true,
				textsizeclasses: ['m', 'l', 'xl', 'xxl'],
				switcher: true,
				switcherstyles: ['default.css', 'high-contrast.css'],
				styledir: "/css/",
				savecookie: true,
				defaultcss: 'default.css'
			},settings);
		
			return this.each(function(){
				controlbox = this;
				
				var output = '<div class="ability">';
				var breakline = '<br style="clear:both;" />';
				
				var curstyle = jQuery.cookie('style');
				var curtextsize = jQuery.cookie('textsize');
				
				if (settings.textsizer == true) {
					if (curtextsize) { jQuery('body').removeClass().addClass(curtextsize); }
					var textsizer = '<ul class="fontsize">';
					for (var i=0, len = settings.textsizeclasses.length; i < len; i++) {
						textsizer += '<li><a href="#" rel="' + settings.textsizeclasses[i] + '">' + settings.textsizeclasses[i].toUpperCase() + '</a></li>'
					}
					textsizer += '</ul>';
					output += textsizer + breakline;
				}
				
				if (settings.switcher == true) {
					if (curstyle) { switchStyleSheet(curstyle, settings); }
					var switcher = '<ul class="switcher">';
					for (var i=0, len = settings.switcherstyles.length; i < len; i++) {
						var brokenstring=settings.switcherstyles[i].split(".");
						switcher += '<li><a href="#" rel="' + settings.switcherstyles[i] + '">' + brokenstring[0].toUpperCase() + '</a></li>'
					}
					switcher += '</ul>';
					output += switcher + breakline
				}
				output += '<a href="#" class="reset">Reset</a></div>';
				
				jQuery(controlbox).html(output);
				
				jQuery('ul.fontsize li a').bind('click', function(){
						switchTextSize(jQuery(this).attr('rel'), settings);
						return false;
				});
				jQuery('ul.switcher li a').bind('click', function(){
					switchStyleSheet(jQuery(this).attr('rel'), settings);
					return false;
				});
				jQuery('a.reset').bind('click', function(){
					reset(settings);
					return false;
				});
				
			});
		}
	});
})(jQuery);