<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 * Dynamic Upload Property
 *
 * @package dynamicdata
 * @subpackage properties
 */
/* Include parent class */
sys::import('modules.dynamicdata.class.properties');
/**
 * Class to handle file upload properties
 *
 * @package dynamicdata
 */
class UploadProperty extends DataProperty
{
    public $id         = 105;
    public $name       = 'uploads';
    public $desc       = 'Upload';
    public $reqmodules = array('uploads');

    public $display_size                      = 40;
    public $validation_max_file_size          = 1000000;
//    public $validation_file_extensions      = 'gif|jpg|jpeg|png|bmp|pdf|doc|txt';
//    public $initialization_basepath         = null;
    public $initialization_basedirectory      = 'var/uploads';
    public $initialization_import_directory   = null;
    public $initialization_multiple_files     = TRUE;
    public $initialization_directory_name     = 'User_';
    public $initialization_file_input_methods = array(
                                                'trusted'  => false,
                                                'external' => false,
                                                'upload'   => false,
                                                'stored'   => false);

    // this is used by DataPropertyMaster::addProperty() to set the $object->upload flag
    var $upload = true;

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->tplmodule =  'uploads';
        $this->template =  'uploads';
        $this->filepath   = 'modules/uploads/xarproperties';

        // this is used by DD's importpropertytypes() function
        if (empty($args['skipInit'])) {                            // this parameter is not found in the core code
            // Note : {user} will be replaced by the current user uploading the file - e.g. var/uploads/{user} -&gt; var/uploads/myusername_123
            if (!empty($this->initialization_basedirectory) && preg_match('/\{user\}/',$this->initialization_basedirectory)) {
                $uid = xarUserGetVar('uid');
                // Note: we add the userid just to make sure it's unique e.g. when filtering
                // out unwanted characters through xarVarPrepForOS, or if the database makes
                // a difference between upper-case and lower-case and the OS doesn't...
                $udir = xarVarPrepForOS($initialization_directory_name) . $uid;
                $this->initialization_basedirectory = preg_replace('/\{user\}/',$udir,$this->initialization_basedirectory);
            }
            if (!empty($this->initialization_import_directory) && preg_match('/\{user\}/',$this->initialization_import_directory)) {
                $uid = xarUserGetVar('uid');
                // Note: we add the userid just to make sure it's unique e.g. when filtering
                // out unwanted characters through xarVarPrepForOS, or if the database makes
                // a difference between upper-case and lower-case and the OS doesn't...
                $udir = xarVarPrepForOS($initialization_directory_name) . $uid;
                $this->initialization_import_directory = preg_replace('/\{user\}/',$udir,$this->initialization_import_directory);
            }
        }
    }
    /**
     * Check the input into the uploads property
     */
    function checkInput($name='', $value = null)
    {
        if (empty($name)) $name = 'dd_'.$this->id;

        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;
        if (!isset($value)) {
            if (!xarVarFetch($name, 'isset', $value,  NULL, XARVAR_DONT_SET)) {return;}
        }
        return $this->validateValue($value);
    }
    /**
     * Validate the value entered
     */
    function validateValue($value = null)
    {
        if (!parent::validateValue($value)) return false;

        if (isset($this->fieldname)) $name = $this->fieldname;
        else $name = 'dd_'.$this->id;

        // convert old Upload values if necessary
//        if (!isset($value)) {
//            $value = $this->getValue();
//        }

        // retrieve new value for preview + new/modify combinations
        if (xarVarIsCached('DynamicData.Upload',$name)) {
            $this->value = xarVarGetCached('DynamicData.Upload',$name);
            return true;
        }

        // set override for the upload/import paths if necessary
        if (!empty($this->initialization_basedirectory) || !empty($this->initialization_import_directory)) {
            $override = array();
            if (!empty($this->initialization_basedirectory)) {
                $override['upload'] = array('path' => $this->initialization_basedirectory);
            }
            if (!empty($this->initialization_import_directory)) {
                $override['import'] = array('path' => $this->initialization_import_directory);
            }
        } else {
            $override = null;
        }

        $return = xarModAPIFunc('uploads','admin','validatevalue',
                                array('id' => $name, // not $this->id
                                      'value' => $value,
                                      // pass the module id, item type and item id (if available) for associations
                                      'moduleid' => $this->_moduleid,
                                      'itemtype' => $this->_itemtype,
                                      'itemid'   => !empty($this->_itemid) ? $this->_itemid : null,
                                      'multiple' => $this->initialization_multiple_files,
                                      'format' => 'upload',
                                      'methods' => $this->initialization_file_input_methods,
                                      'override' => $override,
                                      'maxsize' => $this->validation_max_file_size));
        if (!isset($return) || !is_array($return) || count($return) < 2) {
            $this->value = null;
            return false;
        }
        if (empty($return[0])) {
            $this->value = null;
            $this->invalid = xarML('value');
            return false;
        } else {
            if (empty($return[1])) {
                $this->value = '';
            } else {
                $this->value = $return[1];
            }
            // save new value for preview + new/modify combinations
            xarVarSetCached('DynamicData.Upload',$name,$this->value);
            return true;
        }
    }

//    function showInput($name = '', $value = null, $size = 0, $maxsize = 0, $id = '', $tabindex = '')
    /**
     * Show the input form
     */
    function showInput(Array $args = array())
    {
        extract($args);

        if (empty($name)) $name = 'dd_'.$this->id;

        // convert old Upload values if necessary
        if (!isset($value)) $value = $this->getValue();

        // inform anyone that we're showing a file upload field, and that they need to use
        // <form ... enctype="multipart/form-data" ... > in their input form
        xarVarSetCached('Hooks.dynamicdata','withupload',1);

        // set override for the upload/import paths if necessary
        if (!empty($this->initialization_basedirectory) || !empty($this->initialization_import_directory)) {
            $override = array();
            if (!empty($this->initialization_basedirectory)) {
                $override['upload'] = array('path' => $this->initialization_basedirectory);
            }
            if (!empty($this->initialization_import_directory)) {
                $override['import'] = array('path' => $this->initialization_import_directory);
            }
        } else {
            $override = null;
        }

        return xarModAPIFunc('uploads','admin','showinput',
                             array('id' => $name, // not $this->id
                                   'value' => $value,
                                   'multiple' => $this->initialization_multiple_files,
                                   'methods' => $this->initialization_file_input_methods,
                                   'override' => $override,
                                   'format' => 'upload',
                                   'invalid' => $this->invalid));
    }
    /**
     * Show the output: a link to the file
     */
    function showOutput(Array $args = array())
    {
        extract($args);

        // convert old Upload values if necessary
        if (!isset($value)) {
            $value = $this->getValue();
        }

        return xarModAPIFunc('uploads','user','showoutput',
                             array('value' => $value,
                                   'format' => 'upload',
                                   'multiple' => $this->initialization_multiple_files));
    }

    /**
     * Get the value of this property (= for a particular object item)
     *
     * (keep this for compatibility with old Uploads values)
     *
     * @return mixed the value for the property
     */
    function getValue()
    {
        $value = $this->value;

        if (empty($value)) {
            return $value;
        // For current values when DD stored the ULID
        } elseif ( is_numeric($value) ) {
            $ulid = ";$value";
        // For old values, pull the ULID from the URL that is stored
        } elseif (strstr($value, 'ulid=')) {
            ereg('ulid=([0-9]+)',$value,$reg);
            $ulid = ";$reg[1]";
        // For new values when DD stores a ;-separated list
        } elseif (strstr($value, ';')) {
            $ulid = $value;
        }
        if (empty($ulid)) {
            $ulid = NULL;
        }
        return $ulid;
    }


    function parseValidation($validation = '')
    {
        list($multiple, $methods, $basedir, $importdir) = xarModAPIFunc('uploads', 'admin', 'dd_configure', $validation);

        $this->initialization_multiple_files = $multiple;
        $this->initialization_methods = $methods;
        $this->initialization_basedirectory = $basedir;
        $this->initialization_import_directory = $importdir;
        $this->maxsize = xarModVars::get('uploads', 'file.maxsize');
    }

    function showValidation(Array $args = array())
    {
        extract($args);

        $data = array();
        $data['name']       = !empty($name) ? $name : 'dd_'.$this->id;
        $data['id']         = !empty($id)   ? $id   : 'dd_'.$this->id;
        $data['tabindex']   = !empty($tabindex) ? $tabindex : 0;
        $data['invalid']    = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';

        $data['size']       = !empty($size) ? $size : 50;
        $data['maxlength']  = !empty($maxlength) ? $maxlength : 254;

        if (isset($validation)) {
            $this->validation = $validation;
            $this->parseValidation($validation);
        }

        $data['multiple'] = $this->initialization_multiple_files;
        $data['methods'] = $this->initialization_file_input_methods;
        $data['basedir'] = $this->initialization_basedirectory;
        $data['importdir'] = $this->initialization_import_directory;
        $data['other'] = '';

        // allow template override by child classes
        if (!isset($template)) {
            $template = '';
        }
        return xarTplProperty('uploads', 'upload', 'validation', $data);
    }

    function updateValidation(Array $args = array())
    {
        extract($args);

        // in case we need to process additional input fields based on the name
        if (empty($name)) {
            $name = 'dd_'.$this->id;
        }
        // do something with the validation and save it in $this->validation
        if (isset($validation)) {
            if (is_array($validation)) {
                if (!empty($validation['other'])) {
                    $this->validation = $validation['other'];

                } else {
                    $this->validation = '';
                    if (!empty($validation['multiple'])) {
                        $this->validation = 'multiple';
                    } else {
                        $this->validation = 'single';
                    }
// CHECKME: verify format of methods(...) part
                    if (!empty($validation['methods'])) {
                        $todo = array();
                        foreach (array_keys($this->methods) as $method) {
                            if (!empty($validation['methods'][$method])) {
                                $todo[] = '+' .$method;
                            } else {
                                $todo[] = '-' .$method;
                            }
                        }
                        if (count($todo) > 0) {
                            $this->validation .= ';methods(';
                            $this->validation .= join(',',$todo);
                            $this->validation .= ')';
                        }
                    }
                    if (!empty($validation['basedir'])) {
                        $this->validation .= ';basedir(' . $validation['basedir'] . ')';
                    }
                    if (!empty($validation['importdir'])) {
                        $this->validation .= ';importdir(' . $validation['importdir'] . ')';
                    }
                }
            } else {
                $this->validation = $validation;
            }
        }

        // tell the calling function that everything is OK
        return true;
    }
}
?>
