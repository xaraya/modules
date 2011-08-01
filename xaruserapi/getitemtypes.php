<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @return array
 * @return array containing the item types and their description
 */
function uploads_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Files
    $id = 1;
    $itemtypes[$id] = array('label' => xarML('Files'),
                            'title' => xarML('View All Files'),
                            'url'   => xarModURL('uploads','admin','view')
                           );

    // TODO: Assoc, VDir and other future tables ?

    return $itemtypes;
}

?>
