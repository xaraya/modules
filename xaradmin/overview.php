<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * Overview displays standard Overview page
 */
function workflow_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('workflow', 'admin', 'main', $data, 'main');
}

?>