<?php

function keywords_indexapi_getid(array $args=[])
{
    extract($args);

    if (!empty($module)) {
        $module_id = xarMod::getRegId($module);
    }
    if (empty($module_id) || !is_numeric($module_id)) {
        $invalid[] = 'module_id';
    }

    if (empty($itemtype)) {
        $itemtype = 0;
    }
    if (!is_numeric($itemtype)) {
        $invalid[] = 'itemtype';
    }

    if (empty($itemid)) {
        $itemid = 0;
    }
    if (!is_numeric($itemid)) {
        $invalid[] = 'itemid';
    }

    if (!empty($invalid)) {
        $msg = 'Invalid #(1) for #(2) module #(3) function #(4)()';
        $vars = [implode(', ', $invalid), 'keywords', 'indexapi', 'getid'];
        throw new BadParameterException($vars, $msg);
    }

    $cacheKey = "$module_id:$itemtype:$itemid";
    if (xarCoreCache::isCached('Keywords.Index', $cacheKey)) {
        return xarCoreCache::getCached('Keywords.Index', $cacheKey);
    }

    if (!$item = xarMod::apiFunc(
        'keywords',
        'index',
        'getitem',
        [
            'module_id' => $module_id,
            'itemtype' => $itemtype,
            'itemid' => $itemid,
        ]
    )) {
        $item = xarMod::apiFunc(
            'keywords',
            'index',
            'createitem',
            [
                'module_id' => $module_id,
                'itemtype' => $itemtype,
                'itemid' => $itemid,
            ]
        );
    }

    xarCoreCache::setCached('Keywords.Index', $cacheKey, $item['id']);

    return $item['id'];
}
