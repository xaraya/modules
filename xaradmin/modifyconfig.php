<?php
/**
 * @package ie7
 * @copyright (C) 2004 by Ninth Avenue Software Pty Ltd
 * @link http://www.ninthave.net
 * @author Roger Keays <roger.keays@ninthave.net>
 */


/**
 * Modify ie7 configuration.
 */
function ie7_admin_modifyconfig($args)
{ 
    /* locals */
    extract($args);
    $data = array();

    /* security check */
    if (!xarSecurityCheck('AdminIE7')) return; 

    /* get values */
    $data['enabled'] = xarModGetVar('ie7', 'enabled');

    /* pass to the template */
    return $data;
} 

?>
