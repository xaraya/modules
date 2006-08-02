<?php
// Edit a Url
function window_admin_editurl($args)
{
    if (!xarSecurityCheck('AdminWindow')) return;
    if (!xarSecConfirmAuthKey()) return;

    $data = array();
    $data['authid'] = xarSecGenAuthKey();
    $data['action'] = xarModURL('window', 'admin', 'newurl');
    $data['window_status'] = "edit";

    $data['urls'] = xarModAPIFunc('window','admin','geturls');

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    if (!xarVarFetch('id', 'id', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bluff', 'str', $bluff, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

    // extract info from db
    $urltable = $xartable['window'];
    $query = "SELECT
            *
            FROM
            $urltable
            WHERE xar_id=$id";

    $result = $dbconn->Execute($query);
    if(!$result) return;

    list($id, $host, $alias, $reg_user_only, $open_direct, $use_fixed_title, $auto_resize, $vsize, $hsize, $status) = $result->fields;

    $data['host'] = $host;
    $data['alias'] = $alias;
    $data['id'] = $id;
    $data['lang_action'] = xarML("Save");

    $data['reg_user_only'] = $reg_user_only;
    $data['open_direct'] = $open_direct;
    $data['use_fixed_title'] = $use_fixed_title;
    $data['auto_resize'] = $auto_resize;
    $data['vsize'] = $vsize;
    $data['hsize'] = $hsize;
    $data['status'] = $status;

    return xarTplModule('window','admin','newurl',$data);
}
?>
