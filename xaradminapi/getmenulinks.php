<?php
/**
 * Shouter Module
 *
 * @package modules
 * @subpackage shouter module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
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
