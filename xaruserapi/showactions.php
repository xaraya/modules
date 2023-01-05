<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * show the actions available to you for this workflow, subjectId and place (called via <xar:workflow-actions tag)
 *
 * @author mikespub
 * @access public
 */
function workflow_userapi_showactions($args)
{
    // Security Check
    if (!xarSecurity::check('ReadWorkflow', 0)) {
        return '';
    }

    sys::import('modules.workflow.class.config');
    $tplData = $args;

    if (!empty($args['template'])) {
        return xarTpl::module('workflow', 'user', 'showactions', $tplData, $args['template']);
    } else {
        return xarTpl::module('workflow', 'user', 'showactions', $tplData);
    }
}
