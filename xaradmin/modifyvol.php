<?php
/**
 * Modify a volume's information
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_admin_modifyvol()
{
    if (!xarVarFetch('vid',       'int', $vid,  NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('confirmed', 'int', $confirmed, NULL, XARVAR_DONT_SET)) return;

    if (!isset($vid)) {
        $msg = "No encyclopedia volume was found";
        xarErrorSet(XAR_USER_EXCEPTION, 'NOT_FOUND', new DefaultUserException($msg));
        return;
    }


    if (empty($confirmed)) {
        $volume = xarModAPIFunc('encyclopedia',
                             'admin',
                             'volget',
                             array('vid' => $vid));
        $data['vid'] = $vid;
        $data['volume'] = $volume['volume'];
        $data['description'] = $volume['description'];
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }

    if(!xarVarFetch('volume',   'str', $volume   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('description',   'str', $description   , '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarSecurityCheck('EditEncyclopedia',0,'Volume',$volume . "::" . $vid)) {return;}
    if (!xarSecConfirmAuthKey()) return;
    xarModAPIFunc('encyclopedia','admin','updatevol',
                            array('vid' => $vid,
                                  'volume' => $volume,
                                  'description' => $description));
    $data['vid'] = $vid;
    $data['volume'] = $volume;
    $data['description'] = $description;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>