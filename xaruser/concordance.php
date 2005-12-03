<?php
/**
 * File: $Id:
 *
 * Main Strong's Concordance function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
function bible_user_concordance()
{
    // security check
    if (!xarSecurityCheck('ViewBible')) return;

    // set page title
    xarTplSetPageTitle(xarML('Concordance'));

    // get strong's texts
    $texts = xarModAPIFunc('bible', 'user', 'getall',
        array('state' => 2, 'type' => 2, 'order' => 'sname', 'sort' => 'desc')
    );

    // if no texts, we have to throw some kind of error
    if (empty($texts)) {
        // API function failed, so return false
        if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return;
        // No API error, so we must not have any texts available.  Send system message.
        } else {
            $msg = xarML('Strong\'s Concordance is not currently available!  '
                . 'Sorry, I am unable to proceed.');
            xarErrorSet(XAR_SYSTEM_MESSAGE, '', new SystemException($msg));
            return;
        }
    }

    // get default text for dropdown list
    $sname = xarSessionGetVar('bible_strongsname');
    if (empty($sname)) {
        // none is set for this session, so use the first one in the texts list
        $sname = $texts[key($texts)]['sname'];
        xarSessionSetVar('bible_strongsname', $sname);
    }

    // initialize template data
    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'concordance'));

    // set template vars
    $data['texts'] = $texts;
    $data['sname'] = $sname;

    return $data;
}

?>
