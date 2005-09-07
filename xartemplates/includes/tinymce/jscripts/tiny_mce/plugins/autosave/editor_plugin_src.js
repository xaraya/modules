/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('autosave', 'en,sv');

function TinyMCE_autosave_getInfo() {
	return {
		longname : 'Auto save',
		author : 'Moxiecode Systems',
		authorurl : 'http://tinymce.moxiecode.com',
		infourl : 'http://tinymce.moxiecode.com/tinymce/docs/plugin_autosave.html',
		version : '2.0RC1'
	};
};

function TinyMCE_autosave_beforeUnloadHandler() {
	var msg = tinyMCE.getLang("lang_autosave_unload_msg");

	var anyDirty = false;
	for (var n in tinyMCE.instances) {
		var inst = tinyMCE.instances[n];

		if (inst.isDirty())
			return msg;
	}

	return;
}

window.onbeforeunload = TinyMCE_autosave_beforeUnloadHandler;
