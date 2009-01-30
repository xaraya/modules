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
 * View a list of items
 *
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array $data array with all information for the template
 */
function twitter_user_view()
{
    xarResponseRedirect(xarModURL('twitter', 'user','main'));
    return true;
}
?>