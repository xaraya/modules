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
 * Generate a seed that has never been used
 *
 */

function otp_adminapi_generate_seed($args)
{
    sys::import('modules.otp.xarincludes.php-otp.Otp');
    
    // Get the list of previously used seeds
    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
    $q = new Query('SELECT', $tables['otp_used_seeds']);
    $q->run();
    $used_seeds = array();
    foreach ($q->output() as $row) $used_seeds[] = $row['seed'];
    
    // Generate a new seed
    $otp = new Otp();
    $seed = $otp->generateSeed(1,16,$used_seeds);
    
    // Add it to the list of used seeds
    $q = new Query('INSERT', $tables['otp_used_seeds']);
    $q->addfield('seed', $seed);
    $q->run();
    
    // Return it
    return $seed;
}
?>