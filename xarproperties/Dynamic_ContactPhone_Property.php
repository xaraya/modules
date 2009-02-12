<?php
/**
 * Extended Phone Number property
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Base module
 * @link http://xaraya.com/index.php/release/68.html
 */
/*
 * @author Roger Keays <roger.keays@ninthave.net>
 */

/**
 * The extended date property converts the value provided by the javascript
 * calendar into a universal YYYY-MM-DD format for storage in most databases
 * supporting the 'date' type. 
 *
 * The problem with the normal Calendar property is that it converts
 * everything into a UNIX timestamp, and for most C librarys this does not
 * include dates before 1970. (see Xaraya bugs 2013 and 1428)
 */
class Dynamic_ContactPhone_Property extends Dynamic_Property
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
        
        if($value == "invalid") $value = "";
        
        xarModAPIFunc('base','javascript','modulefile',
                      array('module' => 'dossier',
                            'filename' => 'phone.js'));

        $data['country'] = '01';
        $data['NPA'] = '';
        $data['NXX']  = '';
        $data['phone']  = '';
        $data['extension']  = '';
        
        $data['standard'] = 1;

        if (empty($value)) {
            $value = '';
        } elseif (is_array($value)) {
            $data['NPA'] = $value['NPA'];
            $data['NXX']  = $value['NXX'];
            $data['phone']  = $value['phone'];

        } elseif (preg_match('/(\d{1})-(\d{3})-(\d{3})-(\d{4}) x(\d{1,6})/', $value, $matches)) {
            // uses both country code and extension
            $data['country'] = $matches[1];
            $data['NPA'] = $matches[2];
            $data['NXX']  = $matches[3];
            $data['phone']  = $matches[4];
            $data['extension']  = $matches[5];
        } elseif (preg_match('/(\d{1})-(\d{3})-(\d{3})-(\d{4})/', $value, $matches)) {
            // uses country code
            $data['country'] = $matches[1];
            $data['NPA'] = $matches[2];
            $data['NXX']  = $matches[3];
            $data['phone']  = $matches[4];
        } elseif (preg_match('/(\d{3})-(\d{3})-(\d{4}) x(\d{1,6})/', $value, $matches)) {
            // uses extension
            $data['NPA'] = $matches[1];
            $data['NXX']  = $matches[2];
            $data['phone']  = $matches[3];
            $data['extension']  = $matches[4];
        } elseif (preg_match('/(\d{3})-(\d{3})-(\d{4})/', $value, $matches)) {
            // uses just the base fields
            $data['NPA'] = $matches[1];
            $data['NXX']  = $matches[2];
            $data['phone']  = $matches[3];
        } else {
            // non-standard value, display in a normal textbox
            $data['standard'] = 0;
        }
        
        
        $data['onchange'] = isset($onchange) ? $onchange : null;
        $data['format']   = $this->validation;
        $data['name']       = $name;
        $data['id']         = $id;
        $data['value']      = $value;
        $data['tabindex']   = !empty($tabindex) ? $tabindex : 0;
        $data['invalid']    = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';

        return xarTplProperty('dossier', 'phone', 'showinput', $data);
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
        } elseif (is_array($value)) {
            $data['NPA'] = $value['NPA'];
            $data['NXX']  = $value['NXX'];
            $data['phone']  = $value['phone'];

        } elseif (preg_match('/(\d{1,3})-(\d{3})-(\d{3})-(\d{4}) x(\d{1,6})/', $value, $matches)) {
            // uses both country code and extension
            $data['country'] = $matches[1];
            $data['NPA'] = $matches[2];
            $data['NXX']  = $matches[3];
            $data['phone']  = $matches[4];
            $data['extension']  = $matches[5];
        } elseif (preg_match('/(\d{1,3})-(\d{3})-(\d{3})-(\d{4})/', $value, $matches)) {
            // uses country code
            $data['country'] = $matches[1];
            $data['NPA'] = $matches[2];
            $data['NXX']  = $matches[3];
            $data['phone']  = $matches[4];
        } elseif (preg_match('/(\d{3})-(\d{3})-(\d{4}) x(\d{1,6})/', $value, $matches)) {
            // uses extension
            $data['NPA'] = $matches[1];
            $data['NXX']  = $matches[2];
            $data['phone']  = $matches[3];
            $data['extension']  = $matches[4];
        } elseif (preg_match('/(\d{3})-(\d{3})-(\d{4})/', $value, $matches)) {
            // uses just the base fields
            $data['NPA'] = $matches[1];
            $data['NXX']  = $matches[2];
            $data['phone']  = $matches[3];
        } else {
            // non-standard value, display in a normal textbox
            $data['standard'] = 0;
        }
        
        $data['format']   = $this->validation;
        $data['value']      = $value;

        return xarTplProperty('dossier', 'phone', 'showoutput', $data);
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
                              'id'         => 775,
                              'name'       => 'contactphone',
                              'label'      => 'Phone Number',
                              'format'     => '775',
                              'validation' => '',
                              'source'         => '',
                              'dependancies'   => '',
                              'requiresmodule' => 'dossier',
                              'aliases'        => '',
                              'args'           => serialize($args),
                            // ...
                           );
        return $baseInfo;
     }

}


?>
