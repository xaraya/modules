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

class TriggerBlockAdmin extends TriggerBlock implements iBlock
{
/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 */
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);
        if (empty($data['showstatus'])) $data['showstatus'] = $this->showtriggerstatus;
        return $data;
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 */
    public function update(Array $data=array())
    {
        $data = parent::update($data);
        if (!xarVarFetch('showstatus', 'int', $showstatus, 0, XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
//        if (empty($data['showstatus'])) $data['showstatus'] = $this->showtriggerstatus;

        $vars = $data;
        $data['content'] = $vars;

        return $data;
    }
}
?>