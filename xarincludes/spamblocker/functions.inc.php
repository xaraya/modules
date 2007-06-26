<?php if (!defined('BB2_CORE')) die("I said no cheating!");
function bb2_insert_head()
{
    global $bb2_javascript;
    echo $bb2_javascript;
}
function bb2_read_settings()
{
    $table_prefix = xarDBGetSiteTablePrefix();
    $bb_running = true;
    $bb_enabled = xarModGetVar('netquery', 'bb_enabled');
    $bb_retention = xarModGetVar('netquery', 'bb_retention');
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
function bb2_insert_stats($force = false)
{
    $settings = bb2_read_settings();
    if ($force || $settings['display_stats']) {
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $SpamblockerTable = $xartable['netquery_spamblocker'];
        $query = "SELECT COUNT(*) FROM $SpamblockerTable WHERE bb_key NOT LIKE '00000000' ";
        if ($result =& $dbconn->Execute($query))
        {
            list($count) = $result->fields;
            $result->Close();
            echo sprintf('<a href="http://www.bad-behavior.ioerror.us/">%1$s</a> %2$s <strong>%3$s</strong> %4$s %5$s %6$s</p>', 'Bad Behavior', 'has blocked', $count, 'access attempts in the last', $settings['log_retain'], 'days.');
        }
    }
}
function bb2_key_response($key)
{
    $response = array('response' => 0, 'explanation' => '', 'log' => '');
    include_once(BB2_CORE . '/responses.inc.php');
    if (is_callable('bb2_get_response')) $response = bb2_get_response($key);
    return $response;
}
function bb2_db_date()
{
    return gmdate('Y-m-d H:i:s');
}
function bb2_email()
{
    return xarModGetVar('mail', 'adminmail');
}
function bb2_relative_path()
{
    $path = xarServerGetBaseURI();
    if (empty($path)) {
        $path = '/';
    }
    return $path;
}
function bb2_db_escape($string)
{
    return addslashes($string);
}
function bb2_db_query($query)
{
    $dbconn =& xarDBGetConn();
    $result =& $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        return false;
    }
    return $result;
}
function bb2_db_num_rows($result)
{
    return count($result);
}
//
// Original functions.
//
if (!function_exists('stripos'))
{
    function stripos($haystack,$needle,$offset = 0)
    {
        return(strpos(strtolower($haystack),strtolower($needle),$offset));
    }
}
if (!function_exists('str_split'))
{
    function str_split($string, $split_length=1)
    {
        if ($split_length < 1) {
            return false;
        }

        for ($pos=0, $chunks = array(); $pos < strlen($string); $pos+=$split_length) {
            $chunks[] = substr($string, $pos, $split_length);
        }
        return $chunks;
    }
}
function uc_all($string)
{
    $temp = preg_split('/(\W)/', str_replace("_", "-", $string), -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($temp as $key=>$word) {
        $temp[$key] = ucfirst(strtolower($word));
    }
    return join ('', $temp);
}
function match_cidr($addr, $cidr)
{
    $output = false;

    if (is_array($cidr)) {
        foreach ($cidr as $cidrlet) {
            if (match_cidr($addr, $cidrlet)) {
                $output = true;
            }
        }
    } else {
        list($ip, $mask) = explode('/', $cidr);
        if (!$mask) $mask = 32;
        $mask = pow(2,32) - pow(2, (32 - $mask));
        $output = ((ip2long($addr) & $mask) == (ip2long($ip) & $mask));
    }
    return $output;
}
function bb2_load_headers()
{
    if (!is_callable('getallheaders'))
    {
        $headers = array();
        foreach($_SERVER as $name => $value)
            if(substr($name, 0, 5) == 'HTTP_')
                $headers[substr($name, 5)] = $value;
    }
    else
    {
        $headers = getallheaders();
    }
    return $headers;
}
?>