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
 * Get admin menu links
 */
function shouter_adminapi_getmenulinks()
{
    $menulinks = array();
    if (xarSecurityCheck('EditShouter', 0)) {
        $menulinks[] = array('url' => xarModURL('shouter', 'admin', 'view'),
            'title' => xarML('Delete Some or All Shouts'),
            'label' => xarML('Moderate Shouts'));
    }
    return $menulinks;
}
?>
