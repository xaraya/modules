<?php
/**
 * Workflow Module ItemDisplay GUI Hook Observer
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

namespace Xaraya\Modules\Workflow\HookObservers;

use sys;

sys::import('modules.workflow.class.hookobservers.generic');

class ItemDisplayObserver extends GenericObserver
{
    public $module = 'workflow';
    public $type   = 'user';
    public $func   = 'displayhook';
}
