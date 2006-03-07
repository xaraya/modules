<?php
/**
 * Overview for Workflow
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow
 * @link http://xaraya.com/index.php/release/188.html
 * @author mikespub
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