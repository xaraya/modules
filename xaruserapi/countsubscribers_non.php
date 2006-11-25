<?php
/**
* Count the number of non-registered subscribers
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * utility function to count the number of non-registered subscribers
 *
 * @author the ebulletin module development team
 * @return int number of items held by this module
 * @throws DATABASE_ERROR
 */
function ebulletin_userapi_countsubscribers_non($args)
{
    extract($args);

    // set defaults
    if (empty($filter)) $filter = array();

    // validate vars
    $invalid = array();
    if (isset($filter) && !is_array($filter)) {
        $invalid[] = 'filter';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'userapi', 'countsubscribers_non', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }



    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $substable = $xartable['ebulletin_subscriptions'];
    $pubstable = $xartable['ebulletin'];

    // get count
    $query = "
        SELECT COUNT(1)
        FROM $substable, $pubstable
        WHERE xar_email != ?
        AND $substable.xar_pid = $pubstable.xar_id
    ";
    $bindvars = array('');
    if ($filter) {
        switch($filter['type']) {
        case 'starts':
                $test_pre = '';
                $test_post = '%';
            break;
        case 'ends':
                $test_pre = '%';
                $test_post = '';
            break;
        case 'equals':
                $test_pre = '';
                $test_post = '';
            break;
        case 'contains':
        default:
                $test_pre = '%';
                $test_post = '%';
        }
        switch($filter['col']) {
        case 'pubname':
            $query .= "AND $pubstable.xar_name LIKE ?\n";
            $bindvars[] = "$test_pre$filter[text]$test_post";
            break;
        case 'email':
            $query .= "AND $substable.xar_email LIKE ?\n";
            $bindvars[] = "$test_pre$filter[text]$test_post";
            break;
        case 'name':
        default:
            $query .= "AND $substable.xar_name LIKE ?\n";
            $bindvars[] = "$test_pre$filter[text]$test_post";
        }
    }

    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();

    // success
    return $numitems;
}

?>
