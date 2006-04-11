<?php
/**
 * Quick Reply
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_quickreply()
{
    if (!xarVarFetch('fid', 'int:1:', $data['fid'])) return;
    if (!xarVarFetch('title', 'str', $data['ttitle'])) return;
    if (!xarVarFetch('text', 'str', $data['text'])) return;
    if (!xarVarFetch('tid', 'int:1:', $data['tid'])) return;

    return $data;
}
?>