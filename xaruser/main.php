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
    if (!xarSecurityCheck('ViewBible')) return;

    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'main'));

    xarTplSetPageTitle(xarML('Quick Search'));

    // get active texts
    $texts = xarModAPIFunc('bible', 'user', 'getall',
                           array('state' => 2,
                                 'type' => 1,
                                 'order' => 'sname'));
    $data['texts'] = $texts;

    // set the default shortname of the text
    $sname = xarSessionGetVar('bible_sname');
    if (empty($sname)) {
        // none is set for this session, so use the first one in the texts list
        $sname = empty($texts) ? '' : $texts[key($texts)]['sname'];
        xarSessionSetVar('bible_sname', $sname);
    }
    $data['sname'] = $sname;


    // Return the template variables defined in this function
    return $data;
}

?>
