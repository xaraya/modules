<?php
/**
* Create a new publication
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
 * create a new publication
 *
 * @author the eBulletin module development team
 * @param  $args['from'] the from of the item to be created

 * all the args here...

 * @returns int
 * @return ebulletin item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function ebulletin_adminapi_create($args)
{
    extract($args);

    // validate vars
    $invalid = array();
    $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';
    if (empty($from) || !is_string($from) || !preg_match($email_regexp, $from)) {
        $invalid[] = 'from';
    }
    if (!empty($replyto) && !preg_match($email_regexp, $replyto)) {
        $invalid[] = 'replyto';
    }
    if (empty($subject) || !is_string($subject)) {
        $invalid[] = 'subject';
    }
    if (empty($tpl_html) && empty($tpl_txt)) {
        $invalid[] = 'template';
    }
    if (!is_numeric($numsago) || $numsago < 0) {
        $invalid[] = 'numsago';
    }
    if (!is_numeric($numsfromnow) || $numsfromnow < 0) {
        $invalid[] = 'numsfromnow';
    }

    // throw error if bad data
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'create', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication')) return;

    // handle checkboxes
    $public = (isset($public) && $public) ? 1 : 0;

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubtable = $xartable['ebulletin'];

    // get the next available ID in the table
    $nextId = $dbconn->GenId($pubtable);

    // generate query
    $query = "
        INSERT INTO $pubtable (
            xar_id,
            xar_name,
            xar_desc,
            xar_public,
            xar_from,
            xar_fromname,
            xar_replyto,
            xar_replytoname,
            xar_subject,
            xar_tpl_html,
            xar_tpl_txt,
            xar_numsago,
            xar_unitsago,
            xar_startsign,
            xar_numsfromnow,
            xar_unitsfromnow,
            xar_endsign
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bindvars = array(
        $nextId, $name, $desc, $public, $from, $fromname, $replyto, $replytoname, $subject,
        $tpl_html, $tpl_txt, $numsago, $unitsago, $startsign, $numsfromnow, $unitsfromnow, $endsign
    );
    $result = $dbconn->Execute($query, $bindvars);

    // if query failed, return
    if (!$result) return;

    // double-check the ID that was created
    $id = $dbconn->PO_Insert_ID($pubtable, 'xar_id');

    // call create hooks
    $item = $args;
    $item['module'] = 'ebulletin';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'create', $id, $item);

    // success
    return $id;
}

?>
