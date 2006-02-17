<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * handle logging levels property
 * This should be in the 'Log Config' module. Alternative sources for dd properties
 * doesnt seem to be working yet?
 * @author nuncanada <nuncanada@xaraya.com>
 * @package dynamicdata
 */
include_once "modules/dynamicdata/class/properties.php";
class Dynamic_LogLevel_Property extends Dynamic_Property
{
    var $options = array ('Emergency', 'Alert', 'Critical', 'Error', 'Warning', 'Notice', 'Info', 'Debug');
    var $value = array();

    function Dynamic_LogLevel_Property($args)
    {
        $this->Dynamic_Property($args);
    }

    function validateValue($value = null)
    {
        if (!isset($value)) {
            $value = $this->value;
        }

        foreach ($this->options as $option) {
            if (($value[$option] != 'ON') && ($value[$option] != 'OFF'))
            {
                $this->invalid = xarML('selection');
                $this->value = null;
                return false;
            }
        }

        //HACK: Hack to allow to store the array:
        $this->value = serialize($value);

        return true;
    }

//    function showInput($name = '', $value = null, $options = array(), $id = '', $tabindex = '')
    function showInput($args = array())
    {
        extract($args);
        $data = array();

        if (!isset($value)) {
            $value = $this->value;
        }

        if (empty($name)) {
            $name = 'dd_'.$this->id;
        }

        //HACK: Hack to allow to store the array:
        if (!empty($value) && !is_array($value)) {$value = unserialize($value);}

        $data['value']   = $value;
        $data['name']    = $name;
        $data['tabindex'] =!empty($tabindex) ? ' tabindex="'.$tabindex.'" ' : '';
        $data['invalid']  =!empty($this->invalid) ? ' <span class="xar-error">'.xarML('Invalid #(1)', $this->invalid) .'</span>' : '';
        $data['options'] = $this->options;

        $template="";
        return xarTplProperty('logconfig', 'loglevel', 'showinput', $data);
    }

    function showOutput($args = array())
    {
         extract($args);
        if (!isset($value)) {
            $value = $this->value;
        }
        //$out = '';
        $data=array();

        //HACK: Hack to allow to store the array:
        if (!empty($value) && !is_array($value)) {$value = unserialize($value);}

        $data['value']   = $value;
        $data['options'] = $this->options;

        $template="";
        return xarTplProperty('logconfig', 'loglevel', 'showoutput', $data);
        // return $out;
    }

    /**
     * Get the base information for this property.
     *
     * @returns array
     * @return base information for this property
     **/
     function getBasePropertyInfo()
     {
         $args = array();
         $baseInfo = array(
                              'id'         => 107,
                              'name'       => 'loglevel',
                              'label'      => 'Logging Level',
                              'format'     => '107',
                              'validation' => '',
                              'source'         => '',
                              'dependancies'   => '',
//                              'requiresmodule' => 'logconfig',
                              'requiresmodule' => '',
                              'aliases'        => '',
                              'args'           => serialize($args),
                            // ...
                           );
        return $baseInfo;
     }

}

?>
