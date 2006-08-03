<?php

function xtasks_admin_new($args)
{
    extract($args);

    if (!xarVarFetch('inline', 'str', $inline, $inline, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modid', 'int', $modid, $modid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'int', $itemtype, $itemtype, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    
    xarModLoad('xtasks','user');
    $data = xarModAPIFunc('xtasks','user','menu');

    if (!xarSecurityCheck('AddXTask')) {
        return;
    }
    
//    echo "xarServerGetCurrentURL: ".xarServerGetCurrentURL();
//    echo "xarRequestGetInfo: ";
//    print_r(xarRequestGetInfo());
//    echo "<span style='text-align: left;'>";
//echo "<pre>";
//print_r($_SERVER);
//die("</pre>");
    if(!empty($returnurl)) {
        $data['returnurl'] = $returnurl;
    } elseif (isset($_SERVER['HTTP_REFERER'])) {
        $data['returnurl'] = $_SERVER['HTTP_REFERER'];
    } else {
        $data['returnurl'] = xarModURL('xtasks','admin','view');
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['inline'] = $inline;
    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    $data['objectid'] = $objectid;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));

    $item = array();
    $item['module'] = 'xtasks';
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
}

?>
