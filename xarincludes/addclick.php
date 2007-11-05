<?php

/**
 * Adds link clicked data into the database from a GET request.
 * Limited to MySQL only.
 *
 * @param linkid
 * @param target
 * @param label
 *
 */

function linktracker_addclick()
{
    // This will only work if "var" is directly in the web root.
    include_once(dirname(__FILE__) . '../../../../var/config.system.php');

    // Database connection (assumes MySQL)
    $dblink = mysql_connect(
        $systemConfiguration['DB.Host'],
        $systemConfiguration['DB.UserName'],
        $systemConfiguration['DB.Password']
    );
    mysql_select_db($systemConfiguration['DB.Name']);

    // Fetch variables (some passed in, some local).
    // Strip the target from the current page, which only IE7 seems to send.
    $page = preg_replace('/#.*$/', '', $_SERVER['HTTP_REFERER']);
    $linkid = get_param('linkid');
    $target = get_param('target');
    $label = get_param('label');
    $ipaddr = $_SERVER['REMOTE_ADDR'];
    // Timestamp details.
    $time = time();
    $year = (int)date('Y', $time);
    $month = (int)date('m', $time);
    $day = (int)date('d', $time);

    // CHECKME: this will work in most cases, but the session cookie could be given a different name.
    if (isset($_COOKIE['XARAYASID'])) {
        $cookie = $_COOKIE['XARAYASID'];
    } else {
        $cookie = '';
    }

    // Parse the URLs so we can extract more useful information.
    $page_parsed = parse_url($page);
    $target_parsed = parse_url($target);

    // Check the referrer domain matches the current script domain.
    $http_host = $_SERVER['HTTP_HOST'];
    if (!empty($http_host) && $http_host != $page_parsed['host']) return false;

    // Run the query to insert the data, but only if we have enough data.
    if (!empty($cookie) && !empty($page) && !empty($linkid) && !empty($target)) {
        // Prepare variables for the database.
        $db_data = array(
            'page' => $page,
            'page_host' => $page_parsed['host'],
            'page_path' => $page_parsed['path'],
            'page_query' => $page_parsed['query'],
            'link_id' => $linkid,
            'target' => $target,
            'target_host' => $target_parsed['host'],
            'target_path' => $target_parsed['path'],
            'target_query' => $target_parsed['query'],
            'label' => $label,
            'utimestamp' => $time,
            'ip_address' => $ipaddr,
            'year' => $year,
            'month' => $month,
            'day' => $day,
        );

        // Quote all DB parameters.
        foreach($db_data as $key => $value) $db_data[$key] = db_quote($value);
    
        $tblPrefix = $systemConfiguration['DB.TablePrefix'];
        $query = 'INSERT INTO ' . $tblPrefix . '_jquery_linktracker'
            . ' (' . implode(', ', array_keys($db_data)) . ')'
            . ' VALUES (' . implode(', ', $db_data) . ')';
            echo "query = $query";
        $res = mysql_query($query);
    }

    return true;
}

// Get a passed-in parameter, returning '' if the parameter does not exist.
function get_param($name)
{
    // Check the value is available.
    if (empty($name)) return '';
    if (!isset($_GET[$name]) || !is_string($_GET[$name])) return '';

    // Get the value.
    $value = $_GET[$name];

    // Undo magic quotes, if they happen to be set.
    if (get_magic_quotes_gpc()) $value = stripslashes($value);

    // Strip out a few characters we don't need.
    $value = str_replace(array("\n", "\r", "\t"), ' ', $value);
    $value = trim(strip_tags($value));

    // Return the cleaned parameter value.
    return $value;
}


/**
 * Quote variable to make safe for MySQL.
 * Shamelessly taken from http://www.php.net/mysql_real_escape_string
 */
function db_quote($value)
{
	// Quote if not a number.
	if (!is_numeric($value)) $value = "'" . mysql_real_escape_string($value) . "'";

	return $value;
}

// Log the click.
$result = linktracker_addclick();

header("Content-Type: text/xml; charset=utf-8\r\n");
echo "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>\n";

if ($result) {
    echo '<rsp stat="ok">OK</rsp>';
} else {
    echo '<rsp stat="error">ERROR</rsp>';
}

?>