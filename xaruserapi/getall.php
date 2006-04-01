<?php
/**
* Get all publications
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
 * get all publications
 *
 * @author the eBulletin module development team
 * @param public
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ebulletin_userapi_getall($args)
{
    // security check
    if (!xarSecurityCheck('VieweBulletin')) return;

    extract($args);

    // prepare for database query
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubtable = $xartable['ebulletin'];

    // generate query
    $query = "SELECT * FROM $pubtable\n";
    $bindvars = array();
    if (isset($public)) {
        $query .= "WHERE xar_public = ?\n";
        $bindvars[] = ($public) ? 1 : 0;
    }
    $query .= "ORDER BY xar_name\n";

    // execute query
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // assemble array of publications
    $pubs = array();
    for (; !$result->EOF; $result->MoveNext()) {

        // get params
        list(
            $id, $name, $desc, $public, $from, $fromname, $replyto, $replytoname,
            $subject, $tpl_txt, $tpl_html, $numsago, $unitsago, $startsign, $numsfromnow,
            $unitsfromnow, $endsign
        ) = $result->fields;

        // security check
        if (xarSecurityCheck('VieweBulletin', 0, 'Publication', "$name:$id")) {

            // assemble row of data
            $row = array(
                'id'            => $id,
                'name'          => $name,
                'public'        => $public,
                'desc'          => $desc,
                'from'          => $from,
                'fromname'      => $fromname,
                'replyto'       => $replyto,
                'replytoname'   => $replytoname,
                'subject'       => $subject,
                'tpl_txt'       => $tpl_txt,
                'tpl_html'      => $tpl_html,
                'numsago'       => $numsago,
                'unitsago'      => $unitsago,
                'startsign'     => $startsign,
                'numsfromnow'   => $numsfromnow,
                'unitsfromnow'  => $unitsfromnow,
                'endsign'       => $endsign
            );

            // add to publications array
            $pubs[$id] = $row;
        }
    }
    $result->Close();

    // success
    return $pubs;
}

?>
