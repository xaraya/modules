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
   xarModSetVar('tinymce', 'tinymode', 'textareas');
   xarModSetVar('tinymce', 'tinyask', 'true');
   xarModSetVar('tinymce', 'tinybuttons', 'removeformat');
   xarModSetVar('tinymce', 'tinybuttonsremove', '');
   xarModSetVar('tinymce', 'tinyexstyle', 'heading 1=head1,heading 2=head2,heading 3=head3,heading 4=head4');
   xarModSetVar('tinymce', 'tinyextended', 'code,pre,blockquote/quote,a[href|rel:external]');
   xarModSetVar('tinymce', 'tinyinstances','summary,body');
   xarModSetVar('tinymce', 'tinycsslist','./themes/Xaraya_Classic/style/style.css');


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
function tinymce_upgrade($oldVersion)
{
   switch ($oldversion) {
   case '0.0.1':
       // Set up new module vars
       xarModSetVar('tinymce', 'tinybuttonsremove', '');
       if (!$result) return;
       break;
   case '0.0.2':
        // Current version
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
