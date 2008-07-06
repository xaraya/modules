<?php
/**
 * Dynamic data tinymce WYSIWYG GUI property
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
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
include_once "modules/base/xarproperties/Dynamic_TextArea_Property.php";

class Dynamic_TinyMCE_Property extends  Dynamic_TextArea_Property
{
    var $rows = 8;
    var $cols = 35;
    var $wrap = 'soft';
    var $classname='mceEditor'; 
    var $defaultclass='mceEditor';       
    
  function Dynamic_TinyMCE_Property($args)
  {
        $this->Dynamic_Property($args);
         
        /* check validation for allowed rows/cols (or values) */
        if (!empty($this->validation)) {
            $this->parseValidation($this->validation);
        }
    }

    function checkInput($name='', $value = null)
    {
        if (empty($name)) {
            $name = 'dd_'.$this->id;
        }
        // store the fieldname for any validations that needs it (e.g. file uploads)
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
       /*  allowable HTML is handled by tinymce */
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
        //we allow GUI to override a template class
        $data['class']    = !empty($this->classname) ? $this->classname : (!empty($class) ?$class : $this->defaultclass);
        
        $template = (isset($template) && !empty($template)) ? $template : 'tinymce';
        
        return xarTplProperty('tinymce', $template, 'showinput', $data);
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
        
        $template = (isset($template) && !empty($template)) ? $template : 'tinymce';
        return xarTplProperty('tinymce', $template, 'showoutput', $data );
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
        $baseInfo =   array(
                            'id'         => 205,
                            'name'       => 'xartinymce',
                            'label'      => 'TinyMCE GUI Editor',
                            'format'     => '5',
                            'validation' => '',
                            'source'     => '',
                            'dependancies' => '',
                            'requiresmodule' => 'tinymce',
                            'aliases'        => '',
                            'args' => serialize( $args )
                            );

        return $baseInfo;
     }
}

?>