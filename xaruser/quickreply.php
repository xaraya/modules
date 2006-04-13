<?php
/**
 * Quick Reply
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
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