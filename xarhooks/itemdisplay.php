<?php
/**
 * ItemDisplay Hook
 *
 * Displays similar items
**/
function fulltext_hooks_itemdisplay($args)
{
    extract($args);
        
    
    return xarTplModule('fulltext', 'hooks', 'itemdisplay', $data);
}
?>