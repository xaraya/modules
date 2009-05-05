<?php

/**
 * Utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 * @todo remove the need to do xarVarPrepForDisplay() here - it should be done at the point of display.
 */

function xarpages_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Get publication types
    $pagetypes = xarModAPIFunc('xarpages', 'user', 'get_types');

    foreach ($pagetypes as $pagetype) {
        // The description is multi-line, so only take the first line as the title.
        $desc_line1 = preg_replace('/[\n\r].*/', '', $pagetype['desc']);

        // The description is optional, so use the name as a fallback.
        if (empty($desc_line1)) {
            $desc_line1 = $pagetype['name'];
        }

        $itemtypes[$pagetype['id']] = array(
            'label' => xarVarPrepForDisplay($desc_line1),
            'title' => xarVarPrepForDisplay(xarML('Display #(1)', $desc_line1)),
            'url'   => xarModURL('xarpages', 'user', 'display', array('ptid' => $pagetype['id']))
        );
    }
    return $itemtypes;
}

?>