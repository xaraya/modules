<?php

/**
 * udpate item from categories_admin_modify
 */
function categories_admin_updatecat()
{
    if (!xarVarFetch('creating', 'bool', $creating)) return;

    if ($creating) {
        return xarModFunc('categories','admin','create');
    } else {
        return xarModFunc('categories','admin','update');
    }
}
?>
