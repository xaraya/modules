<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Marco Canini
// Purpose of file: Generate translations sets loadable thorugh the PHP
//                  translations backend
// ----------------------------------------------------------------------

class PHPTranslationsGenerator {

    var $locale;

    function PHPTranslationsGenerator($locale)
    {
        $this->locale = $locale;
        $l = xarLocaleGetInfo($locale);
        $this->outCharset = $l['charset'];
        $this->isUTF8 = ($l['charset'] == 'utf-8');
    }

    function bindDomain($dnType, $dnName)
    {
        $varDir = xarCoreGetVarDirPath();
        $locales_dir = "$varDir/locales";
        if (!file_exists($locales_dir) || !is_writeable($locales_dir)) {
            $msg = xarML("The directory #(1) must be writeable by PHP.", $locales_dir);
            xarExceptionSet(XAR_USER_EXCEPTION, 'WrongPermissions', new DefaultUserException($msg));
            return;
        }

        $locale_dir = "$locales_dir/{$this->locale}";
        $php_dir = "$locale_dir/php";
        $modules_dir = "$php_dir/modules";
        $themes_dir = "$php_dir/themes";
        $core_dir = "$php_dir/core";

        // FIXME: <marco> Remove 0777 later!
        if (!file_exists($locale_dir)) mkdir($locale_dir, 0777);
        if (!file_exists($php_dir)) mkdir($php_dir, 0777);
        if (!file_exists($modules_dir)) mkdir($modules_dir, 0777);
        if (!file_exists($themes_dir)) mkdir($themes_dir, 0777);
        if (!file_exists($core_dir)) mkdir($core_dir, 0777);

        switch ($dnType) {
            case XARMLS_DNTYPE_MODULE:
            $this->baseDir = "$modules_dir/$dnName/";
            if (!file_exists($this->baseDir)) mkdir($this->baseDir, 0777);
            if (!file_exists($this->baseDir.'blocks')) mkdir($this->baseDir.'blocks', 0777);
            if (!file_exists($this->baseDir.'templates')) mkdir($this->baseDir.'templates', 0777);
            if (!file_exists($this->baseDir.'templates/includes')) mkdir($this->baseDir.'templates/includes', 0777);
            if (!file_exists($this->baseDir.'templates/blocks')) mkdir($this->baseDir.'templates/blocks', 0777);            
            break;
            case XARMLS_DNTYPE_THEME:
            $this->baseDir = "$themes_dir/$dnName/";
            if (!file_exists($this->baseDir)) mkdir($this->baseDir, 0777);
            if (!file_exists($this->baseDir.'templates')) mkdir($this->baseDir.'templates', 0777);
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
        switch ($ctxType) {
            case XARMLS_CTXTYPE_FILE:
                $this->fileName .= $ctxName;
            break;
            case XARMLS_CTXTYPE_TEMPLATE:
                $this->fileName .= "templates/$ctxName";
            break;
            case XARMLS_CTXTYPE_INCLTEMPL:
                $this->fileName .= "templates/includes/$ctxName";
            break;
            case XARMLS_CTXTYPE_BLOCK:
                $this->fileName .= "blocks/$ctxName";
        }
        $this->fileName .= '.php';

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
        // FIXME: <marco> Remove 0666 later!
        chmod($this->fileName, 0666);
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