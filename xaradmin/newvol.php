<?php
/**
 * Add a volume to the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_admin_newvol()
{
    if (!xarSecurityCheck('AddEncyclopedia')) {return;}

    if(!xarVarFetch('volume',   'str', $data['volume']   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('description',   'str', $data['description']   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('confirmed',   'int', $confirmed   , 0, XARVAR_NOT_REQUIRED)) {return;}

    $data['authid'] = xarSecGenAuthKey();

    if ($confirmed) {
        if (!xarSecConfirmAuthKey()) return;
        $id = xarModAPIFunc('encyclopedia',
                            'admin',
                            'addvol',
                            array('volume' => $data['volume'],
                                  'description' => $data['description']));
        $data['volume'] = '';
        $data['description'] = '';
    }
    return $data;
}
?>