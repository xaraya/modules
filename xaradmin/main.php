<?php
/**
 * icecast main administration function
 *
 * @copyright (C) 2004 by Johnny Robeson
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link
 *
 * @subpackage icecast
 * @author Johnny Robeson
 */
/**
 * Show main template
 */
 function icecast_admin_main()
{
    if (!xarSecurityCheck('AdminIcecast')) return;

    $data = xarModAPIFunc('icecast', 'admin', 'menu');
    $data['welcome'] = xarML('Welcome to the administration part of this icecast module...');
    return $data;
}
?>
