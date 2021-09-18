<?php

function indexer_indexapi_reindex(array $args=[])
{
    extract($args);

    static $runonce = false;
    if ($runonce) {
        return true;
    }

    $modules = xarMod::apiFunc(
        'modules',
        'admin',
        'getitems',
        [
            'state' => xarMod::STATE_ANY,
            'name' => !empty($module) ? $module : null,
        ]
    );

    foreach ($modules as $module) {
        try {
            $index = xarMod::apiFunc(
                'indexer',
                'index',
                'createitem',
                [
                    'module_id' => $module['regid'],
                    'itemtype' => 0,
                    'item_id' => 0,
                ]
            );
            if ($module['state'] != xarMod::STATE_ACTIVE) {
                continue;
            }
            try {
                $itemtypes = xarMod::apiFunc($module['name'], 'user', 'getitemtypes');
            } catch (Exception $f) {
                $itemtypes = [];
            }
            foreach ($itemtypes as $itemtype => $linkinfo) {
                $i_index = xarMod::apiFunc(
                    'indexer',
                    'index',
                    'createitem',
                    [
                        'module_id' => $module['regid'],
                        'itemtype' => $itemtype,
                        'item_id' => 0,
                    ]
                );
                try {
                    $itemlinks = xarMod::apiFunc($module['name'], 'user', 'getitemlinks');
                } catch (Exception $f) {
                    $itemlinks = [];
                }
                foreach ($itemlinks as $itemid => $itemlink) {
                    $l_index = xarMod::apiFunc(
                        'indexer',
                        'index',
                        'createitem',
                        [
                            'module_id' => $module['regid'],
                            'itemtype' => $itemtype,
                            'item_id' => $itemid,
                        ]
                    );
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    //something
    // something
    //  somethings

    return $runonce = true;
}
