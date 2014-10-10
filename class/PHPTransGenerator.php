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

class PHPTranslationsGenerator 
{
    var $locale;
    var $fp;
    var $outCharset;
    var $isUTF8;
    var $fileName;
    var $baseDir;
    
    function PHPTranslationsGenerator($locale)
    {
        $this->locale = $locale;
        $l = xarLocaleGetInfo($locale);
        $this->outCharset = $l['charset'];
        $this->isUTF8 = ($l['charset'] == 'utf-8');
    }

    function bindDomain($dnType, $dnName='xaraya')
    {
        $varDir         = sys::varpath();
        $locales_dir    = "$varDir/locales";
        $locale_dir     = "$locales_dir/{$this->locale}";
        $php_dir        = "$locale_dir/php";
        $core_dir       = "$php_dir/core";
        $modules_dir    = "$php_dir/modules";
        $themes_dir     = "$php_dir/themes";
        $properties_dir = "$php_dir/properties";
        $blocks_dir     = "$php_dir/blocks";

        $canWrite = 1;
        if (file_exists($locales_dir)) {
            if (file_exists($locale_dir)) {
                if (file_exists($php_dir)) {
                    if (file_exists($modules_dir) && 
                        file_exists($properties_dir) && 
                        file_exists($blocks_dir) && 
                        file_exists($themes_dir) &&
                        file_exists($core_dir)) {
                        if (!is_writeable($modules_dir)) $canWrite = 0;
                        if (!is_writeable($properties_dir)) $canWrite = 0;
                        if (!is_writeable($blocks_dir)) $canWrite = 0;
                        if (!is_writeable($themes_dir)) $canWrite = 0;
                        if (!is_writeable($core_dir)) $canWrite = 0;
                    } else {
                        if (is_writeable($php_dir)) {
                            if (file_exists($modules_dir)) {
                                if (!is_writeable($modules_dir)) $canWrite = 0;
                            } else {
                                mkdir($modules_dir, 0777);
                            }
                            if (file_exists($properties_dir)) {
                                if (!is_writeable($properties_dir)) $canWrite = 0;
                            } else {
                                mkdir($properties_dir, 0777);
                            }
                            if (file_exists($blocks_dir)) {
                                if (!is_writeable($blocks_dir)) $canWrite = 0;
                            } else {
                                mkdir($blocks_dir, 0777);
                            }
                            if (file_exists($themes_dir)) {
                                if (!is_writeable($themes_dir)) $canWrite = 0;
                            } else {
                                mkdir($themes_dir, 0777);
                            }
                            if (file_exists($core_dir)) {
                                if (!is_writeable($core_dir)) $canWrite = 0;
                            } else {
                                mkdir($core_dir, 0777);
                            }
                        } else {
                            $canWrite = 0; // var/locales/LOCALE/php is unwriteable
                        }
                    }
                } else {
                    if (is_writeable($locale_dir)) {
                        mkdir($php_dir, 0777);
                        mkdir($modules_dir, 0777);
                        mkdir($properties_dir, 0777);
                        mkdir($blocks_dir, 0777);
                        mkdir($themes_dir, 0777);
                        mkdir($core_dir, 0777);
                    } else {
                        $canWrite = 0; // var/locales/LOCALE is unwriteable
                    }
                }
            } else {
                if (is_writeable($locales_dir)) {
                    mkdir($locale_dir, 0777);
                    mkdir($php_dir, 0777);
                    mkdir($modules_dir, 0777);
                    mkdir($properties_dir, 0777);
                    mkdir($blocks_dir, 0777);
                    mkdir($themes_dir, 0777);
                    mkdir($core_dir, 0777);
                } else {
                    $canWrite = 0; // var/locales is unwriteable
                }
            }
        } else {
            $canWrite = 0; // var/locales missed
        }

        if (!$canWrite) {
            $msg = xarML("The directories under #(1) must be writeable by PHP.", $locales_dir);
            throw new Exception($msg);
        }

        switch ($dnType) {
            case XARMLS_DNTYPE_MODULE:
            $this->baseDir = sys::code() . "$modules_dir/$dnName/";
            if (!file_exists($this->baseDir)) mkdir($this->baseDir, 0777);

            $dirnames = xarMod::apiFunc('translations','admin','get_module_dirs',array('moddir'=>$dnName));
            foreach ($dirnames as $dirname) {
                if (file_exists($this->baseDir.$dirname)) continue;
                if (!file_exists(sys::code() . "modules/$dnName/xar$dirname")) continue;
                mkdir($this->baseDir.$dirname, 0777);
            }
            break;
            case XARMLS_DNTYPE_PROPERTY:
            $this->baseDir = "$properties_dir/$dnName/";
            if (!file_exists($this->baseDir)) mkdir($this->baseDir, 0777);
            //if (!file_exists($this->baseDir.'templates')) mkdir($this->baseDir.'templates', 0777);
            $dirnames = xarMod::apiFunc('translations','admin','get_property_dirs',array('propertydir'=>$dnName));
            foreach ($dirnames as $dirname) {
                if (file_exists($this->baseDir.$dirname)) continue;
                if (!file_exists("properties/$dnName/$dirname")) continue;
                mkdir($this->baseDir.$dirname, 0777);
            }
            break;
            case XARMLS_DNTYPE_BLOCK:
            $this->baseDir = "$blocks_dir/$dnName/";
            if (!file_exists($this->baseDir)) mkdir($this->baseDir, 0777);
            //if (!file_exists($this->baseDir.'templates')) mkdir($this->baseDir.'templates', 0777);
            $dirnames = xarMod::apiFunc('translations','admin','get_block_dirs',array('blockdir'=>$dnName));
            foreach ($dirnames as $dirname) {
                if (file_exists($this->baseDir.$dirname)) continue;
                if (!file_exists("blocks/$dnName/$dirname")) continue;
                mkdir($this->baseDir.$dirname, 0777);
            }
            break;
            case XARMLS_DNTYPE_THEME:
            $this->baseDir = "$themes_dir/$dnName/";
            if (!file_exists($this->baseDir)) mkdir($this->baseDir, 0777);
            //if (!file_exists($this->baseDir.'templates')) mkdir($this->baseDir.'templates', 0777);
            $dirnames = xarMod::apiFunc('translations','admin','get_theme_dirs',array('themedir'=>$dnName));
            foreach ($dirnames as $dirname) {
                if (file_exists($this->baseDir.$dirname)) continue;
                if (!file_exists("themes/$dnName/$dirname")) continue;
                mkdir($this->baseDir.$dirname, 0777);
            }
            break;
            case XARMLS_DNTYPE_CORE:
            $this->baseDir = $core_dir.'/';
        }

        return true;
    }

    function create($ctxType, $ctxName)
    {
        assert('!empty($this->baseDir)');
        $this->fileName = $this->baseDir;

        if (!mb_ereg("^[a-z]+:$", $ctxType)) {
           list($prefix,$directory) = explode(':',$ctxType);
           if ($directory != "") $this->fileName .= $directory . "/";
        }

        $this->fileName .= $ctxName . ".php";

        $this->fp = fopen($this->fileName.'.swp', 'w');

        fwrite($this->fp, "<?php\nglobal \$xarML_PHPBackend_entries;\nglobal \$xarML_PHPBackend_keyEntries;\n");
        return true;
    }

    function close()
    {
        fwrite($this->fp, "?>");
        fclose($this->fp);
        // I don't know why I can't rename this file under Windows :(
        // rename($this->fileName.'.swp', $this->fileName);
        copy($this->fileName.'.swp', $this->fileName);
        unlink($this->fileName.'.swp');
        return true;
    }

    function addEntry($string, $translation)
    {
        // NOTE: $string is not converted since its charset is US-ASCII which is a subset of utf-8
        if (!$this->isUTF8) {
            if ($this->outCharset == 'windows-1251') {
                $translation = iconv('utf-8', $this->outCharset, $translation);
            } else {
                $translation = mb_convert_encoding($translation, $this->outCharset, 'utf-8');
            }
        }
        $string = str_replace(array("\\","'"), array("\\\\","\\'"), $string);
        $translation = str_replace(array("\\","'"), array("\\\\","\\'"), $translation);
        fwrite($this->fp, "\$xarML_PHPBackend_entries['".$string."'] = '".$translation."';\n");
    }

    function addKeyEntry($key, $translation)
    {
        // NOTE: $key is not converted since its charset is US-ASCII which is a subset of utf-8
        if (!$this->isUTF8) {
            if ($this->outCharset == 'windows-1251') {
                $translation = iconv('utf-8', $this->outCharset, $translation);
            } else {
                $translation = mb_convert_encoding($translation, $this->outCharset, 'utf-8');
            }
        }
        $translation = str_replace(array("\\","'"), array("\\\\","\\'"), $translation);
        $quote_translation = (strpos($translation, "\\") === false) ? "'" : "\"";
        fwrite($this->fp, "\$xarML_PHPBackend_keyEntries['".$key."'] = '".$translation."';\n");
    }

}

?>