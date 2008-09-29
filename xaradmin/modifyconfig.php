<?php
/**
 * Xaraya Smilies Config
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/
/**
 * @returns output
 * @return output with smilies configuration settings
 */
function smilies_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminSmilies')) return;
    
    $data = array();

    // Bug 3957: get configured tags to skip
    $skipstring = xarModGetVar('smilies', 'skiptags');
    $skiptags = array();
    if (!empty($skipstring)) {
      $skiptags = unserialize($skipstring);
    } 
    $data['skiptags'] = join(',',$skiptags);
    // make use of the itemsperpage module var
    $data['itemsperpage'] = xarModGetVar('smilies', 'itemsperpage');
    // Bug 5116: option to hide duplicate emotions in user main
    $data['showduplicates'] = xarModGetVar('smilies', 'showduplicates');
    // Bug 5271: allow specifying a subfolder from which to serve smilies images
    $data['image_folder'] = xarModGetVar('smilies', 'image_folder');
    // call modifyconfig hooks with module
    $hooks = xarModCallHooks('module', 'modifyconfig', 'smilies',
                             array('module'   => 'smilies'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } else {
        $data['hooks'] = $hooks;
    }
    $data['authid'] = xarSecGenAuthKey('smilies');
    return $data;
}
?>