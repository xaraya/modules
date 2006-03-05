<?php
/**
 * Chat Module - Port of PJIRC for Xaraya
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Chat Module
 * @link http://xaraya.com/index.php/release/158.html
 * @author John Cox
 */
/**
 * Main user function
 * Correct?:
 * @author Paul Rosania
 * @todo decide whether to use this file or delete it
 */
function chat_user_main($args)
{
    // Security Check
    if(!xarSecurityCheck('ReadChat')) return;
    // fetch some optional 'page' argument or parameter
    extract($args);

    $data = array();
    $data['server'] = xarModGetVar('chat', 'server');
    $data['port']   = xarModGetVar('chat', 'port');
    $data['channel']= xarModGetVar('chat', 'channel');

    xarTplSetPageTitle($data['channel']);
    return $data;
}
?>
