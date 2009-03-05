<?php
/**
 * Show list of aviable newsgroups
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsgroups
 * @link http://xaraya.com/index.php/release/802.html
 * @author John Cox
 */
/**
 * Show list of aviable newsgroups
 * TODO: Server as argument, Bug 1446
 * @return array
 */

function newsgroups_user_main()
{
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    $server = xarModGetVar('newsgroups', 'server');
    $data = array();
    $data['server'] = "news://$server";
    xarTplSetPageTitle(xarVarPrepForDisplay($server));

    $data['items'] = xarModAPIFunc('newsgroups','user','getgroups');
    if (!isset($data['items'])) {
        return array();
    }

    return $data;
}

?>
