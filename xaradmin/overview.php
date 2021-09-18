<?php
/**
 * Messages Module
 *
 * @package modules
 * @subpackage messages module
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 * @author Ryan Walker
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Overview function that displays the standard Overview page
 */
function messages_admin_overview()
{
    /* Security Check */
    if (!xarSecurity::check('AdminMessages')) {
        return;
    }

    $data=[];

    return xarTpl::module('messages', 'admin', 'overview', $data);
}
