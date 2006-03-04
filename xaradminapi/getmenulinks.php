<?php
/**
 * Xaraya BBCode
 *
 * Based on pnBBCode Hook from larsneo
 * http://www.pncommunity.de
 * Converted to Xaraya by John Cox
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BBCode Module
 * @link http://xaraya.com/index.php/release/778.html
 * @author John Cox
*/
/**
 * utility function pass individual menu items to the admin panels
 *
 * @author the BBCode module development team
 * @return array containing the menulinks for the main menu items.
 */
function bbcode_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('EditBBCode', 0)) {

        $menulinks[] = Array('url' => xarModURL('bbcode',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the bbcode module'),
            'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}
?>
