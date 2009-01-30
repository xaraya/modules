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
 * Display an item
 *
 * This is a standard function to provide detailed information on a single item
 * available from the module.
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @param  array $args an array of arguments (if called by other modules)
 * @return array $data The array that contains all data for the template
 */
function twitter_user_display($args)
{
    xarResponseRedirect(xarModURL('twitter', 'user','main'));
    return true;
}
?>