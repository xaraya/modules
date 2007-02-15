<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
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
