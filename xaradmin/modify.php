<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * modify an item
 * @param 'hid' the id of the headline to be modified
 */
function headlines_admin_modify($args)
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('hid','int:1:',$hid)) return;
    if (!xarVarFetch('obid','str:1:',$obid,$hid,XARVAR_NOT_REQUIRED)) return;

    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;
    $data = array();

    $data = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($data == false) return;

    $data['module']         = 'headlines';
    $data['itemtype']       = NULL; // forum
    $data['itemid']         = $hid;
    $hooks = xarModCallHooks('item','modify',$hid,$data);
    if (empty($hooks)) {
        $data['hooks']      = '';
    } elseif (is_array($hooks)) {
        $data['hooks']      = join('',$hooks);
    } else {
        $data['hooks']      = $hooks;
    }
    // see if this feed has any settings stored
    if (isset($data['settings']) && !empty($data['settings'])) {
        $setstring = $data['settings'];
        if (is_string($setstring)) {
            $settings = unserialize($setstring);
        } elseif (is_array($setstring)) {
            $settings = $setstring;
        } 
    }
    // if we didn't find settings, this is first run > v1.2.1 upgrade
    // so we just take the module defaults
    $data['itemsperpage'] = isset($settings['itemsperpage']) ? $settings['itemsperpage'] : xarModGetVar('headlines', 'feeditemsperpage');
    $data['maxdescription'] = isset($settings['maxdescription']) ? $settings['maxdescription'] : xarModGetVar('headlines', 'maxdescription');
    $data['parser'] = xarModGetVar('headlines', 'parser');
    // see if we're using simplepie
    if ($data['parser'] == 'simplepie') {
        $data['showchanimage'] = isset($settings['showchanimage']) ? $settings['showchanimage'] : xarModGetVar('headlines', 'showchanimage');
        $data['showitemimage'] = isset($settings['showitemimage']) ? $settings['showitemimage'] : xarModGetVar('headlines', 'showitemimage');
        $data['showitemcats'] = isset($settings['showitemcats']) ? $settings['showitemcats'] : xarModGetVar('headlines', 'showitemcats');
    }
    $data['submitlabel']    = xarML('Submit');
    $data['authid']         = xarSecGenAuthKey();

    return $data;

}
?>
