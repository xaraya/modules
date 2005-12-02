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
    if (!xarSecurityCheck('ViewBible')) return;

    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'concordance')); 

    xarTplSetPageTitle(xarML('Concordance')); 

    // get strong's texts
    $texts = xarModAPIFunc('bible', 'user', 'getall',
                             array('state' => 2,
                                   'type' => 2,
                                   'order' => 'sname', 'sort' => 'desc'));
    if (empty($texts)) {
        $data['status'] = xarML('No concordances are installed and active!  Please contact the website administraor.');
        return $data;
    }
    $data['texts'] = $texts;

    // set the default shortname of the text
    $sname = xarSessionGetVar('bible_strongsname');
    if (empty($sname)) {
        // none is set for this session, so use the first one in the texts list
        $sname = $texts[key($texts)]['sname'];
        xarSessionSetVar('bible_strongsname', $sname);
    }
    $data['sname'] = $sname;


    // Return the template variables defined in this function
    return $data; 
} 

?>
