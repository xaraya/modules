<?php
/**
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Members module
 */

/**
 * Address Property
 * @author Marc Lutolf (mfl@netspan.ch)
 */

sys::import('modules.dynamicdata.xarproperties.subform');

class AddressProperty extends SubFormProperty
{
    public $id         = 30033;
    public $name       = 'address';
    public $desc       = 'Address';
    public $reqmodules = array('members');

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->template  = 'address';
        $this->tplmodule = 'members';
        $this->filepath   = 'modules/members/xarproperties';
    }

    public function showOutput(Array $data = array())
    {
        extract($data);
        if (!isset($value)) $value = $this->value;

        $data['style'] = $this->style;
        $data['value'] = $value;
        if (!empty($this->objectid) && !empty($value)) {
            $object = $this->getObject($value);
            $data['fields'] = $object->getProperties();
        }

        $module    = empty($module)   ? $this->getModule()   : $module;
        $template  = empty($template) ? $this->getTemplate() : $template;
        return xarTplProperty($module, $template, 'showoutput', $data);
    }
}

?>