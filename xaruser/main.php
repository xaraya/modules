<?php
/**
 * File: $Id$
 *
 * Base User GUI functions
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage base
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
