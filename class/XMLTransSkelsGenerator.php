<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 * @author Marco Canini
 */

/**
 * Translations Module
 *
 */
class XMLTranslationsSkelsGenerator
{
    public $locale;
    public $fp;
    public $fileName;
    public $baseDir;

    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    public function bindDomain($dnType, $dnName='xaraya')
    {
        $varDir = sys::varpath();
        $locales_dir    = "$varDir/locales";
        $locale_dir     = "$locales_dir/{$this->locale}";
        $xml_dir        = "$locale_dir/xml";
        $core_dir       = "$xml_dir/core";
        $modules_dir    = "$xml_dir/modules";
        $themes_dir     = "$xml_dir/themes";
        $properties_dir = "$xml_dir/properties";
        $blocks_dir     = "$xml_dir/blocks";
        $objects_dir    = "$xml_dir/objects";

        // General check that all the diretories at the top level we need to translate a site are presnet and writable
        $canWrite = 1;
        if (file_exists($locales_dir)) {
            if (file_exists($locale_dir)) {
                if (file_exists($xml_dir)) {
                    if (file_exists($modules_dir) && file_exists($properties_dir) && file_exists($blocks_dir) && file_exists($themes_dir) && file_exists($objects_dir) &&
                        file_exists($core_dir)) {
                        if (!is_writeable($modules_dir)) {
                            $canWrite = 0;
                        }
                        if (!is_writeable($properties_dir)) {
                            $canWrite = 0;
                        }
                        if (!is_writeable($blocks_dir)) {
                            $canWrite = 0;
                        }
                        if (!is_writeable($themes_dir)) {
                            $canWrite = 0;
                        }
                        if (!is_writeable($objects_dir)) {
                            $canWrite = 0;
                        }
                        if (!is_writeable($core_dir)) {
                            $canWrite = 0;
                        }
                    } else {
                        if (is_writeable($xml_dir)) {
                            if (file_exists($modules_dir)) {
                                if (!is_writeable($modules_dir)) {
                                    $canWrite = 0;
                                }
                            } else {
                                mkdir($modules_dir, 0o777);
                            }
                            if (file_exists($properties_dir)) {
                                if (!is_writeable($properties_dir)) {
                                    $canWrite = 0;
                                }
                            } else {
                                mkdir($properties_dir, 0o777);
                            }
                            if (file_exists($blocks_dir)) {
                                if (!is_writeable($blocks_dir)) {
                                    $canWrite = 0;
                                }
                            } else {
                                mkdir($blocks_dir, 0o777);
                            }
                            if (file_exists($themes_dir)) {
                                if (!is_writeable($themes_dir)) {
                                    $canWrite = 0;
                                }
                            } else {
                                mkdir($themes_dir, 0o777);
                            }
                            if (file_exists($objects_dir)) {
                                if (!is_writeable($objects_dir)) {
                                    $canWrite = 0;
                                }
                            } else {
                                mkdir($objects_dir, 0o777);
                            }
                            if (file_exists($core_dir)) {
                                if (!is_writeable($core_dir)) {
                                    $canWrite = 0;
                                }
                            } else {
                                mkdir($core_dir, 0o777);
                            }
                        } else {
                            $canWrite = 0; // var/locales/LOCALE/xml is unwriteable
                        }
                    }
                } else {
                    if (is_writeable($locale_dir)) {
                        mkdir($xml_dir, 0o777);
                        mkdir($modules_dir, 0o777);
                        mkdir($properties_dir, 0o777);
                        mkdir($blocks_dir, 0o777);
                        mkdir($themes_dir, 0o777);
                        mkdir($objects_dir, 0o777);
                        mkdir($core_dir, 0o777);
                    } else {
                        $canWrite = 0; // var/locales/LOCALE is unwriteable
                    }
                }
            } else {
                if (is_writeable($locales_dir)) {
                    mkdir($locale_dir, 0o777);
                    mkdir($xml_dir, 0o777);
                    mkdir($modules_dir, 0o777);
                    mkdir($properties_dir, 0o777);
                    mkdir($blocks_dir, 0o777);
                    mkdir($themes_dir, 0o777);
                    mkdir($objects_dir, 0o777);
                    mkdir($core_dir, 0o777);
                } else {
                    $canWrite = 0; // var/locales is unwriteable
                }
            }
        } else {
            $canWrite = 0; // var/locales missed
        }

        // Bail if one or more of the directories cannot be written to
        if (!$canWrite) {
            $msg = xarML("The directories under #(1) must be writeable by PHP.", $locales_dir);
            throw new Exception($msg);
        }

        // Check that all the directories for this locale below the top level exist
        // In general these are 1 level for the DNTYPE and below that a second level of one or more directories
        // If not create them
        switch ($dnType) {
            case xarMLS::DNTYPE_MODULE:
                $this->baseDir = "$modules_dir/$dnName/";
                if (!file_exists($this->baseDir)) {
                    mkdir($this->baseDir, 0o777);
                }

                $dirnames = xarMod::apiFunc('translations', 'admin', 'get_module_dirs', ['moddir'=>$dnName]);
                foreach ($dirnames as $dirname) {
                    if (file_exists($this->baseDir.$dirname)) {
                        continue;
                    }
                    if (!file_exists(sys::code() . "modules/$dnName/xar$dirname")) {
                        continue;
                    }
                    mkdir($this->baseDir.$dirname, 0o777);
                }
                break;
            case xarMLS::DNTYPE_PROPERTY:
                $this->baseDir = "$properties_dir/$dnName/";
                if (!file_exists($this->baseDir)) {
                    mkdir($this->baseDir, 0o777);
                }
                //if (!file_exists($this->baseDir.'templates')) mkdir($this->baseDir.'templates', 0777);
                $dirnames = xarMod::apiFunc('translations', 'admin', 'get_property_dirs', ['propertydir'=>$dnName]);
                foreach ($dirnames as $dirname) {
                    if (file_exists($this->baseDir.$dirname)) {
                        continue;
                    }
                    if (!file_exists("properties/$dnName/$dirname")) {
                        continue;
                    }
                    mkdir($this->baseDir.$dirname, 0o777);
                }
                break;
            case xarMLS::DNTYPE_BLOCK:
                $this->baseDir = "$blocks_dir/$dnName/";
                if (!file_exists($this->baseDir)) {
                    mkdir($this->baseDir, 0o777);
                }
                //if (!file_exists($this->baseDir.'templates')) mkdir($this->baseDir.'templates', 0777);
                $dirnames = xarMod::apiFunc('translations', 'admin', 'get_block_dirs', ['blockdir'=>$dnName]);
                foreach ($dirnames as $dirname) {
                    if (file_exists($this->baseDir.$dirname)) {
                        continue;
                    }
                    if (!file_exists("blocks/$dnName/$dirname")) {
                        continue;
                    }
                    mkdir($this->baseDir.$dirname, 0o777);
                }
                break;
            case xarMLS::DNTYPE_THEME:
                $this->baseDir = "$themes_dir/$dnName/";
                if (!file_exists($this->baseDir)) {
                    mkdir($this->baseDir, 0o777);
                }
                //if (!file_exists($this->baseDir.'templates')) mkdir($this->baseDir.'templates', 0777);
                $dirnames = xarMod::apiFunc('translations', 'admin', 'get_theme_dirs', ['themedir'=>$dnName]);
                foreach ($dirnames as $dirname) {
                    if (file_exists($this->baseDir.$dirname)) {
                        continue;
                    }
                    if (!file_exists("themes/$dnName/$dirname")) {
                        continue;
                    }
                    mkdir($this->baseDir.$dirname, 0o777);
                }
                break;
            case xarMLS::DNTYPE_OBJECT:
                $this->baseDir = "$objects_dir/";
                break;
            case xarMLS::DNTYPE_CORE:
                $this->baseDir = $core_dir.'/';
                break;
        }

        return true;
    }

    public function create($contextType, $contextName)
    {
        assert('!empty($this->baseDir)');
        $this->fileName = $this->baseDir;

        // Complete the directory path if the context directory is not empty
        if (!mb_ereg("^[a-z]+:$", $contextType)) {
            $contextParts = xarMLSContext::getContextTypeComponents($contextType);
            if (!empty($contextParts[1])) {
                $this->fileName .= $contextParts[1] . "/";
            }
        }
        $this->fileName .= $contextName . ".xml";

        $this->fp = $this->fopen_recursive($this->fileName.'.swp', 'w');

        // XML files are always encoded in utf-8
        // The utf-8 encoding is guarateed by the fact that translations module
        // has to run in UNBOXED MULTI language mode
        fwrite($this->fp, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
        // FXIME: <marco> Add schema reference
        fwrite($this->fp, "<translations xmlns=\"http://xaraya.com/2002/ns/translations\" locale=\"{$this->locale}\">\n");
        return true;
    }

    public function fopen_recursive($path, $mode, $chmod=0o755)
    {
        $directory = dirname($path);
        $file = basename($path);
        if (!is_dir($directory)) {
            if (!mkdir($directory, $chmod, 1)) {
                return false;
            }
        }
        return fopen($path, $mode);
    }

    public function open($contextType, $contextName)
    {
        assert('!empty($this->baseDir)');
        $this->fileName = $this->baseDir;

        // Complete the directory path if the context directory is not empty
        if (!mb_ereg("^[a-z]+:$", $contextType)) {
            $contextParts = xarMLSContext::getContextTypeComponents($contextType);
            if (!empty($contextParts[1])) {
                $this->fileName .= $contextParts[1] . "/";
            }
        }
        $this->fileName .= $contextName . ".xml";

        if (file_exists($this->fileName)) {
            $lines = file($this->fileName);
            $this->fp = fopen($this->fileName.'.swp', 'w');
            foreach ($lines as $line_num => $line) {
                if (!strncmp($line, '</translations>', 15)) {
                    continue;
                }
                fwrite($this->fp, $line);
            }
        } else {
            $this->fp = fopen($this->fileName.'.swp', 'w');
            // Translations XML files are always encoded in utf-8
            fwrite($this->fp, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
            // FXIME: <marco> Add schema reference
            fwrite($this->fp, "<translations xmlns=\"http://xaraya.com/2002/ns/translations\" locale=\"{$this->locale}\">\n");
        }

        return true;
    }

    public function close()
    {
        fwrite($this->fp, "</translations>\n");
        fclose($this->fp);
        // I don't know why I can't rename this file under Windows :(
        // rename($this->fileName.'.swp', $this->fileName);
        copy($this->fileName.'.swp', $this->fileName);
        unlink($this->fileName.'.swp');
        return true;
    }

    public function deleteIfExists($contextType, $contextName)
    {
        assert('!empty($this->baseDir)');
        $this->fileName = $this->baseDir;

        // Complete the directory path if the context directory is not empty
        if (!mb_ereg("^[a-z]+:$", $contextType)) {
            $contextParts = xarMLSContext::getContextTypeComponents($contextType);
            if (!empty($contextParts[1])) {
                $this->fileName .= $contextParts[1] . "/";
            }
        }
        $this->fileName .= $contextName . ".xml";
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
        return true;
    }

    public function addEntry($string, $references, $translation = '')
    {
        // Replace any special characters with entities
        $string = xarVar::prepForDisplay($string);
        $translation = xarVar::prepForDisplay($translation);

        fwrite($this->fp, "<entry>");
        fwrite($this->fp, "\n\t<string>".$string."</string>");
        fwrite($this->fp, "\n\t<translation>".$translation."</translation>");
        if (xarModVars::get('translations', 'maxreferences')) {
            fwrite($this->fp, "\n\t<references>");
            foreach ($references as $reference) {
                fwrite($this->fp, "\n\t\t<reference file=\"$reference[file]\" line=\"$reference[line]\"/>\n");
            }
            fwrite($this->fp, "\t</references>\n");
        }
        fwrite($this->fp, "</entry>\n");
    }

    public function addKeyEntry($key, $references, $translation = '')
    {
        // Replace any special characters with entities
        $string = xarVar::prepForDisplay($string);
        $key = xarVar::prepForDisplay($key);

        fwrite($this->fp, "<keyEntry>");
        fwrite($this->fp, "\n\t<<key>".$key."</key>");
        fwrite($this->fp, "\n\t<<translation>".$translation."</translation>");
        if (xarModVars::get('translations', 'maxreferences')) {
            fwrite($this->fp, "\n\t<references>\n");
            foreach ($references as $reference) {
                fwrite($this->fp, "\n\t\t<reference file=\"$reference[file]\" line=\"$reference[line]\"/>\n");
            }
            fwrite($this->fp, "\t</references>\n");
        }
        fwrite($this->fp, "</keyEntry>\n");
    }
}
