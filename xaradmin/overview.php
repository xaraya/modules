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
 */
/**
 * Overview displays standard Overview page
 *
 * @returns array xarTpl::module with $data containing template data
 * @return array containing the menulinks for the overview item on the main manu
 * @since 14 Oct 2005
 */
function comments_admin_overview()
{
    /* Security Check */
    if (!xarSecurity::check('AdminComments', 0)) {
        return;
    }

    $data = [];

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */

    return xarTpl::module('comments', 'admin', 'main', $data, 'main');
}
