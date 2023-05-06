<?php
/**
 * Hitcount Module
 *
 * @package modules
 * @subpackage hitcount module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */
/**
 * Hooks shows the configuration of hooks for other modules
 *
 * @author the Hitcount module development team
 * @return array xarTpl::module with $data containing template data
 * @since 4 March 2006
 */
function hitcount_admin_hooks()
{
    /* Security Check */
    if(!xarSecurity::check('AdminHitcount',0)) return;

    $data = array();

    return $data;
}

?>
