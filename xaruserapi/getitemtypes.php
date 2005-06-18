<?php

/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
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
