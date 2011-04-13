/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

 
/******** Config *********/

//Define changes to default configuration here. For example:
//CKEDITOR.config.language = 'fr';
//CKEDITOR.config.uiColor = '#AADC6E';
CKEDITOR.config.width = '95%';
CKEDITOR.config.resize_minWidth = '95%';
CKEDITOR.config.resize_minHeight = '200';
CKEDITOR.config.resize_maxHeight = '1000';
CKEDITOR.config.toolbarCanCollapse = false;
CKEDITOR.config.toolbar_Full =
[
	['Maximize','-','Source','-','ShowBlocks','-','Preview','-','Templates','-','PasteText','PasteFromWord','Print','-','SpellChecker','Scayt','-','-','Find','Replace','-','SelectAll','RemoveFormat','-','Undo','Redo'],
		'/',
	['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','Anchor','-','Image','-','Bold','Italic','-','Styles'],
   
];

/******** Plugins *********/

//CKEDITOR.plugins.load('pgrfilemanager');