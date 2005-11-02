<?php
/**
 * Modify site configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage wizards
 * @author Marc Lutolf
 */
/**
 * Modify site configuration
 *
 * @return array of template values
 */
function wizards_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminWizard')) return;

    if(xarModGetVar('wizards','status') == '') $wizards = 0;
    else $wizards = xarModGetVar('wizards','status');

    $data['showuserwizards'] = $wizards % 2;
    $data['showadminwizards'] = ($wizards - ($wizards % 2))/2;
    return $data;
}

?>