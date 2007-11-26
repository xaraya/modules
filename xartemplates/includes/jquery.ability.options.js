/* DEPRECATED */

/* Full options list. Not the defaults for namespace reasons. */

jQuery('document').ready(function(){  
	jQuery('#accessibility').ability({ 
		textsizer: true, 
		textsizeclasses: ['m', 'l', 'xl', 'xxl'], 
		switcher: true, 
		switcherstyles: ['default.ability.css', 'high-contrast.ability.css'], 
		/* FIXME: This will not work until the base URL is added to styledir, since Xaraya produces absolute URLs for all its stylesheets */
		styledir: "modules/jquery/xarstyles/", 
		savecookie: true, 
		defaultcss: 'default.ability.css' 
	}); 
});
