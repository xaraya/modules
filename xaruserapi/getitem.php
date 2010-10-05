<?php
function fulltext_userapi_getitem($args)
{
    if (empty($args))
        throw new BadParameterException('args');
    $items = xarMod::apiFunc('fulltext', 'user', 'getitems', $args);
    if (!empty($items) && is_array($items) && count($items) == 1)
        return reset($items);
    return;
}
?>