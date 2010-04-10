<?php
/**
 * Dynamic data tinymce WYSIWYG GUI property
 
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xartinymce module
 * @copyright (C) 2004-2010 2skies.com
 * @link http://xarigami.com/projects/xartinymce
 * @author Jo Dalle Nogare <icedlava@2skies.com>
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
    var $classname = NULL; // passed in from GUI
    var $class = NULL; //from template
    var $defaultclass='mceEditor';       
    
  function Dynamic_TinyMCE_Property($args)
  {
        $this->Dynamic_Property($args);

        // Check validation for allowed rows/cols (or values)
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
        // allowable HTML is handled by tinymce
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
        if (!isset($classname)) {
            $classname = $this->classname;
        }
        $data = array();

        $xarbaseurl=xarServerGetBaseURL();
        $editorpath = "'.$xarbaseurl.'modules/tinymce/xartemplates/includes/tinymce/jscripts/tiny_mce";
         //initialize
        $data['jsstring'] = '';
        $data['gzstring'] = '';
        $config['options'] = array();
        $data['usebutton'] = '';
 
        $data['name']     = $name;
        $data['id']       = $id;
        $data['value']    = isset($value) ? xarVarPrepForDisplay($value) : xarVarPrepForDisplay($this->value);
        $data['tabindex'] = !empty($tabindex) ? $tabindex : 0;
        $data['invalid']  = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';
        $data['rows']     = !empty($rows) ? $rows : $this->rows;
        $data['cols']     = !empty($cols) ? $cols : $this->cols;

        //we allow GUI to override a template class
        $data['defaultconfig'] = xarModGetVar('tinymce','default');
        
        //we could have more than one name in the class from the template so it gets tricky
        $classarray = '';
        $config = $data['defaultconfig']; //by default
        if (!empty($classname)) { //gui classname takes precedence
            $test = xarModAPIFunc('tinymce','user','getall',array('name'=>$classname)); //passed in from GUI
            $test= @current($test);
            if (is_array($test) && !empty($test)) {
                $config = $classname; 
                $class = $classname;
            } 
        
        }elseif (empty($classname) && isset($class) && !empty($class)) { // we might have something from template
            $classarray = explode(' ',$class);
            foreach ($classarray as $templateclass) {
                $test =  xarModAPIFunc('tinymce','user','getall',array('name'=>trim($templateclass)));
                $test = current($test);
                 if (is_array($test) && !empty($test)) { //take the first match for a config
                    $config =$templateclass;
                    $classname = $templateclass;
                    $class = $templateclass;
                    break;
                } 
            }
        } else { //both classname and class are empty
            $config = $data['defaultconfig'];
        }
  
        //we can always have a config for DD, the default, but not a classname.
        if (!empty($config)) {
            $configs = xarModAPIFunc('tinymce','user','getall',array('name'=>$config));
        }
        if (isset($config) && count($config)>0) {
            $config = @current($configs);
        } else {
            //we forgot to check something - show some error
        }
            
        $options = unserialize($config['options']);
        $data['usebutton'] = $options['useswitch'];
        $data['usegzp'] = $options['usegzp'];
        $data['isactive'] = $options['active']; 
        $data['configname'] = $config['name'];
        $data['autoload'] = $options['autoload'];
        $data['tinyloadmode'] = xarModGetVar('tinymce','tinymodeload');
        $data['editor_selector'] = isset($options['editor_selector'])?$options['editor_selector']:$this->defaultclass;
        $data['editor_deselector'] = isset($options['editor_deselector'])?$options['editor_deselector']:'mceNoEditor';

        if (empty($classname)) $classname = $data['editor_selector'];
        if (empty($class)) $class = $classname;
        
        $data['usingdd'] = TRUE;
        $browsers = explode(',',$options['browsers']);      
        //work out whether we need to show the editor - better to do it here instead of the template
        $useragent = xarServerGetVar('HTTP_USER_AGENT');
        $usewysiwyg = 0;
        //we might have had a non-empty classname or class but not match for a config - perhaps it is irrelevant OR a deselector

        if ($classname == $data['editor_deselector']) {//no wysiwyg
            $usewysiwyg = 0;
            $data['jsstring'] = '';
            $data['gzstring']  = '';
            $data['editor_selector'] = $classname; //ie the editor deselector
        }elseif (xarModGetVar('tinymce','activetinymce')  && xarModIsAvailable('tinymce') && $data['isactive']) {
             foreach($browsers as $browsername) {
                 if (preg_match('/'.$browsername.'/i',$useragent)) {
                    $usewysiwyg = 1;
                 }
             }
            $data['jsstring'] = $config['jsstring'];
            $data['gzstring'] = $config['gzstring'];
        }else {
            $usewysiwyg = 0;
            $data['jsstring'] = '';
            $data['gzstring'] = '';
        }     
        
        $data['classname']    = $classname;

        $data['usewysiwyg'] = $usewysiwyg;
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
