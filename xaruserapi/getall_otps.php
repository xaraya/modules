<?php
/**
 * Otp Module
 *
 * @package modules
 * @subpackage otp
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2017 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Return the otps that match a specific passphrase
 *
 */

function otp_userapi_getall_otps($args)
{
    extract($args);
    
    // Load the query class
    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
            
    // Run the SQL
    $q = new Query('SELECT', $tables['otp_otps']);
    
    // Optional filters
    if (isset($passphrase)) $q->eq('passphrase',  $passphrase);
    if (isset($user_ident)) $q->eq('user_ident',  $user_ident);
    if (isset($reference))  $q->eq('reference',  $reference);
    if (isset($seed))       $q->eq('seed',  $seed);
    if (isset($algorithm))  $q->eq('algorithm',  $algorithm);
    if (isset($sequence))   $q->eq('sequence',  $sequence);
    // This checks that the otp starts sooner and expires later than the time passed
    if (isset($time_starts))  $q->gt('time_starts',  $time_starts);
    if (isset($time_expires)) $q->gt('time_expires', $time_expires);
    
    $q->run();
    
    // Index by user identification
    $result = array();
    foreach ($q->output() as $row) $result[$row['user_ident']] = $row;
    
    // If we only have one row, then return just that as a simple array
    // This is a bit dicey, but in fact we usually expect only 1 row here
    if (count($result) == 1)
        $result = reset($result);
        
    return $result;
}

?>