<?php
function fulltext_admin_search($args)
{
    
    if (!xarVarFetch('q', 'pre:trim:str:1:', $q, "", XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('search', 'checkbox', $search, false, XARVAR_NOT_REQUIRED)) return;
    
    $itemsperpage = xarModVars::get('fulltext', 'items_per_page');
    
    if ($search) {
        $items = xarMod::apiFunc('fulltext', 'user', 'search',
            array(
                'q' => $q,
                'startnum' => $startnum,
                'numitems' => $itemsperpage,
            ));
    }
    static $_modinfos = array();
    $results = array();
    if (!empty($items)) {
        foreach ($items as $item) {
            if (!isset($_modinfos[$item['module_id']]))
                $_modinfos[$item['module_id']] = xarMod::getInfo($item['module_id']);
            $modinfo = $_modinfos[$item['module_id']];
            if (!isset($_modinfos[$item['module_id']]['itemtypes'])) {
                try {
                    $_modinfos[$item['module_id']]['itemtypes'] = xarMod::apiFunc($modinfo['name'], 'user', 'getitemtypes');
                } catch (Exception $e) {
                    $_modinfos[$item['module_id']]['itemtypes'] = array();
                }
            }
            $itemtypes = $_modinfos[$item['module_id']]['itemtypes'];
            try {
                $itemlinks = xarMod::apiFunc($modinfo['name'], 'user', 'getitemlinks', 
                    array(
                        'itemids' => array($item['itemid']),
                        'itemtype' => !empty($item['itemtype']) ? $item['itemtype'] : null,
                    ));
                if (isset($itemlinks[$item['itemid']]))
                    $itemlink = $itemlinks[$item['itemid']];
                if (!empty($itemtypes[$item['itemtype']]))
                    $itemlink += array(
                        'module' => $modinfo['name'],
                        'itemtype' => !empty($item['itemtype']) ? $item['itemtype'] : null,
                        'itemid' => $item['itemid'],
                    );
                if (empty($itemlink['label']) && !empty($itemlink['title']))
                    $itemlink['label'] = $itemlink['title'];                  
            } catch (Exception $e) {
                if (!empty($itemtypes[$item['itemtype']])) {
                    $title = xarML('Display #(1) #(2) #(3)', $modinfo['displayname'], $itemtypes[$item['itemtype']]['label'], $item['itemid']);
                    $label = xarML('#(1) #(2) #(3)', $modinfo['displayname'], $itemtypes[$item['itemtype']]['label'], $item['itemid']);
                } else {
                    $title = xarML('Display #(1) #(2)', $modinfo['displayname'], $item['itemid']);
                    $label = xarML('#(1) #(2)', $modinfo['displayname'], $item['itemid']);
                }
                $url = xarModURL($modinfo['name'], 'user', 'display',
                    array(
                        'itemid' => $item['itemid'],
                        'itemtype' => !empty($item['itemtype']) ? $item['itemtype'] : null,
                    )); 
                if (empty($label) && !empty($title)) 
                    $label = $title;
                $itemlink = array(
                    'title' => $title,
                    'label' => $label,
                    'url' => $url,
                    'module' => $modinfo['name'],
                    'itemtype' => !empty($item['itemtype']) ? $item['itemtype'] : null,
                    'itemid' => $item['itemid'],
                );
            }
            $results[] = $itemlink;
        }
    }            
    $data = array();
    $data['q'] = $q;
    $data['results'] = $results;
    
    return $data;
    
}
?>