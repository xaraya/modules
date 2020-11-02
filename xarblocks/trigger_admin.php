<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
        if (!xarVar::fetch('showstatus', 'checkbox', $vars['showstatus'], 0, xarVar::DONT_SET)) {
            return;
        }
        $this->setContent($vars);
        return true;
    }
}
