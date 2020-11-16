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
 */
/**
 * Configure hooks by hook module
 *
 * @author Xaraya Development Team
 * @param $args['curhook'] current hook module (optional)
 * @param $args['return_url'] URL to return to after updating the hooks (optional)
 * @return array data for the template display
 *
 */
function keywords_admin_hooks(array $args=array())
{
    // Security
    if (!xarSecurity::check('ManageKeywords')) {
        return;
    }

    $data = array();
    return $data;
}
