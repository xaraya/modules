<?php
/**
 * Polls Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * main user display function
 *
 * Redirect user to the listing of polls
 */
function polls_user_main($args)
{
    return xarModFunc('polls', 'user', 'list', $args);
}

?>
