<?php
/**
 * File: $Id$
 * 
 * Realms configuration modification
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
  * @subpackage Realms
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function tinymce_admin_modifyconfig()
{
    $data = xarModAPIFunc('tinymce', 'admin', 'menu');
    if (!xarSecurityCheck('AdminTinyMCE')) return;
    $data['authid'] = xarSecGenAuthKey();
    // Specify some labels and values for display
    $data['tinytheme'] = xarModGetVar('tinymce', 'tinytheme');
    $data['tinylang'] = xarModGetVar('tinymce', 'tinylang');    
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));
    $data['tinymode'] = xarModGetVar('tinymce', 'tinymode');
    $data['tinyinstances'] = xarModGetVar('tinymce', 'tinyinstances');
    $data['tinycsslist'] = xarModGetVar('tinymce', 'tinycsslist');
    $data['tinyask'] = xarModGetVar('tinymce', 'tinyask');
    $data['tinyextended'] = xarModGetVar('tinymce', 'tinyextended');
    $data['tinyexstyle'] = xarModGetVar('tinymce', 'tinyexstyle');
    $data['tinybuttons'] = xarModGetVar('tinymce', 'tinybuttons');     
    $data['tinybuttonsremove'] = xarModGetVar('tinymce', 'tinybuttonsremove');                    
    $data['tinytoolbar'] = xarModGetVar('tinymce', 'tinytoolbar');
    $data['tinywidth'] = xarModGetVar('tinymce', 'tinywidth');
    $data['tinyinlinestyle'] = xarModGetVar('tinymce', 'tinyinlinestyle');
    $data['tinyundolevel'] = xarModGetVar('tinymce', 'tinyundolevel');
    $data['defaulteditor'] = xarModGetVar('base','editor');
    $data['tinydirection'] = xarModGetVar('tinymce','tinydirection');    
    $data['tinyencode'] = xarModGetVar('tinymce','tinyencode');   
     //get list of valid themes
    $tinythemepath="./modules/tinymce/xartemplates/includes/tinymce/jscripts/tiny_mce/themes";
    $themelist=array();
    $handle=opendir($tinythemepath);
    $skip_array = array('.','..','SCCS','index.htm','index.html');
    while (false !== ($file = readdir($handle))) {
        // check the skip array and add files in it to array
        if (!in_array($file,$skip_array)) {
            $themelist[]=$file;
        }
    }
    closedir($handle);
    //get list of valid languages
    $tinylangpath="./modules/tinymce/xartemplates/includes/tinymce/jscripts/tiny_mce/langs";
    $langlist=array();
    $handle=opendir($tinylangpath);
    while (false !== ($file = readdir($handle))) {
        // check the skip array and add files in it to array
        if (!in_array($file,$skip_array)) {
            $langlist[]=str_replace('.js', '', $file);
        }
    }
    closedir($handle);
    $data['themelist']=$themelist;
    $data['langlist']=$langlist;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'tinymce',
        array('module' => 'tinymce'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // Return the template variables defined in this function
    return $data;
}

?>
