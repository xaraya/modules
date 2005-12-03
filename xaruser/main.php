<?php
/**
 * File: $Id:
 *
 * Bible main user function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
function bible_user_main()
{
    // security check
    if (!xarSecurityCheck('ViewBible')) return;

    // get active texts
    $texts = xarModAPIFunc('bible', 'user', 'getall',
        array('state' => 2, 'type' => 1, 'order' => 'sname')
    );

    // if no texts, we have to throw some kind of error
    if (empty($texts)) {
        // API function failed, so return false
        if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return;
        // No API error, so we must not have any texts available.  Send system message.
        } else {
            $msg = xarML('No Bible texts are available for searching or passage lookup!  '
                . 'Sorry, I am unable to proceed.');
            xarErrorSet(XAR_SYSTEM_MESSAGE, '', new SystemException($msg));
            return;
        }
    }

     // get default text for dropdown list
    $sname = xarSessionGetVar('bible_sname');
    if (empty($sname)) {
        // none is set for this session, so use the first one in the texts list
        $sname = empty($texts) ? '' : $texts[key($texts)]['sname'];
        xarSessionSetVar('bible_sname', $sname);
    }

    // set page title
    xarTplSetPageTitle(xarML('Quick Search'));

    // initialzie template data
    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'main'));

    // set template vars
    $data['texts'] = $texts;
    $data['sname'] = $sname;

    return $data;
}

?>
