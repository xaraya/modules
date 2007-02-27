<?php
/**
 * AddressBook Dynamic Phone property
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */
/**
 * @author ckraeft <ckraeft@xaraya.com>
 */
class Dynamic_Phone_Property extends Dynamic_Property
{
    /**
     * We allow two validations: date, and datetime (corresponding to the
     * database's date and datetime data types.
     *
     * We also don't make any modifications for the timezone (too hard).
     */
    function validateValue($value = null)
    {
        if (empty($this->validation)) {
            $this->validation = '';
        }
        if (!isset($value)) {
            $value = $this->value;
        }
        if (empty($value)) {
            $this->value = $value;
            return true;

        } elseif (is_array($value)) {

            if (!empty($value['NPA']) && !empty($value['NXX']) && !empty($value['phone'])) {
                if (is_numeric($value['NPA']) && is_numeric($value['NXX']) && is_numeric($value['phone']) &&
                    strlen($value['NXX']) == 3 && strlen($value['NXX']) == 3 && strlen($value['phone']) == 4) {
                    
                    if(is_numeric($value['extension']) && strlen($value['extension']) > 0) {
                        $this->value = sprintf('%03d-%03d-%04d x%04d',$value['NPA'],$value['NXX'],$value['phone'],$value['extension']);
                    } else {
                        $this->value = sprintf('%03d-%03d-%04d',$value['NPA'],$value['NXX'],$value['phone']);
                    }
                    
                } else {
                    $this->invalid = xarML('phone');
                    $this->value = null;
                    return false;
                }
            } else {
                $this->value = '';
            }
            return true;

        /* sample value: 2004-06-18 18:47:33 */
        } elseif (is_string($value) && preg_match('/\d{3}-\d{3}-\d{4}/', $value) || preg_match('/\d{3}-\d{3}-\d{4}-\d{1,6}/', $value)) {

            /* TODO: use xaradodb to format the date */
            $this->value = $value;
            return true;

        } else {
            $this->invalid = xarML('phone');
            $this->value = null;
            return false;
        }
    } /* validateValue */

    /**
     * Show the input according to the requested dateformat.
     */
    function showInput($args = array())
    {
        extract($args);
        $data = array();

        if (empty($name)) {
            $name = 'dd_'.$this->id;
        }
        if (empty($id)) {
            $id = $name;
        }
        if (!isset($value)) {
            $value = $this->value;
        }
        
        xarModAPIFunc('base','javascript','modulefile',
                      array('module' => 'addressbook',
                            'filename' => 'phone.js'));

        $data['NPA'] = '210';
        $data['NXX']  = '';
        $data['phone']  = '';
        $data['extension']  = '';

        if (empty($value)) {
            $value = '';

        } elseif (preg_match('/(\d{3})-(\d{3})-(\d{4}) x(\d{1,6})/', $value, $matches)) {
            $data['NPA'] = $matches[1];
            $data['NXX']  = $matches[2];
            $data['phone']  = $matches[3];
            $data['extension']  = $matches[4];
        }
        $data['format']   = $this->validation;
        $data['name']       = $name;
        $data['id']         = $id;
        $data['value']      = $value;
        $data['tabindex']   = !empty($tabindex) ? $tabindex : 0;
        $data['invalid']    = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';

        return xarTplProperty('addressbook', 'phone', 'showinput', $data);
    }

    /**
     * Show the output according to the requested dateformat.
     */
    function showOutput($args = array())
    {
        extract($args);

        $data = array();

        if (!isset($value)) {
            $value = $this->value;
        }

        $data['NPA'] = '';
        $data['NXX']  = '';
        $data['phone']  = '';

        // default time is unspecified
        if (empty($value)) {
            $value = '';

        } elseif (preg_match('/(\d{3})-(\d{3})-(\d{4})/', $value, $matches)) {
            $data['NPA'] = $matches[1];
            $data['NXX']  = $matches[2];
            $data['phone']  = $matches[3];

        } elseif (preg_match('/(\d{1})-(\d{3})-(\d{3})-(\d{4})/', $value, $matches)) {
            $data['NPA'] = $matches[2];
            $data['NXX']  = $matches[3];
            $data['phone']  = $matches[4];
        }
        $data['format']   = $this->validation;
        $data['value']      = $value;

        return xarTplProperty('addressbook', 'phone', 'showoutput', $data);
    } /* showOutput */

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
                              'id'         => 420,
                              'name'       => 'phone',
                              'label'      => 'Phone Number',
                              'format'     => '420',
                              'validation' => '',
                              'source'         => '',
                              'dependancies'   => '',
                              'requiresmodule' => 'addressbook',
                              'aliases'        => '',
                              'args'           => serialize($args),
                            // ...
                           );
        return $baseInfo;
     }

}


?>