<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main function for comments module
 *
 */
function comments_user_main($args)
{
    return xarTpl::module('comments', 'user', 'errors', ['layout' => 'no_direct_access']);
}
