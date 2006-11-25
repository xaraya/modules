<?php
/**
* Get a publication
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
 * get a specific item
 *
 * @author the eBulletin module development team
 * @param  $args ['id'] id of ebulletin item to get
 * @return array item array, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ebulletin_userapi_get($args)
{
    extract($args);

    // validate inputs
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'publication ID', 'user', 'get', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubstable = $xartable['ebulletin'];

    // query for all publications
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
        FROM $pubstable WHERE xar_id = ?";
    $result = $dbconn->Execute($query, array($id));
    if (!$result) return;

    // verify that the publication exists
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('Publication #(1) does not exist.', $id);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST', new SystemException($msg));
        return;
    }

    // retrieve params for this publication
    list(
        $id, $template, $name, $description, $public, $from, $fromname, $replyto, $replytoname,
        $subject, $html, $startday, $endday, $theme
    ) = $result->fields;

    $result->Close();

    // security check
    if (!xarSecurityCheck('ReadeBulletin', 1, 'Publication', "$name:$id")) return;

    // put params into an array
    $pub = array(
        'id'            => $id,
        'template'      => $template,
        'name'          => $name,
        'description'   => $description,
        'public'        => $public,
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

    // success
    return $pub;
}

?>
