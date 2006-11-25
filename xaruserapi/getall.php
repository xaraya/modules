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
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ebulletin_userapi_getall($args)
{
    // security check
    if (!xarSecurityCheck('VieweBulletin')) return;

    extract($args);

    if (!isset($order)) $order = 'name';
    if (!isset($sort)) $sort = 'asc';

    // prepare for database query
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubtable = $xartable['ebulletin'];

    // generate query
    $query = "SELECT
        xar_id
        , xar_template
        , xar_name
        , xar_desc
        , xar_public
        , xar_from
        , xar_fromname
        , xar_replyto
        , xar_replytoname
        , xar_subject
        , xar_html
        , xar_startday
        , xar_endday
        , xar_theme
        FROM $pubtable\n";
    $bindvars = array();
    if (isset($public)) {
        $query .= "WHERE xar_public = ?\n";
        $bindvars[] = ($public) ? 1 : 0;
    }
    $query .= "ORDER BY xar_$order $sort\n";

    // execute query
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // assemble array of publications
    $pubs = array();
    for (; !$result->EOF; $result->MoveNext()) {

        // get params
        list(
            $id, $template, $name, $description, $public, $from, $fromname, $replyto,
            $replytoname, $subject, $html, $startday, $endday, $theme
        ) = $result->fields;

        // security check
        if (xarSecurityCheck('VieweBulletin', 0, 'Publication', "$name:$id")) {

            // assemble row of data
            $row = array(
                'id'            => $id,
                'template'      => $template,
                'name'          => $name,
                'public'        => $public,
                'description'   => $description,
                'from'          => $from,
                'fromname'      => $fromname,
                'replyto'       => $replyto,
                'replytoname'   => $replytoname,
                'subject'       => $subject,
                'html'          => $html,
                'startday'      => $startday,
                'endday'        => $endday,
                'theme'         => $theme,
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
