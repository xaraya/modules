<?php
/**
 * File: $Id
 *
 * Initialise the tinymce module
 *
* @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage tinymce Module
 * @author Jo Dalle Nogare <jojodee>
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
//Set default module vars
   xarModSetVar('tinymce', 'tinytheme', 'default');
   xarModSetVar('tinymce', 'tinylang', 'en');
   xarModSetVar('tinymce', 'tinymode', 'textareas');
   xarModSetVar('tinymce', 'tinyask', 'true');
   xarModSetVar('tinymce', 'tinybuttonsremove', '');
   xarModSetVar('tinymce', 'tinyexstyle', 'heading 1=head1,heading 2=head2,heading 3=head3,heading 4=head4');
   xarModSetVar('tinymce', 'tinyextended', 'span[*],p[style|id|name],code,pre,blockquote/quote,a[style|id|name|href|target|rel:external|title|onclick],img[style|class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout]');
   xarModSetVar('tinymce', 'tinyinstances','summary,body');
   xarModSetVar('tinymce', 'tinycsslist','modules/tinymce/xarstyles/editor.css');
   xarModSetVar('tinymce', 'tinytoolbar','bottom');
   xarModSetVar('tinymce', 'tinyshowpath','bottom');
   xarModSetVar('tinymce', 'tinywidth','');
   xarModSetVar('tinymce', 'tinyheight','');
   xarModSetVar('tinymce', 'tinydirection','ltr');
   xarModSetVar('tinymce', 'tinyencode','');
   xarModSetVar('tinymce', 'tinyinlinestyle','true');
   xarModSetVar('tinymce', 'tinyundolevel',10);
   xarModSetVar('tinymce', 'tinyplugins', 'emotions,zoom,preview,searchreplace,print,table');
   xarModSetVar('tinymce', 'tinybuttons', 'search,replace');
   xarModSetVar('tinymce', 'tinybuttons2','preview,zoom,print');
   xarModSetVar('tinymce', 'tinybuttons3','tablecontrols,emotions');
   xarModSetVar('tinymce', 'tinybuild1', '');
   xarModSetVar('tinymce', 'tinybuild2', '');
   xarModSetVar('tinymce', 'tinybuild3', '');
    xarModSetVar('tinymce','tinydate', '');
    xarModSetVar('tinymce','tinytime', '');
    xarModSetVar('tinymce', 'tinybr', 'false');
    xarModSetVar('tinymce', 'tinypara', 'true');
    xarModSetVar('tinymce', 'tinyinvalid', '');    
    xarModSetVar('tinymce', 'tinyadvformat', '');
    xarModSetVar('tinymce', 'useibrowser', 0);
    xarModSetVar('tinymce', 'tinyeditorcss','');
    xarModSetVar('tinymce', 'tinynowrap','false');
    xarModSetVar('tinymce', 'tinyloadmode','auto');    
//Set masks
    xarRegisterMask('ViewTinyMCE','All','tinymce','All','All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadTinyMCE','All','tinymce','All','All:All','ACCESS_READ');
    xarRegisterMask('EditTinyMCE','All','tinymce','All','All:All','ACCESS_EDIT');
    xarRegisterMask('AddTinyMCE','All','tinymce','All','All:All','ACCESS_ADD');
    xarRegisterMask('DeleteTinyMCE ','All','tinymce','All','All:All','ACCESS_DELETE');
    xarRegisterMask('AdminTinyMCE','All','tinymce','All','All:All','ACCESS_ADMIN');

    return true;
}

/**
 * Upgrade the tinymce module from an old version
 *
 * @access public
 * @param none $
 * @returns bool
 */
function tinymce_activate()
{
    // Activate successful
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
    case '0.0.1':
        // Set up new module vars
        xarModSetVar('tinymce', 'tinybuttonsremove', '');
        return tinymce_upgrade('0.0.2');
        continue;
   case '0.0.2':
    // Set up new module vars
         xarModSetVar('tinymce', 'tinytoolbar', 'bottom');
         xarModSetVar('tinymce', 'tinylang', 'uk'); 
         xarModSetVar('tinymce', 'tinywidth','');
        return tinymce_upgrade('0.1.0');         
    break;
    case '0.0.3':
    // Set up new module vars
    xarModSetVar('tinymce', 'tinyinlinestyle','true');
    xarModSetVar('tinymce', 'tinyundolevel',10);
    return tinymce_upgrade('0.1.0');
    break;
    case '0.1.0':
    // Set up new module vars
    xarModSetVar('tinymce', 'tinydirection','ltr');
    xarModSetVar('tinymce', 'tinyencode','');
    return tinymce_upgrade('0.1.1');
    //Set up new module vars
    xarModSetVar('tinymce', 'tinyplugins', 'emotions,zoom,preview');
    xarModSetVar('tinymce', 'tinybuttons2','preview,zoom');
    xarModSetVar('tinymce', 'tinybuttons3','emotions');
    xarModSetVar('tinymce', 'tinybuild1', '');
    xarModSetVar('tinymce', 'tinybuild2', '');
    xarModSetVar('tinymce', 'tinybuild3', '');
    xarModSetVar('tinymce', 'tinydate', '');
    xarModSetVar('tinymce', 'tinytime', '');    
    xarModSetVar('tinymce', 'tinybr', 'false');            
    return tinymce_upgrade('0.1.2');
    break;
    case '0.1.2':
    xarModSetVar('tinymce', 'tinyadvformat', '');
    xarModSetVar('tinymce', 'tinyinvalid', '');
    return tinymce_upgrade('0.1.3');
    break;
    case '0.1.3':
    xarModSetVar('tinymce', 'tinyheight','');
    xarModSetVar('tinymce', 'useibrowser', 0);
    return tinymce_upgrade('0.2.0');
    case '0.2.0':
    return tinymce_upgrade('0.2.1');
    break;
    case '0.2.1':
    xarModSetVar('tinymce', 'tinypara', 'true');
    xarModSetVar('tinymce', 'tinyshowpath','bottom');
    return tinymce_upgrade('0.3.0');
    break;
    case '0.3.0':
    //database or var changes
    return tinymce_upgrade('0.3.1');
    break;
    case '0.3.1':
    //database or var changes
    //new charset and uk now changed to en
    if (xarModGetVar('tinymce', 'tinylang') =='uk') {
        xarModSetVar('tinymce', 'tinylang', 'en');
    }
    //all tablecontrols moved to a plug in
    if (xarModGetVar('tinymce', 'tinytheme') =='advanced') {
       $newplugs = xarModGetVar('tinymce', 'tinyplugins').',table'; //add table plugin
       xarModSetVar('tinymce','tinyplugins',$newplugs);
       $newbuttons3=xarModGetVar('tinymce','tinybuttons3').',tablecontrols';
       xarModSetVar('tinymce','tinybuttons3',$newbuttons3);
    }
     return tinymce_upgrade('0.3.2');
    break;
    case '0.3.2':
       return tinymce_upgrade('0.4.0');
    break;

    case '0.4.0':
    // Current version
    xarModSetVar('tinymce', 'tinycontentcss','');
    xarModSetVar('tinymce', 'tinynowrap','false');

    return tinymce_upgrade('0.4.1');
    break;
    case '0.4.1':
    xarModSetVar('tinymce', 'tinyloadmode','auto');
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
    // Remove Masks and Instances
    xarRemoveMasks('tinymce');
    xarRemoveInstances('tinymce');
    return true;
}

?>
