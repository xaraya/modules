<?php

/**
 * show a particular version of a module item
 */
function changelog_admin_showversion($args)
{ 
    extract($args);

    if (!xarVarFetch('modid',    'isset', $modid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid',   'isset', $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('logid',    'isset', $logid,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminChangeLog',1,'Item',"$modid:$itemtype:$itemid")) return;

    $data = xarModAPIFunc('changelog','admin','getversion',
                          array('modid' => $modid,
                                'itemtype' => $itemtype,
                                'itemid' => $itemid,
                                'logid' => $logid));
    if (empty($data) || !is_array($data)) return;

    if (xarSecurityCheck('AdminChangeLog',0)) {
        $data['showhost'] = 1;
    } else {
        $data['showhost'] = 0;
    }

    $data['profile'] = xarModURL('roles','user','display',
                                 array('uid' => $data['editor']));
    if (!$data['showhost']) {
        $data['hostname'] = '';
    }
    if (!empty($data['remark'])) {
        $data['remark'] = xarVarPrepForDisplay($data['remark']);
    }
// TODO: adapt to local/user time !
    $data['date'] = strftime('%a, %d %B %Y %H:%M:%S %Z', $data['date']);

    $data['link'] = xarModURL('changelog','admin','showlog',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'itemid' => $itemid));

    $data['fields'] = array();
    if (!empty($data['content'])) {
        $fields = unserialize($data['content']);
        $data['content'] = '';
        foreach ($fields as $field => $value) {
            if ($field == 'module' || $field == 'itemtype' || $field == 'itemid' ||
                $field == 'mask' || $field == 'pass') {
                continue;
            }
            if (is_array($value) || is_object($value)) {
                $value = serialize($value);
            }
            $data['fields'][$field] = xarVarPrepForDisplay($value);
        }
    }
    return $data;
}

?>
