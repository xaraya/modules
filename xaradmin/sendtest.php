<?php
/**
* Send Test
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
 * Test send an issue to runtime-specified recipients
 *
 * @param  $ 'id' the id of the item to be publishd
 * @param  $ 'confirm' confirm that this item can be publishd
 */
function ebulletin_admin_sendtest($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'int:1:', $id)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // get issue
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (empty($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$issue[pubname]:$issue[id]")) return;

    // Check for confirmation.
    if (empty($confirm)) {

        // add body sizes
        $units = array(xarML(''), xarML('K'), xarML('M'), xarML('G'));
        $htmlsize = $txtsize = '';
        if (!empty($issue['body_html'])) {
            $size = strlen($issue['body_html']);
            $cnt = 0;
            $unit = '';
            $htmlsize = $size;
            while ($size > 1024) {
                $size /= 1024;
                $cnt++;
                $htmlsize = round($size, 1).$units[$cnt];
            }
        }
        if (!empty($issue['body_txt'])) {
            $size = strlen($issue['body_txt']);
            $cnt = 0;
            $unit = '';
            $txtsize = $size;
            while ($size > 1024) {
                $size /= 1024;
                $cnt++;
                $txtsize = round($size, 1).$units[$cnt];
            }
        }

        // get publication
        $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $issue['pid']));
        if (empty($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // initialize template data
        $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

        // get vars
        $authid = xarSecGenAuthKey();

        // set template data
        $data['id'] = $id;
        $data['issue'] = $issue;
        $data['pub'] = $pub;
        $data['authid'] = $authid;
        $data['htmlsize'] = $htmlsize;
        $data['txtsize'] = $txtsize;

        return $data;
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // validate inputs
    // get remaining HTTP vars
    if (!xarVarFetch('to', 'str:1:', $to, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('toname', 'str:0:', $toname, '', XARVAR_NOT_REQUIRED)) return;

    // validate vars
    $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*'
        .'@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';
    if (empty($to) || !preg_match($email_regexp, $to)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'TO email', 'admin', 'sendtest', 'eBulletin');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // call API function to do the publishing
    if (!xarModAPIFunc('ebulletin', 'admin', 'send_test', array('test' => true, 'id' => $id, 'to' => $to, 'toname' => $toname))) return;

    // set status message and return to view
    xarSessionSetVar('statusmsg', xarML('Test issue successfully sent!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'viewissues'));

    // success
    return true;
}

?>