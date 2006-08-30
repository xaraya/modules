<?php
/**
 * Expire non-validated accounts or whatever via Scheduler
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * expire non-validated accounts or whatever (executed by the scheduler module)
 *
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 * @access private
 * @todo MichelV: can we deprecate this?
 */
function registration_schedulerapi_expire($args)
{

// TODO: get some configuration info here if necessary
    // $whatever = xarModGetVar('authentication','whatever');
    // ...
// TODO: we need some API function here (not a GUI function)
//       It may return true (or some logging text) if it succeeds, and null if it fails
    // return xarModAPIFunc('authentication','admin','...',
    //                      array('whatever' => $whatever));

    return true;
}

?>