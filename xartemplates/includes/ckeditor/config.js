/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.width = '95%';
	config.resize_minWidth = '95%';
	config.resize_minHeight = '200';
	config.resize_maxHeight = '1000';
	config.toolbarCanCollapse = false;
	config.toolbar_Full =
[
		['Maximize','-','Source','-','ShowBlocks','-','Preview','-','Templates','-','PasteText','PasteFromWord','Print','-','SpellChecker','Scayt','-','-','Find','Replace','-','SelectAll','RemoveFormat','-','Undo','Redo'],
		'/',
		['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','Anchor','-','Image','-','Bold','Italic','-','Styles'],
   
];
};

CKEDITOR.plugins.load('pgrfilemanager');