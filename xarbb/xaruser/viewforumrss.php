<?php
/**
 * File: $Id$
 * 
 * Subscribe to a forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_viewforumrss()
{

    $fid = xarVarCleanFromInput('fid');

    // The user API function is called.
    $data = xarModAPIFunc('xarbb',
                          'user',
                          'getforum',
                          array('fid' => $fid));

    if (empty($data)) return;

    // Security Check
    if(!xarSecurityCheck('ReadxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;

    // The user API function is called
    $topics = xarModAPIFunc('xarbb',
                            'user',
                            'getalltopics',
                            array('fid' => $fid));

    // Add the array of items to the template variables
    $data['fid'] = $fid;
    $data['items'] = $topics;

    // Return the template variables defined in this function
    return $data;
}

?>
