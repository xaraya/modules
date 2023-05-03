<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.calendar.xarblocks.month');

class Calendar_MonthBlockAdmin extends Calendar_MonthBlock
{
    function modify()
    {
        $data = $this->getContent();
        return $data;
    }

    function update(Array $data=array())
    {
        $args = array();
        xarVar::fetch('targetmodule',   'str',     $args['targetmodule'],  $this->targetmodule, xarVar::NOT_REQUIRED);
        xarVar::fetch('targettype',     'str',     $args['targettype'],    $this->targettype, xarVar::NOT_REQUIRED);
        xarVar::fetch('targetfunc',     'str',     $args['targetfunc'],    $this->targetfunc, xarVar::NOT_REQUIRED);

        $this->setContent($args);
        return true;        
    }
}
?>