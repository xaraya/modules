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
 * Manage publication types (all-in-one function for now)
 */

function publications_admin_view_pubtypes()
{
    if (!xarSecurity::check('AdminPublications')) {
        return;
    }

    // Return the template variables defined in this function
    return [];
}
