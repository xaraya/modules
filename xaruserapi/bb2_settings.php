<?php
function netquery_userapi_bb2_settings()
{
    xarModDBInfoLoad('blocks');
    $table_prefix = xarDBGetSiteTablePrefix();
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $query = "SELECT groupstable.xar_group_id FROM $xartable[block_group_instances] as groupstable
     LEFT JOIN $xartable[block_instances] as instancestable ON instancestable.xar_id = groupstable.xar_instance_id
     LEFT JOIN $xartable[block_types] as typestable ON typestable.xar_id = instancestable.xar_type_id
     WHERE xar_type = 'nqmonitor'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($group_id) = $result->fields;
    $result->Close();
    $bb_running = ($group_id > '0') ? true : false;
    $bb_retention = xarModGetVar('netquery', 'bb_retention');
    $bb_enabled = xarModGetVar('netquery', 'bb_enabled');
    $bb_visible = xarModGetVar('netquery', 'bb_visible');
    $bb_display_stats = xarModGetVar('netquery', 'bb_display_stats');
    $bb_strict = xarModGetVar('netquery', 'bb_strict');
    $bb_verbose = xarModGetVar('netquery', 'bb_verbose');
    $settings = array('log_table' => $table_prefix.'_netquery_spamblocker',
                      'log_retain' => $bb_retention,
                      'enabled' => $bb_enabled,
                      'running' => $bb_running,
                      'visible' => $bb_visible,
                      'display_stats' => $bb_display_stats,
                      'strict' => $bb_strict,
                      'verbose' => $bb_verbose );
    return $settings;
}
?>