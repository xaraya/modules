<?php
/**
* Regenerate an issue
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
 * Regenerate issue
 *
 * @param  $args['id'] the issue to regenerate
 */
function ebulletin_admin_regenerateissue($args)
{
    // security check
    if (!xarSecConfirmAuthKey()) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'id', $id, $id, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return', 'str:1:', $return, $return, XARVAR_NOT_REQUIRED)) return;

    // validate vars
    $invalid = array();
    if (empty($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }

    // throw error if bad data
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'regenerateissue', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // retrieve issue
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('EditeBulletin', 1, 'Publication', "$issue[pubname]:$issue[pid]")) return;

    // let API function do the regenerating
    $id = xarModAPIFunc('ebulletin', 'admin', 'regenerateissue', array('id' => $id));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set status message and redirect to publications view page
    xarSessionSetVar('statusmsg', xarML('Publication successfully regenerated!'));
    $return = empty($return) ? xarModURL('ebulletin', 'admin', 'viewissues') : $return;
    xarResponseRedirect($return);

    // success
    return true;
}

?>
