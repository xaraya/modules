<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * Manage block
 *
 * @author  John Cox <admin@dinerminor.com>
 * @access  public
 * @param   none
 * @return  nothing
 * @throws  no exceptions
 * @todo    nothing
*/
sys::import('modules.scheduler.xarblocks.trigger');

class Scheduler_TriggerBlockAdmin extends Scheduler_TriggerBlock implements iBlock
{
/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 */
    public function modify()
    {
        return $this->getContent();
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 */
    public function update()
    {
        $vars = array();
        if(!xarVarFetch('showstatus',  'checkbox', $vars['showstatus'],  0, XARVAR_DONT_SET)) {return;}
        $this->setContent($vars);
        return true;
    }

}
?>