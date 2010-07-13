<?php
/**
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 */
/* Include parent class */
sys::import('modules.dynamicdata.class.properties.base');
/**
 * Class to handle file upload properties
 */
class UploadProperty extends DataProperty
{
    public $id         = 19742;
    public $name       = 'upload';
    public $desc       = 'Upload';
    public $reqmodules = array('downloads');

    public $display_size                    = 40;
    public $validation_max_file_size          = 1000000;
	public $validation_max_file_size_invalid;
	public $validation_min_length          = null;
	public $validation_min_length_invalid;
	public $validation_max_length          = null;
	public $validation_max_length_invalid;
    public $initialization_basedirectory    = '../uploads';
    public $initialization_importdirectory  = null;
    public $validation_file_extensions      = 'gif, jpg, jpeg, png, bmp, pdf, doc, txt';
    public $initialization_basepath         = null;

    public $validation_allow_duplicates     = 2; // Overwrite the old instance
    //public $obfuscate_filename              = false;
    // Note: if you use this, make sure you unlink($this->value) yourself once you're done with it
    public $use_temporary_file              = false;

    // this is used by DataPropertyMaster::addProperty() to set the $object->upload flag
    public $upload = true;

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->tplmodule = 'downloads';
        $this->template  = 'upload';
        $this->filepath   = 'modules/downloads/xarproperties';

        if(xarServer::getVar('PATH_TRANSLATED')) {
            $base_directory = dirname(realpath(xarServer::getVar('PATH_TRANSLATED')));
        } elseif(xarServer::getVar('SCRIPT_FILENAME')) {
            $base_directory = dirname(realpath(xarServer::getVar('SCRIPT_FILENAME')));
        } else {
            $base_directory = './';
        }

        $this->initialization_basepath = $base_directory;

        if (empty($this->initialization_basedirectory)) {
            $this->initialization_basedirectory = '../uploads';
        }

        if (empty($this->validation_file_extensions)) {	
			$this->validation_file_extensions = '';
		} 

		if (empty($this->validation_min_length)) $this->validation_min_length = '';

    }

    function checkInput($name='', $value = null)
    {
        if (empty($name)) $name = 'dd_' . $this->id;

        // Store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;
        if (!isset($value)) {
            xarVarFetch($name, 'isset', $value,  NULL, XARVAR_DONT_SET);
        }
        return $this->validateValue($value);
    }

    public function validateValue($value = null)
    {
        if (!parent::validateValue($value)) return false;

        if (isset($this->fieldname)) $name = $this->fieldname;
        else $name = 'dd_'.$this->id;

        // retrieve new value for preview + new/modify combinations
        if (xarVarIsCached('DynamicData.FileUpload',$name)) {
            $this->value = xarVarGetCached('DynamicData.FileUpload',$name);
            return true;
        }

        if (isset($_FILES[$name])) {
            $file =& $_FILES[$name];
        } else {
            $file = array();
        }

		$minlen = $this->validation_min_length;

		if (isset($minlen) && strlen($value) < $minlen && strlen($file['name']) < $minlen) { 
			if (!empty($this->validation_min_length_invalid)) {
				$this->invalid = xarML($this->validation_min_length_invalid);
			} else {
				$this->invalid = xarML('#(1) #(3): must be at least #(2) characters long', $this->name,$this->validation_min_length, $this->desc);
			}
			$this->value = null;
			return false;
		}

		$maxlen = $this->validation_max_length;

		if (isset($maxlen) && strlen($value) > $maxlen) { 
			if (!empty($validation_max_length_invalid)) {
				$this->invalid = xarML($validation_max_length_invalid);
			} else {
				$this->invalid = xarML('#(1) #(3): must be no more than #(2) characters long', $this->name,$maxlen, $this->desc);
			}
			$this->value = null;
			return false;
		}

		$maxsize = $this->validation_max_file_size;
		$maxmb = $maxsize/1000000;

		if (isset($file['error']) && $file['error'] == 2) { 
			if (!empty($validation_max_file_size_invalid)) {
				$this->invalid = xarML($validation_max_length_invalid);
			} else {
				$this->invalid = xarML('File must be no more than #(2) MB in size', $this->name,$maxmb, $this->desc);
				$this->value = null;
				return false;
			}
		}

		if (isset($file['error']) && $file['error'] == 1) { 
			$this->invalid = xarML('The uploaded file exceeds the maximum size allowed in the php.ini or htaccess file.');
			$this->value = null;
			return false;		
		}

        if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name']) && $file['size'] > 0) {
            if (!empty($_FILES[$name]['name'])) {
                if (!$this->validateExtension($file['name'])) {
                    $this->invalid = xarML('The file type is not allowed');
                    $this->value = null;
                    return false;
                }
                $filename = $file['name'];
            } 

            if ($this->use_temporary_file) {
                $filepath = tempnam(realpath($this->initialization_basepath . '/' . $this->initialization_basedirectory), 'tempdd');

            //} elseif ($this->obfuscate_filename) {
            // TODO: obfuscate filename + return hash & original filename + handle that combined value in the other methods
            //    // cfr. file_obfuscate_name() function in uploads module
            //    $filehash = crypt($filename, substr(md5(time() . $filename . getmypid()), 0, 2));
            //    $filehash = substr(md5($filehash), 0, 8) . time() . getmypid();
            //    $fileparts = explode('.', $filename);
            //    if (count($fileparts) > 1) {
            //        $filehash .= '.' . array_pop($fileparts);
            //    }
            //    $filepath = $this->initialization_basepath . '/' . $this->initialization_basedirectory . '/'. $filehash;

            } else {
                $filename = $file['name'];
                $filepath = $this->initialization_basepath . '/' . $this->initialization_basedirectory . '/'. $filename;
                if ($this->validation_allow_duplicates == 2) {
                    // overwrite existing file if necessary
                } elseif ($this->validation_allow_duplicates == 1 && file_exists($filepath)) {
                    // create new instance of the file
                    $fileparts = explode('.', $filename);
                    if (count($fileparts) > 1) {
                        $fileext = '.' . array_pop($fileparts);
                        $filebase = implode('.', $fileparts);
                    } else {
                        $fileext = '';
                        $filebase = $filename;
                    }
                    $i = 1;
                    $filename = $filebase . '_' . $i . $fileext;
                    $filepath = $this->initialization_basepath . '/' . $this->initialization_basedirectory . '/'. $filename;
                    while (file_exists($filepath)) {
                        $i++;
                        $filename = $filebase . '_' . $i . $fileext;
                        $filepath = $this->initialization_basepath . '/' . $this->initialization_basedirectory . '/'. $filename;
                    }
                } elseif ($this->validation_allow_duplicates == 0 && file_exists($filepath)) { 
                    // duplicate files are not allowed
                    $this->invalid = xarML('This file already exists');
                    $this->value = null;
                    return false;
                }
            }

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                $this->invalid = xarML('The file upload failed');
                $this->value = null;
                return false;
            }

            if ($this->use_temporary_file) {
                // We pass the whole path to the temporary file here, since we're not 100% sure where it will be created
                // Note: if you use this, make sure you unlink($this->value) yourself once you're done with it
                $this->value = $filepath;
                // save new value for preview + new/modify combinations
                xarVarSetCached('DynamicData.FileUpload',$name,$this->value);

            //} elseif ($this->obfuscate_filename) {
            // TODO: obfuscate filename + return hash & original filename + handle that combined value in the other methods
            //    $this->value = $filehash . ',' . $filename;
            //    // save new value for preview + new/modify combinations
            //    xarVarSetCached('DynamicData.FileUpload',$name,$this->value);

            } else {
                $this->value = $filename;
                // save new value for preview + new/modify combinations
                xarVarSetCached('DynamicData.FileUpload',$name,$this->value);
            }

        // retrieve new value for preview + new/modify combinations
        } elseif (xarVarIsCached('DynamicData.FileUpload',$name)) {
            $this->value = xarVarGetCached('DynamicData.FileUpload',$name);
        } elseif (!empty($value) &&  !(is_numeric($value) || stristr($value, ';'))) {
            if (!$this->validateExtension($value)) {
                $this->invalid = xarML('The file type is not allowed');
                $this->value = null;
                return false;
            } elseif (!file_exists($this->initialization_basedirectory . '/'. $value) || !is_file($this->initialization_basedirectory . '/'. $value)) {
                $this->invalid = xarML('The file cannot be found');
                $this->value = null;
                return false;
            }
            $this->value = $value;
        } else {
            // No file name entered, ignore
            $this->value = '';
            return true;
        }
		
        return true;
    }

    public function showInput(Array $data = array())
    {
        $data['name'] = empty($data['name']) ? 'dd_'.$this->id : $data['name'];
        $data['upname'] = $data['name'] .'_upload';
        
        // Allow overriding by specific parameters 
		if (isset($data['size']))   $this->display_size = $data['size'];
		if (isset($data['maxsize']))   $this->validation_max_file_size = $data['maxsize'];

        return parent::showInput($data);
    }

    public function showOutput(Array $data = array())
    {
        extract($data);

        if (!isset($value)) $value = $this->value;

        if (!empty($value)) {  
            return parent::showOutput($data);
        } else {
            return '';
        }
    }

    public function getExtensions()
    {	
		$extensions = $this->validation_file_extensions;
		$extensions = str_replace(' ', '', $extensions);
		$pattern = '/^[A-Za-z,]+$/';
		if (!preg_match($pattern, $extensions)) {
			throw new Exception('Invalid extension list format in upload.php');
		}
		$extensions = explode(',',$extensions);
        return $extensions;
    }

    /**
     * Validate the given filename against the list of allowed file extensions
     */
    public function validateExtension($filename = '')
    {
		$filename = strtolower($filename);

        if (strstr($filename,'.')) {
            $extension = substr(strrchr($filename,'.'),1);
        } else {
            return false;
        }

		$extensions = $this->getExtensions();

        if (!empty($extensions) &&
            !in_array($extension, $extensions)) {
            return false;
        }
        return true;
    }

}

?>