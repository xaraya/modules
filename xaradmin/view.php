<?php
/**
 * Standard function to view items
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
 * Standard function to view items
 *
 * @author Example module development team
 * @return array
 */
function twitter_admin_view()
{

    xarResponseRedirect(xarModURL('twitter', 'admin', 'modifyconfig'));

    return true;

}
?>