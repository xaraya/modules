<?php
/**
 * Surveys table definitions function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Short Description [REQUIRED one line description]
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return int  type and name returned                       [OPTIONAL A REQURIED]
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Deprecated [release version here]             [AS REQUIRED]
 */
/*
 * Lookups on the status table.
 * $args['return'] - just return the named element
 */

function surveys_userapi_lookupstatuses($args) {
    //  Expand arguments.
    extract($args);

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $bind = array();
    $where = array();

    // Selecting a specific status?
    if (isset($status)) {
        $where[] = 'xar_status = ?';
        $bind[] = $status;
    }

    // Selecting a specific system status?
    if (isset($system_status)) {
        $where[] = 'xar_system_status = ?';
        $bind[] = $system_status;
    }

    // The type is specified (this normally would be).
    if (isset($type)) {
        $where[] = 'xar_type = ?';
        $bind[] = $type;
    }

    $query = 'SELECT xar_ssid, xar_type, xar_status, xar_system_status,'
        . ' xar_short_name, xar_desc'
        . ' FROM ' . $xartable['surveys_status']
        . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '');

    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    $statuses = array();
    while (!$result->EOF) {
        list($ssid, $type, $status, $system_status, $short_name, $desc) = $result->fields;

        if (isset($return) && is_string($return) && isset($$return)) {
            // Return a single column if requested.
            // Only return unique values.
            if (!in_array($$return, $statuses)) {
                $statuses[] = $$return;
            }
        } else {
            $statuses[] = array(
                'ssid' => (int)$ssid,
                'type' => $type,
                'status' => $status,
                'system_status' => $system_status,
                'short_name' => $short_name,
                'desc' => $desc
            );
        }

        // Get next status.
        $result->MoveNext();
    }

    // TODO: get DD fields if we are hooked, and handle the
    // language fields if requested.
    // (only do DD if $return is not set)

    return $statuses;
}

?>