<?php

/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function articles_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Get publication types
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    foreach ($pubtypes as $id => $pubtype) {
        $itemtypes[$id] = array('label' => xarVarPrepForDisplay($pubtype['descr']),
                                'title' => xarVarPrepForDisplay(xarML('Display #(1)',$pubtype['descr'])),
                                'url'   => xarModURL('articles','user','view',array('ptid' => $id))
                               );
    }
    return $itemtypes;
}

?>
