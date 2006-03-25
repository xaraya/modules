<?php
/**
 * xarTinyMCE initialization
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce module
 * @link http://xaraya.com/index.php/release/63.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Initialise the tinymce module
 *
 * @access public
 * @param none
 * @returns bool true
 */
function tinymce_init()
{
  /* Set default module vars */
   xarModSetVar('tinymce', 'tinytheme', 'advanced');
   xarModSetVar('tinymce', 'tinylang', 'en');
   xarModSetVar('tinymce', 'tinymode', 'specific_textareas');
   xarModSetVar('tinymce', 'tinyask', 1);
   xarModSetVar('tinymce', 'tinybuttonsremove', '');
   xarModSetVar('tinymce', 'tinyexstyle', 'heading 1=head1,heading 2=head2,heading 3=head3,heading 4=head4');
   xarModSetVar('tinymce', 'tinyextended', '');
   xarModSetVar('tinymce', 'tinyinstances','summary,body');
   xarModSetVar('tinymce', 'tinycsslist','');
   xarModSetVar('tinymce', 'tinytoolbar','top');
   xarModSetVar('tinymce', 'tinyshowpath','bottom');
   xarModSetVar('tinymce', 'tinywidth','');
   xarModSetVar('tinymce', 'tinyheight','');
   xarModSetVar('tinymce', 'tinydirection','ltr');
   xarModSetVar('tinymce', 'tinyencode',0);
   //xarModSetVar('tinymce', 'tinyentities',''); uses the default string if tinyeentity_encoding is named
   xarModSetVar('tinymce', 'tinyentity_encoding','raw');
   xarModSetVar('tinymce', 'tinyinlinestyle',1);
   xarModSetVar('tinymce', 'tinyundolevel',10);
   xarModSetVar('tinymce', 'tinyplugins', 'searchreplace,print,advimage,advlink,table,paste,fullscreen,emotions,liststyle');
   xarModSetVar('tinymce', 'tinybuttons', 'search,replace,pastetext,pasteword');
   xarModSetVar('tinymce', 'tinybuttons2','print,fullscreen,emotions');
   xarModSetVar('tinymce', 'tinybuttons3','liststyle,tablecontrols');
   xarModSetVar('tinymce', 'tinybuild1', '');
   xarModSetVar('tinymce', 'tinybuild2', '');
   xarModSetVar('tinymce', 'tinybuild3', '');
    xarModSetVar('tinymce','tinydate', '');
    xarModSetVar('tinymce','tinytime', '');
    xarModSetVar('tinymce', 'tinybr', 0);
    xarModSetVar('tinymce', 'tinypara', 1);
    xarModSetVar('tinymce', 'tinyinvalid', '');
    xarModSetVar('tinymce', 'tinyadvformat', '');
    //xarModSetVar('tinymce', 'useibrowser', 0);
    xarModSetVar('tinymce', 'tinyeditorcss','');
    //xarModSetVar('tinymce', 'tinynowrap',0);
    xarModSetVar('tinymce', 'tinyloadmode','manual');
    xarModSetVar('tinymce', 'multiconfig','');
    xarModSetVar('tinymce', 'usemulticonfig',0);
    xarModSetVar('tinymce', 'tinyadvresize',1);
    xarModSetVar('tinymce', 'tinytilemap',1);
    xarModSetVar('tinymce', 'tinyenablepath',1);
    xarModSetVar('tinymce', 'tinyresizehorizontal',0);
    xarModSetVar('tinymce', 'tinyeditorselector','mceEditor');
    xarModSetVar('tinymce', 'tinyeditordeselector','');
    xarModSetVar('tinymce', 'tinycompressor',0);
    xarModSetVar('tinymce', 'tinycleanup',1);
    xarModSetVar('tinymce', 'striplinebreaks',1);
    xarModSetVar('tinymce',  'sourceformat',1);
   /* Set masks */
    xarRegisterMask('ViewTinyMCE','All','tinymce','All','All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadTinyMCE','All','tinymce','All','All:All','ACCESS_READ');
    xarRegisterMask('EditTinyMCE','All','tinymce','All','All:All','ACCESS_EDIT');
    xarRegisterMask('AddTinyMCE','All','tinymce','All','All:All','ACCESS_ADD');
    xarRegisterMask('DeleteTinyMCE ','All','tinymce','All','All:All','ACCESS_DELETE');
    xarRegisterMask('AdminTinyMCE','All','tinymce','All','All:All','ACCESS_ADMIN');

    return true;
}

/**
 * Separate activation routines if necessary
 *
 * @access public
 * @param none $
 * @returns bool
 */
function tinymce_activate()
{
    /* Activate successful */
    return true;
}

/**
 * Upgrade the tinymce module from an old version
 *
 * @access public
 * @param oldVersion $
 * @returns bool
 * @raise DATABASE_ERROR
 */
function tinymce_upgrade($oldversion)
{
    switch ($oldversion) {
    case '0.7.0':
        xarModSetVar('tinymce', 'tinyadvresize',1);
        xarModSetVar('tinymce', 'tinytilemap',1);
        xarModSetVar('tinymce', 'tinyenablepath',1);
        xarModSetVar('tinymce', 'tinyresizehorizontal',0);
        return tinymce_upgrade('0.9.0');

    case '0.9.0':
        xarModSetVar('tinymce', 'tinyeditorselector','mceEditor');
        xarModSetVar('tinymce', 'tinyeditordeselector','');
    case '0.9.2':
            return tinymce_upgrade('1.0.0');
    case '1.0.0':
            xarModSetVar('tinymce', 'tinycompressor',0);
            xarModSetVar('tinymce', 'tinycleanup',1);
            xarModDelVar('tinymce', 'useibrowser');
            xarModDelVar('tinymce', 'tinynowrap');
            return tinymce_upgrade('1.0.1');
    case '1.0.1':
         return tinymce_upgrade('1.0.2');
    case '1.0.2':
           xarModSetVar('tinymce', 'tinyentity_encoding','raw');
      return tinymce_upgrade('1.0.3');
    case '1.0.3': //current version
        xarModSetVar('tinymce', 'striplinebreaks',1);
        xarModSetVar('tinymce',  'sourceformat',1);
        break;
    }
    return true;
}

/**
 * Delete the tinymce module
 *
 * @access public
 * @param none $
 * @returns bool true
 */
function tinymce_delete()
{
    xarModDelAllVars('tinymce');
    /* Remove Masks and Instances */
    xarRemoveMasks('tinymce');
    xarRemoveInstances('tinymce');
    return true;
}

?>