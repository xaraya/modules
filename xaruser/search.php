<?php
 /**
 * File: $Id: 
 * 
 * Interface for advanced search GUI
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
/**
 * advanced keyword search GUI
 * 
 * @param  $args an array of arguments (if called by other modules)
 */
function bible_user_search()
{
    if (!xarSecurityCheck('ViewBible')) return;

    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'search')); 

    xarTplSetPageTitle(xarML('Keyword Search')); 

    // get active texts
    $texts = xarModAPIFunc('bible', 'user', 'getall',
                           array('state' => 2,
                                 'type' => 1,
                                 'order' => 'sname'));
    $data['texts'] = $texts;

    if (empty($texts)) {
        $data['status'] = xarML('No texts are installed and active!  Please contact the website administrator.');
        return $data;
    }

    // get book groups
    list($placeholder, $groupnames) = xarModAPIFunc('bible', 'user', 'getaliases', array('type' => 'groups'));
    $data['groupnames'] = $groupnames;

    // get book aliases
    $aliases = xarModAPIFunc('bible', 'user', 'getaliases');
    $data['aliases'] = $aliases;

    // set the default shortname of the text
    $sname = xarSessionGetVar('bible_sname');
    if (empty($sname)) {
        // none is set for this session, so use the first one in the texts list
        $sname = $texts[key($texts)]['sname'];
        xarSessionSetVar('bible_sname', $sname);
    }
    $data['sname'] = $sname;

    // Return the template variables defined in this function
    return $data; 
} 

?>
