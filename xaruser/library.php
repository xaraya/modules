<?php
/**
 * File: $Id:
 *
 * Show library of all available texts
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
function bible_user_library()
{
    // security check
    if (!xarSecurityCheck('ViewBible')) return;

    // get HTTP vars
    if (!xarVarFetch('sname', 'str:1:', $sname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tid', 'int:1:', $tid, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (!empty($objectid))  $tid = $objectid;

    // validate variables
    $invalid = array();
    if (!empty($sname) && is_numeric($sname)) {
        $invalid[] = 'sname';
    }
    if (!empty($tid) && !is_numeric($tid)) {
        $invalid[] = 'tid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(',', $invalid), 'user', 'library', 'bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // set page title
    xarTplSetPageTitle(xarML('Library'));

    // if given a text, display its Table of Contents
    if (!empty($sname) || !empty($tid)) {

        // get text data
        $args = array();
        if (!empty($tid)) $args['tid'] = $tid;
        if (!empty($sname)) $args['sname'] = $sname;
        $text = xarModAPIFunc('bible', 'user', 'get', $args);

        // if no text, we have to throw some kind of error
        if (empty($text)) {
            // API function failed, so return false
            if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
                return;
            // No API error, so text must exist but is not available(?). Send system message.
            } else {
                $msg = xarML('This text is not currently available!  '
                    . 'Sorry, I am unable to proceed.');
                xarErrorSet(XAR_SYSTEM_MESSAGE, '', new SystemException($msg));
                return;
            }
        }

        // get table of contents
        $toc = xarModAPIFunc('bible', 'user', 'lookup', $text);

        // initialize template data
        $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'library'));

        // set template vars
        $data['sname'] = $sname;
        $data['text'] = &$text;
        $data['toc'] = &$toc;

    // no text was specified, so we list the available texts
    } else {

        // get active texts
        $texts = xarModAPIFunc('bible', 'user', 'getall',
            array('state' => 2, 'type' => 1, 'order' => 'sname')
        );

        // get Strong's texts
        $strongs = xarModAPIFunc('bible', 'user', 'getall',
            array('state' => 2, 'type' => 2, 'order' => 'sname', 'sort' => 'desc')
        );

        // if no texts, we have to throw some kind of error
        if (empty($texts) && empty($strongs)) {
            // API function failed, so return false
            if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
                return;
            // No API error, so text must exist but is not available(?). Send system message.
            } else {
                $msg = xarML('No texts or concordances are available!  '
                    . 'Sorry, I am unable to proceed.');
                xarErrorSet(XAR_SYSTEM_MESSAGE, '', new SystemException($msg));
                return;
            }
        }

        // initialize template data
        $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'library'));

        // set template vars
        $data['texts'] = &$texts;
        $data['strongs'] = &$strongs;

    }

    return $data;
}

?>
