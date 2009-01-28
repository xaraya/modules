<?php
/**
 * View a list of items
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
 * View a list of items
 *
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 *
 * @author the Example module development team
 * @return array $data array with all information for the template
 */
function twitter_user_view()
{
    xarResponseRedirect(xarModURL('twitter', 'user','main'));
    return true;
}
?>