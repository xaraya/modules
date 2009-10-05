<?php
/**
 * Hooks shows the configuration of hooks for other modules
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */
/**
 * Hooks shows the configuration of hooks for other modules
 *
 * @author the Changelog module development team
 * @return array xarTplModule with $data containing template data
 * @since 4 March 2006
 */
function changelog_admin_hooks()
{
    /* Security Check */
    if(!xarSecurityCheck('AdminChangelog',0)) return;

    $data = array();

    return $data;
}

?>
