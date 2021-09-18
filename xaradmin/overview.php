<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 */
function keywords_admin_overview()
{
    /* Security Check */
    if (!xarSecurity::check('AdminKeywords', 0)) {
        return;
    }

    $data=[];

    return xarTpl::module('keywords', 'admin', 'main', $data, 'main');
}
