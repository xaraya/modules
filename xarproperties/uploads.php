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
sys::import('modules.base.xarproperties.fileupload');
/**
 * Class to handle file upload properties
 *
 * @package dynamicdata
 */
class UploadProperty extends FileUploadProperty
{
    public $id         = 105;
    public $name       = 'uploads';
    public $desc       = 'Upload';
    public $reqmodules = array('uploads');

    public $display_size                      = 40;
    public $validation_max_file_size          = 0;
//    public $initialization_basepath         = null;
    public $initialization_basedirectory      = 'var/uploads';
    public $initialization_import_directory   = null;
    public $initialization_multiple_files     = TRUE;
    public $initialization_directory_name     = 'User_';
    public $initialization_file_input_methods = array(1,2,3,4);

    /*
    Trusted/local  --> 1
    External       --> 2
    Uploads        --> 3
    Stored         --> 4
    */

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
            $uid = xarSession::getVar('role_id');
            if (!empty($this->initialization_basedirectory) && preg_match('/\{user\}/',$this->initialization_basedirectory)) {
                // Note: we add the userid just to make sure it's unique e.g. when filtering
                // out unwanted characters through xarVarPrepForOS, or if the database makes
                // a difference between upper-case and lower-case and the OS doesn't...
                $udir = xarVarPrepForOS($this->initialization_directory_name) . $uid;
                $this->initialization_basedirectory = preg_replace('/\{user\}/',$udir,$this->initialization_basedirectory);
            }
            if (!empty($this->initialization_import_directory) && preg_match('/\{user\}/',$this->initialization_import_directory)) {
                // Note: we add the userid just to make sure it's unique e.g. when filtering
                // out unwanted characters through xarVarPrepForOS, or if the database makes
                // a difference between upper-case and lower-case and the OS doesn't...
                $udir = xarVarPrepForOS($this->initialization_directory_name) . $uid;
                $this->initialization_import_directory = preg_replace('/\{user\}/',$udir,$this->initialization_import_directory);
            }
        }
        $this->validation_max_file_size = xarModVars::get('uploads', 'file.maxsize');
        $this->initialization_import_directory = xarModVars::get('uploads', 'path.imports-directory');
    }

    /**
     * Validate the value entered
     */
    function validateValue($value = null)
    {
        // TODO: move some of this to the parent
        //if (!parent::validateValue($value)) return false;

        if (isset($this->fieldname)) $name = $this->fieldname;
        else $name = 'dd_'.$this->id;

        // retrieve new value for preview + new/modify combinations
        if (xarVarIsCached('DynamicData.Upload',$name)) {
            $this->value = xarVarGetCached('DynamicData.Upload',$name);
            return true;
        }

        xarModAPILoad('uploads','user');

        if (!empty($this->initialization_file_input_methods)) {
            $typeCheck = 'enum:0:' . _UPLOADS_GET_STORED;
            $typeCheck .= (in_array(2,$this->initialization_file_input_methods))  ? ':' . _UPLOADS_GET_EXTERNAL : '';
            $typeCheck .= (in_array(1,$this->initialization_file_input_methods)) ? ':' . _UPLOADS_GET_LOCAL : '';
            $typeCheck .= (in_array(3,$this->initialization_file_input_methods)) ? ':' . _UPLOADS_GET_UPLOAD : '';
            $typeCheck .= ':-2'; // clear value
        } else {
            $typeCheck = 'enum:0:' . _UPLOADS_GET_STORED;
            $typeCheck .= (xarModVars::get('uploads', 'dd.fileupload.external') == TRUE) ? ':' . _UPLOADS_GET_EXTERNAL : '';
            $typeCheck .= (xarModVars::get('uploads', 'dd.fileupload.trusted') == TRUE) ? ':' . _UPLOADS_GET_LOCAL : '';
            $typeCheck .= (xarModVars::get('uploads', 'dd.fileupload.upload') == TRUE) ? ':' . _UPLOADS_GET_UPLOAD : '';
            $typeCheck .= ':-2'; // clear value
        }

        xarVarFetch($name . '_attach_type', $typeCheck, $data['action'], -3, XARVAR_NOT_REQUIRED);

        switch ($data['action']) {
            case _UPLOADS_GET_UPLOAD:
                if (!xarVarFetch('MAX_FILE_SIZE', "int::$this->validation_max_file_size", $this->validation_max_file_size)) return;
                if (!xarVarValidate('array:1:', $_FILES[$name . '_attach_upload'])) return;

                $upload         =& $_FILES[$name . '_attach_upload'];
                $data['upload'] =& $_FILES[$name . '_attach_upload'];
                if (!$this->validateExtension($upload['name'])) {
                    $this->invalid = xarML('The file type is not allowed');
                    $this->value = null;
                    return false;
                }
                break;
            case _UPLOADS_GET_EXTERNAL:
                // minimum external import link must be: ftp://a.ws  <-- 10 characters total

                if (!xarVarFetch($name . '_attach_external', 'regexp:/^([a-z]*).\/\/(.{7,})/', $import, 0, XARVAR_NOT_REQUIRED)) return;

                if (empty($import)) {
                    // synchronize file associations with empty list
                    if (!empty($moduleid) && !empty($itemid)) {
                        uploads_sync_associations($moduleid, $itemtype, $itemid);
                    }
                    $this->value = null;
                    xarVarSetCached('DynamicData.Upload',$name,$this->value);
                    return true;
                }

                $data['import'] = $import;
                break;
            case _UPLOADS_GET_LOCAL:

                if (!xarVarFetch($name . '_attach_trusted', 'list:regexp:/(?<!\.{2,2}\/)[\w\d]*/', $fileList)) return;

            // CHECKME: use 'imports' name like in db_get_file() ?
                // replace /trusted coming from showinput() again
                $importDir = $this->initialization_import_directory;
                foreach ($fileList as $file) {
                    $file = str_replace('/trusted', $importDir, $file);
                    $data['fileList']["$file"] = xarModAPIFunc('uploads', 'user', 'file_get_metadata',
                                                                array('fileLocation' => "$file"));
                    if (isset($args['fileList']["$file"]['fileSize']['long'])) {
                        $data['fileList']["$file"]['fileSize'] = $args['fileList']["$file"]['fileSize']['long'];
                    }
                }
                break;
            case _UPLOADS_GET_STORED:

                if (!xarVarFetch($name . '_attach_stored', 'list:int:1:', $fileList, 0, XARVAR_NOT_REQUIRED)) return;

                // If we've made it this far, then fileList was empty to start,
                // so don't complain about it being empty now
                if (empty($fileList) || !is_array($fileList)) {
                    // synchronize file associations with empty list
                    if (!empty($moduleid) && !empty($itemid)) {
                        uploads_sync_associations($moduleid, $itemtype, $itemid);
                    }
                    $this->value = null;
                    xarVarSetCached('DynamicData.Upload',$name,$this->value);
                    return true;
                }

                // We prepend a semicolon onto the list of fileId's so that
                // we can tell, in the future, that this is a list of fileIds
                // and not just a filename
                $this->value = ';' . implode(';', $fileList);

                // synchronize file associations with file list
                if (!empty($moduleid) && !empty($itemid)) {
                    uploads_sync_associations($moduleid, $itemtype, $itemid, $fileList);
                }

                    return true;
                break;
            case '-1':
                    return true;
                break;
            case '-2':
                // clear stored value
                    $this->value = null;
                    xarVarSetCached('DynamicData.Upload',$name,$this->value);
                    return true;
                break;
            default:
                if (isset($value)) {
                    if (strlen($value) && $value{0} == ';') {
                        return true;
                    } else {
                    $this->value = null;
                    return false;
                    }
                } else {
                    // If we have managed to get here then we have a NULL value
                    // and $action was most likely either null or something unexpected
                    // So let's keep things that way :-)
                        $this->value = null;
                        return true;
                }
                break;
        }

        if (!empty($data['action'])) {

            if (isset($storeType)) $data['storeType'] = $storeType;

            // This is where the actual saves happen
            $data['override']['upload']['path'] = $this->initialization_basedirectory;
            $list = xarModAPIFunc('uploads','user','process_files', $data);
            $storeList = array();
            foreach ($list as $file => $fileInfo) {
                if (!isset($fileInfo['errors'])) {
                    $storeList[] = $fileInfo['fileId'];
                } else {
                    $this->invalid .= xarML('Invalid upload: #(1)', $fileInfo['fileName'] . " " . $fileInfo['errors'][0]['errorMesg']);
                }
            }
            if (!empty($this->invalid)) return false;
            if (!empty($storeList)) {
                // We prepend a semicolon onto the list of fileId's so that
                // we can tell, in the future, that this is a list of fileIds
                // and not just a filename
                $this->value = ';' . implode(';', $storeList);

                // synchronize file associations with store list
                if (!empty($moduleid) && !empty($itemid)) {
                    uploads_sync_associations($moduleid, $itemtype, $itemid, $storeList);
                }
            } else {
                $this->value = null;
                return false;
            }
        } else {
            return false;
        }
        xarVarSetCached('DynamicData.Upload',$name,$this->value);
        return true;
    }

//    function showInput($name = '', $value = null, $size = 0, $maxsize = 0, $id = '', $tabindex = '')
    /**
     * Show the input form
     */
    function showInput(Array $data = array())
    {
        // inform anyone that we're showing a file upload field, and that they need to use
        // <form ... enctype="multipart/form-data" ... > in their input form
        xarVarSetCached('Hooks.dynamicdata','withupload',1);

        if (!empty($data['name'])) $this->name = $data['name'];
        if (empty($data['name'])) $data['name'] = 'dd_'.$this->id;
        if (!empty($data['value'])) $this->value = $data['value'];
        if (!empty($data['basedir'])) $this->initialization_basedirectory = $data['basedir'];
        if (!empty($data['importdir'])) $this->initialization_import_directory = $data['importdir'];
        if (!empty($data['file_maxsize'])) $this->validation_max_file_size = $data['file_maxsize'];
        if (!empty($data['methods'])) {
            if (!is_array($data['methods'])) {
                $data['methods'] = explode(',',$data['methods']);                
            }
            $this->initialization_file_input_methods = $data['methods'];
        }
        
        $descend = TRUE;

        xarModAPILoad('uploads','user');
        $data['getAction']['LOCAL']       = _UPLOADS_GET_LOCAL;
        $data['getAction']['EXTERNAL']    = _UPLOADS_GET_EXTERNAL;
        $data['getAction']['UPLOAD']      = _UPLOADS_GET_UPLOAD;
        $data['getAction']['STORED']      = _UPLOADS_GET_STORED;
        $data['getAction']['REFRESH']     = _UPLOADS_GET_REFRESH_LOCAL;
    //    $data['id']                       = $id;

        // Set up the trusted method
        if (in_array(1,$this->initialization_file_input_methods)) {
            if (!file_exists($this->initialization_import_directory)) {
                $msg = xarML('Unable to find trusted directory #(1)', $trusted_dir);
                throw new Exception($msg);
            }
            $cacheExpire = xarModVars::get('uploads','file.cache-expire');

        // CHECKME: use 'imports' name like in db_get_file() ?
            // Note: for relativePath, the (main) import directory is replaced by /trusted in file_get_metadata()
            $data['fileList']     = xarModAPIFunc('uploads', 'user', 'import_get_filelist',
                                                  array('fileLocation' => $this->initialization_import_directory,
                                                        'descend'      => $descend,
                                                        // no need to analyze the mime type here
                                                        'analyze'      => FALSE,
                                                        // cache the results if configured
                                                        'cacheExpire'  => $cacheExpire));
        } else {
            $data['fileList']     = array();
        }

        // Set up the stored method
        if (in_array(2,$this->initialization_file_input_methods)) {
            // if there is an override['upload']['path'], try to use that
            if (!empty($this->initialization_basedirectory)) {
                if (file_exists($this->initialization_basedirectory)) {
                    $data['storedList']   = xarModAPIFunc('uploads', 'user', 'db_get_file',
                        // find all files located under that upload directory
                        array('fileLocation' => $this->initialization_basedirectory . '/%'));
                } else {
                    // Note: the parent directory must already exist
                    $result = @mkdir($this->initialization_basedirectory);
                    if ($result) {
                        // create dummy index.html in case it's web-accessible
                        @touch($this->initialization_basedirectory . '/index.html');
                        // the upload directory is still empty for the moment
                        $data['storedList']   = array();
                    } else {
                    // CHECKME: fall back to common uploads directory, or fail here ?
                    //  $data['storedList']   = xarModAPIFunc('uploads', 'user', 'db_getall_files');
                        $msg = xarML('Unable to create an upload directory #(1)', $this->initialization_basedirectory);
                        throw new Exception($msg);
                    }
                }
            } else {
                $data['storedList']   = xarModAPIFunc('uploads', 'user', 'db_getall_files');
            }
        } else {
            $data['storedList']   = array();
        }

        // used to allow selection of multiple files
        $data['multiple_' . $this->id] = $this->initialization_multiple_files;

        if (!empty($this->value)) {
            $data['value'] = $this->value;
            
            // We use array_filter to remove any values from
            // the array that are empty, null, or false
            $aList = array_filter(explode(';', $this->value));

            if (is_array($aList) && count($aList)) {
                $data['inodeType']['DIRECTORY']   = _INODE_TYPE_DIRECTORY;
                $data['inodeType']['FILE']        = _INODE_TYPE_FILE;
                $data['Attachments'] = xarModAPIFunc('uploads', 'user', 'db_get_file',
                                                      array('fileId' => $aList));
                $list = xarModAPIFunc('uploads','user','showoutput',
                                      array('value' => $this->value, 'style' => 'icon', 'multiple' => $this->initialization_multiple_files));

                foreach ($aList as $fileId) {
                    if (!empty($data['storedList'][$fileId])) {
                        $data['storedList'][$fileId]['selected'] = TRUE;
                    } elseif (!empty($data['Attachments'][$fileId])) {
                        // add it to the list (e.g. from another user's upload directory - we need this when editing)
                        $data['storedList'][$fileId] = $data['Attachments'][$fileId];
                        $data['storedList'][$fileId]['selected'] = TRUE;
                    } else {
                        // missing data for $fileId
                    }
                }
            }
        }
        // Jump over the direct parent for now
        return DataProperty::showInput($data);
    }
    
    /**
     * Show the output: a link to the file
     */
    function showOutput(Array $data = array())
    {
        if (empty($data['value'])) $data['value'] = $this->value;
        if (empty($data['multiple'])) $data['multiple'] = $this->initialization_multiple_files;
        if (empty($data['format'])) $data['format'] = 'fileupload';

        // The explode will create an empty indice,
        // so we get rid of it with array_filter :-)
        $data['value'] = array_filter(explode(';', $data['value']));
        if (!$data['multiple']) $data['value'] = array(current($data['value']));

        // make sure to remove any indices which are empty
        $data['value'] = array_filter($data['value']);

        if (is_array($data['value']) && count($data['value'])) {
            $data['attachments'] = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $data['value']));
            if (empty($data['attachments'])) {
                // We probably have just a single file name
                $data['attachments'][] = array('fileDownload' => $this->initialization_basedirectory . "/" . $data['value'][0],
                                             'fileName' => $data['value'][0],
                                             'DownloadLabel' => $data['value'][0],
                                             );
            }
        } else {
            if (empty($data['value'])) {
                $data['attachments'] = array();
            } else {
                 // We probably have just a single file name
                $data['attachments'][] = array('fileDownload' => $this->initialization_basedirectory . "/" . $data['value'][0],
                                             'fileName' => $data['value'][0],
                                             'DownloadLabel' => $data['value'][0],
                                             );
           }
        }

        // Jump over the direct parent because it uses text string field names as file entries
        return DataProperty::showOutput($data);
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


/*
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
    */
}
?>
