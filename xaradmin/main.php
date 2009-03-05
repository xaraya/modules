<?php
/**
 * Default Admin function for Newsgroups
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
 * Main admin gui function, entry point
 *
 * @return bool
 */
function newsgroups_admin_main()
{
    if(!xarSecurityCheck('AdminNewsgroups')) return;

    xarResponseRedirect(xarModURL('newsgroups', 'admin', 'selectgroups'));

    return true;
}

?>