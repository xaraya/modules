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
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.dynamicdata.class.properties.base');

class CrontabProperty extends DataProperty
{
    public $id   = 30126;
    public $name = 'crontab';
    public $desc = 'Crontab';
    public $reqmodules = array('scheduler');

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->filepath   = 'modules/scheduler/xarproperties';
        $this->template   = 'crontab';
        $this->tplmodule   = 'scheduler';
    }

    public function checkInput($name = '', $value = null)
    {
        $name = empty($name) ? 'dd_'.$this->id : $name;
        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;
        if (!isset($value)) {
            list($isvalid, $minutes) = $this->fetchValue($name . '_minute');
            list($isvalid, $hours) = $this->fetchValue($name . '_hour');
            list($isvalid, $days) = $this->fetchValue($name . '_day');
            list($isvalid, $months) = $this->fetchValue($name . '_month');
            list($isvalid, $weekdays) = $this->fetchValue($name . '_weekday');
        }
        if (!isset($minutes) ||!isset($hours) ||!isset($days) ||!isset($months) ||!isset($weekdays)) {
            $this->objectref->missingfields[] = $this->name;
            return null;
        }
        $value = array(
            'minutes' => $minutes,
            'hours' => $hours,
            'days' => $days,
            'months' => $months,
            'weekdays' => $weekdays,
            'nextrun' => 0,
        );
        return $this->validateValue($value);
    }

    public function showInput(Array $data = array())
    {
        $value = $this->getValue();
        if (empty($value)) 
            $this->setValue(array(
                'minutes' => '',
                'hours' => '',
                'days' => '',
                'months' => '',
                'weekdays' => '',
                'nextrun' => 0,
            ));
        if (empty($data['value'])) $data['value'] = $this->getValue();
        return parent::showInput($data);
    }

    public function showOutput(Array $data = array())
    {
        $value = $this->getValue();
        if (empty($value)) 
            $this->setValue(array(
                'minutes' => '',
                'hours' => '',
                'days' => '',
                'months' => '',
                'weekdays' => '',
                'nextrun' => 0,
            ));
        if (empty($data['value'])) $data['value'] = $this->getValue();
        return parent::showOutput($data);
    }

    public function getValue()
    {
        return unserialize($this->value);
    }
    
    public function setValue($value=null)
    {
        $this->value = serialize($value);
    }
    
    function showHidden(Array $data = array())
    {
        $data['name']     = !empty($data['name']) ? $data['name'] : 'dd_'.$this->id;
        $data['id']       = !empty($data['id'])   ? $data['id']   : 'dd_'.$this->id;

        $data['invalid']  = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';
        $value = $this->getValue();
        if (empty($value)) 
            $this->setValue(array(
                'minutes' => '',
                'hours' => '',
                'days' => '',
                'months' => '',
                'weekdays' => '',
                'nextrun' => 0,
            ));
        if (empty($data['value'])) $data['value'] = $this->getValue();

        return parent::showHidden($data);
    }
}

?>
