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
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));
    $data['tinymode'] = xarModGetVar('tinymce', 'tinymode');
    $data['tinyinstances'] = xarModGetVar('tinymce', 'tinyinstances');
    $data['tinycsslist'] = xarModGetVar('tinymce', 'tinycsslist');
    $data['tinyask'] = xarModGetVar('tinymce', 'tinyask');
    $data['tinyextended'] = xarModGetVar('tinymce', 'tinyextended');   
    $data['tinyexstyle'] = xarModGetVar('tinymce', 'tinyexstyle');   
    $data['tinybuttons'] = xarModGetVar('tinymce', 'tinybuttons');                  

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
