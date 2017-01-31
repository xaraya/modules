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
 * Generate a one time password
 *
 * user_ident varchar a string that uniquely defines the user (email, Xaraya userID, user name etc.)
 * passphrase varchar a string that is assigned or given by the user as an initial password
 * seed       varchar a string that represents a seed
 * seq_number integer a number that identifies the current position in the sequence of otps
 * algorithm  varchar the algorithm being used for calculations (e.g. MD5)
 */

function otp_adminapi_generate_otp($args)
{
    // Get an instane of the otp class
    sys::import('modules.otp.xarincludes.php-otp.Otp');
    $otp = new Otp();

    // We need an initial passphrase to generate an OTP
    if (!isset($args['passphrase'])) return false;
    // If not user identification is passed, we assume the ident is being used as the passphrase
    if (!isset($args['user_ident'])) $args['user_ident'] = $args['passphrase'];

    // Check if we already have a otp sequence generated for this user
    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
    
    $q = new Query('SELECT', $tables['otp_otps']);
    $q->eq('user_ident',  $args['user_ident']);
    $q->run();
    $result = $q->row();
    
    // We already have a sequence. Check whether it has expired
    if (!empty($result)) {
        if ($result['time_expires'] > time()) {
            // Still not expired: return it
            $one_time = $otp->generateOtp($result['passphrase'], $result['seed'], $result['sequence'], $result['algorithm']);
            return $one_time;
        } else {
            // Expired: delete it and continue below
            $q = new Query('DELETE', $tables['otp_otps']);
            $q->eq('user_ident',  $args['user_ident']);
            $q->run();
        }
    }
    
    // Make sure we have all the other needed data
    if (!isset($args['save'])) $args['save'] = true;;
    if (!isset($args['seed'])) {
        $args['seed'] = xarMod::apiFunc('otp', 'admin', 'generate_seed');
    }
    if (!isset($args['sequence'])) $args['sequence'] = xarModVars::get('otp', 'sequence');
    if (!isset($args['algorithm'])) $args['algorithm'] = xarModVars::get('otp', 'algorithm');
    
    // Generate the password
    $one_time = $otp->generateOtp($args['passphrase'], $args['seed'], $args['sequence'], $args['algorithm']);
    
    if ($args['save']) {
        // Add this otp to the database
        $passphrase = $one_time['hex_otp'];
        if (empty($q->row())) {
            $q = new Query('INSERT', $tables['otp_otps']);
            $q->addfield('user_ident',   $args['user_ident']);
            $q->addfield('passphrase',   $passphrase);
            $q->addfield('seed',         $args['seed']);
            $q->addfield('sequence',     $args['sequence'] - 1);
            $q->addfield('algorithm',    $args['algorithm']);
            $q->addfield('time_created', time());
            $q->addfield('time_expires', time() + (int)xarModVars::get('otp', 'expires'));
            $q->run();
        }
    }
    
    // Return it
    return $one_time;
}
?>