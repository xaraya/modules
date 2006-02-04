<?php
/**
 * Administration menu links.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */

/**
 * Retreive admin links
 *
 * This function return the menu links for the admin section in Xaraya.
 *
 * @author Jodie Razdrh/John Kevlin/David St.Clair/ Michel V.
 * @access  private
 * @param
 * @return  array $menulinks
 * @todo    MichelV. <michelv@xaraya.com>
 *
 * This module:
 * @copyright (C) 2005 by Metrostat Technologies, Inc.
 * initial template: Roger Raymond
 */


function julian_adminapi_getmenulinks()
{

    $menulinks = '';

    if (xarSecurityCheck('AdminJulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'admin',
                'main'),
            'title' => xarML('Julian Overview'),
            'label' => xarML('Overview'));
    }

    if (xarSecurityCheck('AdminJulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'admin',
                'modifycategories'),
            'title' => xarML('Modify Categories'),
            'label' => xarML('Modify Categories'));
    }

    if (xarSecurityCheck('AdminJulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify Config'),
            'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
