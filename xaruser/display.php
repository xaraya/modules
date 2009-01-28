<?php
/**
 * Display an item
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Display an item
 *
 * This is a standard function to provide detailed information on a single item
 * available from the module.
 *
 * @author the Example module development team
 * @param  array $args an array of arguments (if called by other modules)
 * @param  int $args['objectid'] a generic object id (if called by other modules)
 * @param  int $args['exid'] the item id used for this example module
 * @return array $data The array that contains all data for the template
 */
function twitter_user_display($args)
{
    xarResponseRedirect(xarModURL('twitter', 'user','main'));
    return true;
}
?>