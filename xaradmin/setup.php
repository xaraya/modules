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
 * Set up the otps
 *
 */
function otp_admin_setup($args)
{
    if (!xarSecurity::check('ManageOtp')) {
        return;
    }
    // Define which object will be shown
    if (!xarVar::fetch('algorithm', 'str', $algorithm, xarModVars::get('otp', 'algorithm'), xarVar::DONT_SET)) {
        return;
    }
//    if (!xarVar::fetch('seed',      'str', $seed,      '', xarVar::DONT_SET)) return;
    if (!xarVar::fetch('input', 'str', $input, 'BoDe Hop JaKe Stow juT RAP', xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('seq', 'str', $seq, xarModVars::get('otp', 'sequence'), xarVar::DONT_SET)) {
        return;
    }

    // Generate a seed
    try {
        $seed = xarMod::apiFunc('otp', 'admin', 'generate_seed');
    } catch (Exception $e) {
        return xarTpl::module('otp', 'user', 'errors', array('layout' => 'no_seed_generated'));
    }
    
    $user_ident = 'marc@luetolf-carroll.com';
    
    // Generate an initial password
    $pp_array = xarMod::apiFunc('otp', 'admin', 'generate_otp', array(
                                                            'user_ident' => $user_ident,
                                                            'passphrase' => $input,
                                                            'seed'       => $seed,
                                                            'sequence'   => $seq,
                                                            'algorithm'  => $algorithm));
    $passphrase = $pp_array['hex_otp'];

    // Generate the password array
    $array = xarMod::apiFunc('otp', 'admin', 'setup', array(
                                                            'passphrase' => $passphrase,
                                                            'seed' => $seed,
                                                            'seq_number' => $seq,
                                                            'algorithm' => $algorithm));
    var_dump($array);
    exit;
    $data = array();
    return $data;
}
