<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Check the status of all links in the xar_sitetools_links table
 * @author mikespub
 * @param $args['skiplocal'] bool optional flag to skip local links (default false)
 * @param $args['method'] string the request method to use (default is HEAD, alternative is GET)
 * @param $args['follow'] bool indicates if we want to follow redirects or not (default is true)
 * @param $args['status'] integer optional status of the links to check for
 * @param $args['notstatus'] integer optional status of the links NOT to check for
 * @param $args['where'] string optional where clause (e.g. 'xar_status <> 200')
 * @returns integer
 * @return number of items checked
 * @raise DATABASE_ERROR
*/
function sitetools_adminapi_checklinks($args)
{
    extract($args);

    if (!isset($skiplocal)) $skiplocal = false;
    if (!isset($method)) $method = 'HEAD';
    if (!isset($follow)) $follow = true;

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $linkstable = $xartable['sitetools_links'];

    // find out the total number of links to check
    $query = "SELECT COUNT(DISTINCT(xar_link)) FROM $linkstable ";
    if (!empty($status) && is_numeric($status)) {
        $query .= 'WHERE xar_status='.$status;
    } elseif (!empty($notstatus) && is_numeric($notstatus)) {
        $query .= 'WHERE xar_status<>'.$notstatus;
    } elseif (!empty($where)) {
        $query .= $where;
    }
    $result = $dbconn->Execute($query);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();
    if (empty($numitems)) return $numitems;

    // reset their status to 0
    $update = "UPDATE $linkstable SET xar_status=0 ";
    if (!empty($status) && is_numeric($status)) {
        $query .= 'WHERE xar_status='.$status;
    } elseif (!empty($notstatus) && is_numeric($notstatus)) {
        $query .= 'WHERE xar_status<>'.$notstatus;
    } elseif (!empty($where)) {
        $query .= $where;
    }
    $result = $dbconn->Execute($update);
    if (!$result) return;

    // check all distinct URLs
    $query = "SELECT DISTINCT(xar_link) FROM $linkstable ";
    // only check for those with status 0 if necessary
    if (!empty($status) && is_numeric($status)) {
        $query .= 'WHERE xar_status=0';
    } elseif (!empty($notstatus) && is_numeric($notstatus)) {
        $query .= 'WHERE xar_status=0';
    } elseif (!empty($where)) {
        $query .= 'WHERE xar_status=0';
    }
    $result = $dbconn->Execute($query);
    if (!$result) return;

    $count = 0;
    $date = xarLocaleFormatDate('%x %X %z',time());
    $msg = xarML('#(1) : #(2)/#(3) links checked',$date,$count,$numitems);
    xarModVars::set('sitetools','links_checked',$msg);
    $update = "UPDATE $linkstable SET xar_status=? WHERE xar_link=?";
    for (; !$result->EOF; $result->MoveNext()) {
        list($link) = $result->fields;
        $status = xarModAPIFunc('base','user','checklink',
                                array('url' => $link,
                                      'method' => $method,
                                      'skiplocal' => $skiplocal,
                                      'follow' => $follow));
        if (!is_numeric($status)) {
            $date = xarLocaleFormatDate('%x %X %z',time());
            $msg = xarML('#(1) : #(2)/#(3) links checked - #(4)',$date,$count,$numitems,$status);
            xarModVars::set('sitetools','links_checked',$msg);
            $status = -1;
        }
        $dbconn->Execute($update,array($status,$link));
        $count++;
        if ($status > 0 && $count % 10 == 0) {
            $date = xarLocaleFormatDate('%x %X %z',time());
            $msg = xarML('#(1) : #(2)/#(3) links checked',$date,$count,$numitems);
            xarModVars::set('sitetools','links_checked',$msg);
        }
    }
    $result->Close();

    xarModDelVar('sitetools','links_checked');
    // Return the number of items
    return $numitems;
}

?>