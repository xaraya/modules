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
 * Standard function to view items
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array
 */
function twitter_admin_view()
{

    xarResponseRedirect(xarModURL('twitter', 'admin', 'main'));

    return true;

}
?>