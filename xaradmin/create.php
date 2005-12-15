<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_admin_create($args)
{
    list($name,
        $displaydates,
        $displayhours,
        $displayfreq,
        $private,
        $sendmails,
        $importantdays,
        $criticaldays,
        $billable,
        $description) =	xarVarCleanFromInput('name',
                                            'displaydates',
                                            'displayhours',
                                            'displayfreq',
                                            'private',
                                            'sendmails',
                                            'importantdays',
                                            'criticaldays',
                                            'billable',
                                            'description');

    extract($args);
    if (!xarSecConfirmAuthKey()) return;

    $projectid = xarModAPIFunc('xproject',
                        'admin',
                        'create',
                        array('name' 		=> $name,
                            'displaydates'	=> $displaydates,
                            'displayhours'	=> $displayhours,
                            'displayfreq'	=> $displayfreq,
                            'private'		=> $private,
                            'sendmails'		=> $sendmails,
                            'importantdays'	=> $importantdays,
                            'criticaldays'	=> $criticaldays,
                            'billable'		=> $billable,
                            'description'	=> $description));


    if (!isset($projectid) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('PROJECTCREATED'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));
//    xarResponseRedirect(xarModURL('xproject', 'user', 'display', array('projectid' => $projectid)));

    return true;
}

?>