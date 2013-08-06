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
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.dynamicdata.class.objects.base');

class WorkflowObject extends DataObject
{
    protected function normalize($name, $version = null)
    {
        $name = str_replace(" ","_",$name);
        $name = preg_replace("/[^0-9A-Za-z\_]/",'',$name);
        return $name;
    }

    public function getNormalizedName()
    {
        return self::normalize($this->getName());
    }
}
?>