<?php
/**
 * Twitter Module 
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */

/**
 * The main administration function
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @access public
 * @return true
 */
function twitter_admin_main()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminTwitter',0)) return;
    
    // get the tabs :)
    $data=xarModAPIFunc('twitter', 'user', 'menu', array('modtype' => 'admin', 'modfunc' => 'main'));
    // get current module version for display
    $modname = 'twitter';
    $modid = xarModGetIDFromName($modname);
    $modinfo = xarModGetInfo($modid);
    $data['version'] = $modinfo['version'];
    return $data;

}
?>