<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Modify a shout
 */
function shouter_admin_modify($args)
{
    extract($args);

    if (!xarVarFetch('shoutid', 'id', $shoutid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shout', 'str:1:', $shout, $shout,XARVAR_NOT_REQUIRED)) return;


    if (!empty($objectid)) {
        $shoutid = $objectid;
    }

    $item = xarModAPIFunc('shouter', 'user', 'get',
                    array('shoutid' => $shoutid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditShouter', 1, 'Item', "$item[name]:All:$shoutid")) {
        return;
    }

    $item['module'] = 'shouter';
    $hooks = xarModCallHooks('item', 'modify', $shoutid, $item);
    if (empty($hooks)) {
        $hooks = '';
    } elseif (is_array($hooks)) {
        $hooks = join('', $hooks);
    }
    // Return the template variables defined in this function
    return array('authid'       => xarSecGenAuthKey(),
                 'namelabel'    => xarVarPrepForDisplay(xarML('User Name:')),
                 'name'     => $name,
                 'shoutlabel'   => xarVarPrepForDisplay(xarML('Shout Text:')),
                 'shout'    => $shout,
                 'updatebutton' => xarVarPrepForDisplay(xarML('Modify Shout')),
                 'hooks'        => $hooks,
                 'item'         => $item);
}
?>
