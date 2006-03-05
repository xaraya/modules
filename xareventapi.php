<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * Count page views
 *
 * This user api function stores the received page views in the database
 * and if it doesnt find an entry for the current timeframe and user agent
 * it creates a new one in the stats table
 *
 * @param   $arg
 * @return  bool
 */
function stats_eventapi_OnServerRequest($arg)
{
    $excludelist = explode("\n", trim(xarModGetVar('stats', 'excludelist')));
    $excluded = in_array($_SERVER["REMOTE_ADDR"], $excludelist);
    /* Uncomment the following to fix bug 178 */
    if (((xarSecurityCheck('AdminPanel', 0)) AND (xarModGetVar('stats', 'countadmin') == FALSE)) || $excluded) {
        return true;
    }

/*//TODO:<besfred> support for unique visitors count
    //param $args array - contains boolean $unique (true = unique hit, false = page view)
    extract($args);

    // error handling
    if ( (!($unique === FALSE)) && (!($unique === TRUE))) {
        return false;
    }
*/
    // get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];

    // get the user agent string id (internal browser id) from the session vars
    $uas = xarSessionGetVar('uaid');
    if (!$uas) {
        // Note : we can't use xarModAPIFunc for this event !
//        include_once 'modules/sniffer/xaruserapi.php';
        // sniff once more.
        if(!xarModAPIFunc('sniffer','user','sniff')) return;
        $uas = xarSessionGetVar('uaid');
        // still doesnt work => quit
        if (!$uas) return;
    }

    // e.g. Y: year 2002, n: month 4, w: weekday 0 for su, j: day 1, G: hour 1
    $datearray = explode("|", gmdate("Y|n|w|j|G"));

    // create query:
    // increase the counter of the current time frame
    //TODO:<besfred>Look at method $dbconn->Replace($query);
/*
    $query  = ($unique)
            ? "UPDATE $statstable
               SET $statscolumn[uniquecount] = $statscolumn[uniquecount] + 1 "
            : "UPDATE $statstable
               SET $statscolumn[count]  = $statscolumn[count] + 1 ";
*/
    $query  = "UPDATE $statstable
               SET xar_sta_hits = xar_sta_hits + 1 ";
    $query .= "WHERE xar_sta_year = {$datearray[0]}
               AND xar_sta_month  = {$datearray[1]}
               AND xar_sta_day    = {$datearray[3]}
               AND xar_sta_hour   = {$datearray[4]}
               AND xar_ua_id      = {$uas}";
    $result =& $dbconn->Execute($query);

    // check for an error with the database code
    if (!$result) return;

    // insert a new row if this is the first page view in the new time frame and
    // with the particular user agent
    if (0 >= $dbconn->Affected_Rows()) {
        $query = "INSERT INTO $statstable
                  VALUES ({$datearray[0]}, {$datearray[1]}, {$datearray[2]},
                          {$datearray[3]}, {$datearray[4]}, {$uas}, 1, 1)";
        $result = $dbconn->Execute($query);

        // Check for an error with the database code
        if (!$result) {
            // mid-air collision between two inserts - ignore
            xarErrorFree();
        }
    }

    // return true to indicate a successful count
    return true;
}

?>
