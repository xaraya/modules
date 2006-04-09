<?php
/**
 * Overview displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Newsgroups Module
 * @link http://xaraya.com/index.php/release/802.html
 * @author John Cox
 */
/**
 * Overview function that displays the standard Overview page
 */
function newsgroups_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminNewsGroups',0)) return;

    $data=array();

    return xarTplModule('newsgroups', 'admin', 'main', $data,'main');
}

?>
