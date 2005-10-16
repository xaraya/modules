<?php
/**
 * Main admin function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarlinkme
 * @link http://xaraya.com/index.php/release/889.html
 * @author jojodee <jojodee@xaraya.com>
 */
/**
 * the main administration function
 */
function xarlinkme_admin_main()
{
    if (!xarSecurityCheck('EditxarLinkMe')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        $data = xarModAPIFunc('xarlinkme', 'admin', 'menu');
        // Specify some other variables used in the blocklayout template
        $data['welcome'] = xarML('Welcome to the xarLinkMe Module');
        // Return the template variables defined in this function
        return $data;
    } else {
        // If docs are turned off, then we just return the view page, or whatever
        // function seems to be the most fitting.
        xarResponseRedirect(xarModURL('xarlinkme', 'admin', 'modifyconfig'));
    } 
    // success
    return true;
} 

?>
