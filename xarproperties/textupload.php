<?php
/**
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage base
 * @link http://xaraya.com/index.php/release/68.html
 * @author mikespub <mikespub@xaraya.com>
 */
/* Include parent class */
sys::import('modules.base.xarproperties.fileupload');

/**
 * Handle text upload property
 */
class TextUploadProperty extends FileUploadProperty
{
    public $id         = 38;
    public $name       = 'textupload';
    public $desc       = 'Text Upload';
    public $reqmodules = array('articles');

    public $display_rows = 20;
    public $display_columns = 50;

    // Don't remove leftover values here ... because text might contain ; as well :-)
    public $remove_leftover_values = false;

    // Remove head and tail from uploaded HTML documents
    public $filterhead = '#^.*?<body[^>]*>#is';
    public $filtertail = '#</body.*$#is';

/*
    public $size = 40;
    public $maxsize = 1000000;
    public $methods = array('trusted'  => false,
                            'external' => false,
                            'upload'   => false,
                            'stored'   => false);
    public $basedir = null;
    public $importdir = null;

    // this is used by DataPropertyMaster::addProperty() to set the $object->upload flag
    public $upload = true;
*/
    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->tplmodule = 'articles';
        $this->template  = 'textupload';
        $this->filepath   = 'modules/articles/xarproperties';

        if ($this->UploadsModule_isHooked) {
        } else {
            $this->validation_file_extensions = 'txt|html?';
            // TODO: support automatic conversion of doc, rtf and/or pdf someday ?
        }
/*
        // Note : {user} will be replaced by the current user uploading the file - e.g. var/uploads/{user} -&gt; var/uploads/myusername_123
        if (!empty($this->basedir) && preg_match('/\{user\}/',$this->basedir)) {
            $uname = xarUserGetVar('uname');
            $uname = xarVarPrepForOS($uname);
            $id = xarUserGetVar('id');
            // Note: we add the userid just to make sure it's unique e.g. when filtering
            // out unwanted characters through xarVarPrepForOS, or if the database makes
            // a difference between upper-case and lower-case and the OS doesn't...
            $udir = $uname . '_' . $id;
            $this->basedir = preg_replace('/\{user\}/',$udir,$this->basedir);
        }
        if (!empty($this->importdir) && preg_match('/\{user\}/',$this->importdir)) {
            $uname = xarUserGetVar('uname');
            $uname = xarVarPrepForOS($uname);
            $id = xarUserGetVar('id');
            // Note: we add the userid just to make sure it's unique e.g. when filtering
            // out unwanted characters through xarVarPrepForOS, or if the database makes
            // a difference between upper-case and lower-case and the OS doesn't...
            $udir = $uname . '_' . $id;
            $this->importdir = preg_replace('/\{user\}/',$udir,$this->importdir);
        }
*/
    }

    public function validateValue($value = null)
    {
        // TODO: the behaviour of this property is supposed to be
        //       - without uploads module hooked: add content of temporary uploaded file to text
        //                                        and then delete uploaded file again
        //       - with uploads module hooked: add magic link to uploaded file as #...:NN# in text

        // save old text value for later
        if (isset($this->value)) {
            $this->oldvalue = $this->value;
            $this->value = '';
        } else {
            $this->oldvalue = '';
        }

        // set the property to use a temporary file if the uploads module is not hooked
        if ($this->UploadsModule_isHooked) {
        } else {
            $this->use_temporary_file = true;
        }

        // the variable corresponding to the file upload field is no longer set in PHP 4.2.1+
        // but we're using a textarea field to keep track of any previously uploaded file here
        if (!parent::validateValue($value)) return false;

        // check if we updated the old text value too
        if (isset($this->fieldname)) $name = $this->fieldname;
        else $name = 'dd_'.$this->id;

        $textname = $name . '_text';
        if (!xarVarFetch($textname, 'str', $textvalue, NULL, XARVAR_NOT_REQUIRED)) return;
        if (isset($textvalue)) {
            $this->oldvalue = $textvalue;
        }

        if ($this->UploadsModule_isHooked) {
            // show magic link #...:NN# to file in text (cfr. transform hook in uploads module)
            $magiclinks = '';
            if (!empty($this->value)) {
                $magiclinks = xarMod::apiFunc('uploads','user','showoutput',
                                            array('value' => $this->value,
                                                  'format' => 'textupload',
                                                  'style' => 'icon'));
                // strip template comments if necessary
                $magiclinks = preg_replace('/<\!--.*?-->/','',$magiclinks);
                $magiclinks = trim($magiclinks);
            }
            if (!empty($this->oldvalue) && !empty($magiclinks)) {
                $this->oldvalue .= ' ' . $magiclinks;
            } elseif (!empty($magiclinks)) {
                $this->oldvalue = $magiclinks;
            }
            $this->value = $this->oldvalue;
            $this->oldvalue = '';
            return true;
        }

        // check if we uploaded some temporary file
        if (isset($this->value) && !empty($this->value)) {
            //$file = $this->initialization_basepath . '/' . $this->initialization_basedirectory . '/'. $this->value;
            $tmpfile = $this->value;
        }

        // restore the text value and append the content of the file if necessary
        $this->value = $this->oldvalue;
        if (!empty($tmpfile) && file_exists($tmpfile)) {
            $this->value .= file_get_contents($tmpfile);
            unlink($tmpfile);

            // Remove head and tail from uploaded HTML documents
            $filtername = $name . '_filter';
            if (!xarVarFetch($filtername, 'isset', $filtervalue, NULL, XARVAR_NOT_REQUIRED)) return;
            if (!empty($filtervalue)) {
                if (!empty($this->filterhead)) {
                    $this->value = preg_replace($this->filterhead,'',$this->value);
                }
                if (!empty($this->filtertail)) {
                    $this->value = preg_replace($this->filtertail,'',$this->value);
                }
            }
        }
        $this->oldvalue = '';

/*
        if (isset($this->fieldname)) {
            $name = $this->fieldname;
        } else {
            $name = 'dd_'.$this->id;
        }

        // retrieve new value for preview + new/modify combinations
        if (xarVarIsCached('DynamicData.TextUpload',$name)) {
            $this->value = xarVarGetCached('DynamicData.TextUpload',$name);
            return true;
        }

        // if the uploads module is hooked (to be verified and set by the calling module)
        // any uploaded files will be referenced in the text as #...:NN# for transform hooks
        if (xarVarGetCached('Hooks.uploads','ishooked')) {
            // set override for the upload/import paths if necessary
            if (!empty($this->basedir) || !empty($this->importdir)) {
                $override = array();
                if (!empty($this->basedir)) {
                    $override['upload'] = array('path' => $this->basedir);
                }
                if (!empty($this->importdir)) {
                    $override['import'] = array('path' => $this->importdir);
                }
            } else {
                $override = null;
            }
            $return = xarMod::apiFunc('uploads','admin','validatevalue',
                                    array('id' => $name, // not $this->id
                                          'value' => null, // we don't keep track of values here
                                          // pass the module id, item type and item id (if available) for associations
                                      // Note: for text upload, the file association is not maintained after editing
                                          'moduleid' => $this->_moduleid,
                                          'itemtype' => $this->_itemtype,
                                          'itemid'   => !empty($this->_itemid) ? $this->_itemid : null,
                                          'multiple' => FALSE, // not relevant here
                                          'methods' => $this->methods,
                                          'override' => $override,
                                          'format' => 'textupload',
                                          'maxsize' => $this->maxsize));
            // TODO: This raises exception now, we dont want it allways
            // TODO: insert try/catch clause here once we know what uploads raises for exceptions
            // TODO:
            if (!isset($return) || !is_array($return) || count($return) < 2) {
                $this->value = null;
                return false;
            }
            if (empty($return[0])) {
                $this->value = null;
                $this->invalid = xarML('value: #(1)', $this->name);
                return false;
            }
            // show magic link #...:NN# to file in text (cfr. transform hook in uploads module)
            $magiclinks = '';
            if (!empty($return[1])) {
                $magiclinks = xarMod::apiFunc('uploads','user','showoutput',
                                            array('value' => $return[1],
                                                  'format' => 'textupload',
                                                  'style' => 'icon'));
                // strip template comments if necessary
                $magiclinks = preg_replace('/<\!--.*?-->/','',$magiclinks);
                $magiclinks = trim($magiclinks);
            }
            if (!empty($value) && !empty($magiclinks)) {
                $value .= ' ' . $magiclinks;
            } elseif (!empty($magiclinks)) {
                $value = $magiclinks;
            }
            $this->value = $value;
            // save new value for preview + new/modify combinations
            xarVarSetCached('DynamicData.TextUpload',$name,$value);
            return true;
        }

        $upname = $name .'_upload';
        if (!empty($_FILES) && !empty($_FILES[$upname]) && !empty($_FILES[$upname]['tmp_name'])
            // is_uploaded_file() : PHP 4 >= 4.0.3
            && is_uploaded_file($_FILES[$upname]['tmp_name']) && $_FILES[$upname]['size'] > 0 && $_FILES[$upname]['size'] < 1000000) {

            // this doesn't work on some configurations
            //$this->value = join('', @file($_FILES[$upname]['tmp_name']));
            $tmpdir = sys::varpath();
            $tmpdir .= XARCORE_TPL_CACHEDIR;
            $tmpfile = tempnam($tmpdir, 'dd');
        // no verification of file types here
            if (move_uploaded_file($_FILES[$upname]['tmp_name'], $tmpfile) && file_exists($tmpfile)) {
                $this->value = join('', file($tmpfile));
                unlink($tmpfile);
            }
            // save new value for preview + new/modify combinations
            xarVarSetCached('DynamicData.TextUpload',$name,$this->value);
        // retrieve new value for preview + new/modify combinations
        } elseif (xarVarIsCached('DynamicData.TextUpload',$name)) {
            $this->value = xarVarGetCached('DynamicData.TextUpload',$name);
        } elseif (!empty($value)) {
            $this->value = $value;
        } else {
            $this->value = '';
        }
*/
        return true;
    }

//    function showInput($name = '', $value = null, $size = 0, $maxsize = 0, $id = '', $tabindex = '')
    public function showInput(Array $data = array())
    {
/*
        extract($data);

        $data = array();

        $name = empty($name) ? 'dd_'.$this->id : $name;
        if (empty($id)) {
            $id = $name;
        }
        if (!isset($value)) {
            $value = $this->value;
        }
        $upname = $name .'_upload';

        // inform anyone that we're showing a file upload field, and that they need to use
        // <form ... enctype="multipart/form-data" ... > in their input form
        xarVarSetCached('Hooks.dynamicdata','withupload',1);

        if (xarVarGetCached('Hooks.uploads','ishooked')) {
            // relevant input fields are handled directly by the uploads module
            //$extensions = xarModVars::get('uploads','allowed_types');
            $data['extensions']= '';
            $allowed = '';
            // set override for the upload/import paths if necessary
            if (!empty($this->basedir) || !empty($this->importdir)) {
                $override = array();
                if (!empty($this->basedir)) {
                    $override['upload'] = array('path' => $this->basedir);
                }
                if (!empty($this->importdir)) {
                    $override['import'] = array('path' => $this->importdir);
                }
            } else {
                $override = null;
            }
            $uploads = xarMod::apiFunc('uploads','admin','showinput',
                                     array('id' => $name, // not $this->id
                                           'value' => null, // we don't keep track of values here
                                           'multiple' => FALSE, // not relevant here
                                           'format' => 'textupload',
                                           'override' => $override,
                                           'methods' => $this->methods));
            if (!empty($uploads)) {
                $data['uploads_hooked'] = $uploads;
            }
        } else {
            // no verification of file types here
            $data['extensions']= '';
            $allowed = '';
        }
        $data['allowed']   =$allowed;
        $data['upname']    =$upname;
        // we're using the textarea field to keep track of any previously uploaded file here
        $data['name']     = $name;
        $data['id']       = $id;
        $data['upid']     = !empty($id) ? $id.'_upload' : '';
        $data['rows']     = !empty($rows) ? $rows : $this->display_rows;
        $data['cols']     = !empty($cols) ? $cols : $this->display_cols;
        $data['value']    = isset($value) ? xarVarPrepForDisplay($value) : xarVarPrepForDisplay($this->value);
        $data['maxsize']  = !empty($maxsize) ? $maxsize: $this->maxsize;
        $data['size']     = !empty($size) ? $size : $this->size;
*/
        $data['value'] = isset($data['value']) ? xarVarPrepForDisplay($data['value']) : xarVarPrepForDisplay($this->value);
        if(empty($data['rows'])) $data['rows'] = $this->display_rows;
        if(empty($data['cols'])) $data['cols'] = $this->display_columns;

        return parent::showInput($data);
    }

    public function showOutput(Array $data = array())
    {
/*

*/
        return parent::showOutput($data);
    }

/*
    public function parseConfiguration($validation = '')
    {
        // Determine if the uploads module is hooked to the calling module
        // if so, we will use the uploads modules functionality
        if (xarVarGetCached('Hooks.uploads','ishooked')) {
            list($multiple, $methods, $basedir, $importdir) = xarMod::apiFunc('uploads', 'admin', 'dd_configure', $validation);

            // $multiple is not relevant here
            $this->methods = $methods;
            $this->basedir = $basedir;
            $this->importdir = $importdir;
            $this->maxsize = xarModVars::get('uploads', 'file.maxsize');

        } else {
            // nothing interesting here
        }
    }

    public function showConfiguration(Array $args = array())
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
            $this->configuration = $validation;
            $this->parseConfiguration($validation);
        }

        if (xarVarGetCached('Hooks.uploads','ishooked')) {
            $data['ishooked'] = true;
        } else {
            $data['ishooked'] = false;
        }
        if ($data['ishooked']) {
            $data['methods'] = $this->methods;
            $data['basedir'] = $this->basedir;
            $data['importdir'] = $this->importdir;
        } else {
            // nothing interesting here
        }
        $data['other'] = '';

        // allow template override by child classes
        if (empty($template)) {
            $template = 'textupload';
        }
        return xarTplProperty('base', $template, 'configuration', $data);
    }

    public function updateConfiguration(Array $args = array())
    {
        extract($args);

        // in case we need to process additional input fields based on the name
        $name = empty($name) ? 'dd_'.$this->id : $name;
        // do something with the validation and save it in $this->configuration
        if (isset($validation)) {
            if (is_array($validation)) {
                if (!empty($validation['other'])) {
                    $this->configuration = $validation['other'];

                } elseif (xarVarGetCached('Hooks.uploads','ishooked')) {
                    $this->configuration = '';
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
                            $this->configuration .= ';methods(';
                            $this->configuration .= join(',',$todo);
                            $this->configuration .= ')';
                        }
                    }
                    if (!empty($validation['basedir'])) {
                        $this->configuration .= ';basedir(' . $validation['basedir'] . ')';
                    }
                    if (!empty($validation['importdir'])) {
                        $this->configuration .= ';importdir(' . $validation['importdir'] . ')';
                    }
                } else {
                    $this->configuration = '';
                    // nothing interesting here
                }
            } else {
                $this->configuration = $validation;
            }
        }

        // tell the calling function that everything is OK
        return true;
    }
*/
}

?>