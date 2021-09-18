<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * Overview displays standard Overview page
 */
function publications_admin_overview()
{
    /* Security Check */
    if (!xarSecurity::check('EditPublications', 0)) {
        return;
    }

    $data=[];

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */

    return xarTpl::module('publications', 'admin', 'main', $data, 'main');
}
