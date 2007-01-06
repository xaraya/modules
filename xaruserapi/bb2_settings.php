<?php
function netquery_userapi_bb2_settings()
{
    if (!defined('NQ4_CWD')) define('NQ4_CWD', substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR)));
    if (!defined('BB2_CWD')) define('BB2_CWD', NQ4_CWD);
    require_once(BB2_CWD . "/xarincludes/spamblocker/version.inc.php");
    xarModDBInfoLoad('blocks');
    $table_prefix = xarDBGetSiteTablePrefix();
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $bb_running = false;
    $query = "SELECT COUNT(1) FROM $xartable[block_instances] as instancestable
     LEFT JOIN $xartable[block_types] as typestable ON typestable.xar_id = instancestable.xar_type_id
     WHERE xar_type = 'netquick' AND xar_state > '0'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($blocks_active) = $result->fields;
    if ($blocks_active > 0) $bb_running = true;
    $bb_retention = xarModGetVar('netquery', 'bb_retention');
    $bb_enabled = xarModGetVar('netquery', 'bb_enabled');
    $bb_visible = xarModGetVar('netquery', 'bb_visible');
    $bb_display_stats = xarModGetVar('netquery', 'bb_display_stats');
    $bb_strict = xarModGetVar('netquery', 'bb_strict');
    $bb_verbose = xarModGetVar('netquery', 'bb_verbose');
    $settings = array('version' => BB2_VERSION,
                      'log_table' => $table_prefix.'_netquery_spamblocker',
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