<?php

/**
 * display xlink entry
 *
 * @param $args['itemid'] item id of the xlink entry
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xlink_user_display($args)
{
    xarVarFetch('itemid','isset',$itemid,'', XARVAR_DONT_SET);
    extract($args);

    if (empty($itemid)) {
        $itemid = '';
    }
    return array('itemid' => $itemid);
}

?>
