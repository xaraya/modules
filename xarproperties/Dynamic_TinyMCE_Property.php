<?php
/**
 * Dynamic data tinymce WYSIWYG GUI property
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce module
 * @link http://xaraya.com/index.php/release/63.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Handle tinymce wysiwyg textarea property
 * Utilizes JavaScript based WYSIWYG Editor, TinyMCE
 *
 * @author jojodee
 * @package dynamicdata
 */
include_once "modules/dynamicdata/class/properties.php";
class Dynamic_TinyMCE_Property extends Dynamic_Property
{
    var $rows = 10;
    var $cols = 50;
    var $mceclass = 'mceEditor';
    var $wrap = 'soft';

    function Dynamic_TinyMCE_Property($args)
    {
        $this->Dynamic_Property($args);
        /* check validation for allowed rows/cols/mceclass (or values) */
        if (!empty($this->validation)) {
            $this->parseValidation($this->validation);
        }
    }

    function checkInput($name='', $value = null)
    {
        if (empty($name)) {
            $name = 'dd_'.$this->id;
        }
        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;
        if (!isset($value)) {
            if (!xarVarFetch($name, 'isset', $value,  NULL, XARVAR_DONT_SET)) {return;}
        }
        return $this->validateValue($value);
    }
    function validateValue($value = null) 
    {
        if (!isset($value)) {
            $value = $this->value;
        }
        /*  allowable HTML handled by tinymce */
        $this->value = $value;
        return true;
    }

    function showInput($args = array())
    {
        extract($args);

        if (empty($name)) {
            $name = 'dd_' . $this->id;
        }
        if (empty($id)) {
            $id = $name;
        }
        $data = array();

        static $loadedjavascript;
        $xarbaseurl=xarServerGetBaseURL();
        $editorpath = "'.$xarbaseurl.'modules/tinymce/xartemplates/includes/tinymce/jscripts/tiny_mce";
 
        $data['name']     = $name;
        $data['id']       = $id;
        $data['value']    = isset($value) ? xarVarPrepForDisplay($value) : xarVarPrepForDisplay($this->value);
        $data['tabindex'] = !empty($tabindex) ? $tabindex : 0;
        $data['invalid']  = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';
        $data['rows']     = !empty($rows) ? $rows : $this->rows;
        $data['cols']     = !empty($cols) ? $cols : $this->cols;
        $data['mceclass'] = !empty($mceclass) ? $mceclass : $this->mceclass;

        $template="";

        return xarTplProperty('tinymce', 'tinymce', 'showinput', $data);
    }

    function showOutput($args = array())
    {
        extract($args);
        $data=array();

        if (isset($value)) {
            $data['value'] = xarVarPrepHTMLDisplay($value);
        } else {
            $data['value'] = xarVarPrepHTMLDisplay($this->value);
        }
        $template="";
        return xarTplProperty('tinymce', 'tinymce', 'showoutput', $data );
    }

    /* check validation for allowed min/max length (or values) */
    function parseValidation($validation = '')
    {
        if (!empty($this->validation) && strchr($this->validation,':')) {
            list($rows,$cols,$mceclass) = explode(':',$this->validation);
            if ($rows !== '' && is_numeric($rows)) {
                $this->rows = $rows;
            }
            if ($cols !== '' && is_numeric($cols)) {
                $this->cols = $cols;
            }
            if ($mceclass !== '' && is_string($mceclass)) {
                $this->mceclass = $mceclass;
            }
        }
    }

    /**
     * Get the base information for this property.
     *
     * @returns array
     * @return base information for this property
     */
    function getBasePropertyInfo()
    {
        $args = array();
        $baseInfo = array(
                'id'         => 205,
                'name'       => 'xartinymce',
                'label'      => 'TinyMCE GUI Editor',
                'format'     => '5',
                'validation' => '',
                'source'     => '',
                'dependancies' => '',
                'requiresmodule' => 'tinymce',
                'aliases'        => '',
                'args' => serialize( $args ),
                // ...
               );

        return $baseInfo;
    }

    /**
     * Show the current validation rule in a specific form for this property type
     *
     * @param $args['name'] name of the field (default is 'dd_NN' with NN the property id)
     * @param $args['validation'] validation rule (default is the current validation)
     * @param $args['id'] id of the field
     * @param $args['tabindex'] tab index of the field
     * @return string containing the HTML (or other) text to output in the BL template
     */
    function showValidation($args = array())
    {
        extract($args);

        $data = array();
        $data['name']       = !empty($name) ? $name : 'dd_'.$this->id;
        $data['id']         = !empty($id)   ? $id   : 'dd_'.$this->id;
        $data['tabindex']   = !empty($tabindex) ? $tabindex : 0;
        $data['invalid']    = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';

        if (isset($validation)) {
            $this->validation = $validation;
            // check validation for allowed min/max length (or values)
            $this->parseValidation($validation);
        }
	    $data['defaultrows'] = 10;
	    $data['defaultcols'] = 50;
    	$data['defaultmceclass'] = 'mceEditor';

        $data['rows'] = isset($this->rows) ? $this->rows : '';
        $data['cols'] = isset($this->cols) ? $this->cols : '';
        $data['mceclass'] = isset($this->mceclass) ? $this->mceclass : '';
        $data['other'] = '';
        // if we didn't match the above format
        if (!isset($this->min) && !isset($this->max)) {
            $data['other'] = xarVarPrepForDisplay($this->validation);
        }

        /* allow template override by child classes */
        if (!isset($template)) {
            $template = 'tinymce';
        }
        /* Take the example-validation.xd template from the example module and render it */
        return xarTplProperty('tinymce', $template, 'validation', $data);
    }

    /**
     * Update the current validation rule in a specific way for each property type
     *
     * @param $args['name'] name of the field (default is 'dd_NN' with NN the property id)
     * @param $args['validation'] new validation rule
     * @param $args['id'] id of the field
     * @return bool true if the validation rule could be processed, false otherwise
     */
    function updateValidation($args = array())
    {
        extract($args);

        /* in case we need to process additional input fields based on the name */
        if (empty($name)) {
            $name = 'dd_'.$this->id;
        }

        /* do something with the validation and save it in $this->validation */
        if (isset($validation)) {
            if (is_array($validation)) {
                if (isset($validation['rows']) && $validation['rows'] !== '' && is_numeric($validation['rows'])) {
                    $rows = $validation['rows'];
                } else {
                    $rows = '';
                }
                if (isset($validation['cols']) && $validation['cols'] !== '' && is_numeric($validation['cols'])) {
                    $cols = $validation['cols'];
                } else {
                    $cols = '';
                }
                if (isset($validation['mceclass']) && $validation['mceclass'] !== '' && is_string($validation['mceclass'])) {
                    $mceclass = $validation['mceclass'];
                } else {
                    $mceclass = '';
                }
                // we have some minimum and/or maximum length
                if ($rows !== '' || $cols !== '' || $mceclass !== '') {
                    $this->validation = $rows .':'. $cols .':'. $mceclass;

                // we have some other rule
                } elseif (!empty($validation['other'])) {
                    $this->validation = $validation['other'];

                } else {
                    $this->validation = '';
                }
            } else {
                $this->validation = $validation;
            }
        }

        /*tell the calling function that everything is OK */
        return true;
    }
}

?>
