<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Marco Canini
// Purpose of file:
// ----------------------------------------------------------------------

class XMLTranslationsSkelsGenerator {

    var $locale;

    function XMLTranslationsSkelsGenerator($locale)
    {
        $this->locale = $locale;
    }

    function bindDomain($dnType, $dnName='xaraya')
    {
        $varDir = xarCoreGetVarDirPath();

        $locales_dir = "$varDir/locales";
        if (!file_exists($locales_dir) || !is_writeable($locales_dir)) {
            $msg = xarML("The directory #(1) must be writeable by PHP.", $locales_dir);
            xarExceptionSet(XAR_USER_EXCEPTION, 'WrongPermissions', new DefaultUserException($msg));
            return;
        }

        $locale_dir = "$locales_dir/{$this->locale}";
        $xml_dir = "$locale_dir/xml";
        $modules_dir = "$xml_dir/modules";
        $themes_dir = "$xml_dir/themes";
        $core_dir = "$xml_dir/core";

        // FIXME: <marco> Remove 0777 later!
        if (!file_exists($locale_dir)) mkdir($locale_dir, 0777);
        if (!file_exists($xml_dir)) mkdir($xml_dir, 0777);
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
            if (!file_exists($this->baseDir.'admin')) mkdir($this->baseDir.'admin', 0777);
            if (!file_exists($this->baseDir.'adminapi')) mkdir($this->baseDir.'adminapi', 0777);
            if (!file_exists($this->baseDir.'user')) mkdir($this->baseDir.'user', 0777);
            if (!file_exists($this->baseDir.'userapi')) mkdir($this->baseDir.'userapi', 0777);
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
        $context = $GLOBALS['MLS']->getContextByType($ctxType);
        if ($context->getDir() != "") $fileName .= $context->getDir() . "/";
        $this->fileName .= $ctxName . ".xml";

        $this->fp = fopen($this->fileName.'.swp', 'w');

        // XML files are always encoded in utf-8
        // The utf-8 encoding is guarateed by the fact that translations module
        // has to run in UNBOXED MULTI language mode
        fwrite($this->fp, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
        // FXIME: <marco> Add schema reference
        fwrite($this->fp, "<translations xmlns=\"http://xaraya.com/2002/ns/translations\" locale=\"{$this->locale}\">\n");
        return true;
    }

    function close()
    {
        fwrite($this->fp, "</translations>\n");
        fclose($this->fp);
        // I don't know why I can't rename this file under Windows :(
        // rename($this->fileName.'.swp', $this->fileName);
        copy($this->fileName.'.swp', $this->fileName);
        unlink($this->fileName.'.swp');
        // FIXME: <marco> Remove 0666 later!
        //chmod($this->fileName, 0666);
        return true;
    }

    function addEntry($string, $references, $translation = '')
    {
        // string and translation are already encoded in utf-8
        /*$string = utf8_encode(htmlspecialchars($string));
        $translation = utf8_encode($translation);*/
    //Allow html tags
    $translation = htmlspecialchars($translation);
        $string = htmlspecialchars($string);
        fwrite($this->fp, "\t<entry>\n");
        fwrite($this->fp, "\t\t<string>".$string."</string>\n");
        fwrite($this->fp, "\t\t<translation>".$translation."</translation>\n");
        fwrite($this->fp, "\t\t<references>\n");
        foreach($references as $reference) {
            fwrite($this->fp, "\t\t\t<reference file=\"$reference[file]\" line=\"$reference[line]\" />\n");
        }
        fwrite($this->fp, "\t\t</references>\n");
        fwrite($this->fp, "\t</entry>\n");
    }

    function addKeyEntry($key, $references, $translation = '')
    {
        // translation is already encoded in utf-8
        //$translation = utf8_encode($translation);

        fwrite($this->fp, "\t<keyEntry>\n");
        fwrite($this->fp, "\t\t<key>".$key."</key>\n");
/*
        The xpath query can also be elaborated at run time
        if ($this->ctx['locale'] != $this->ctx['modlocale']) {
            list($ostype, $oslocale) = pnVarPrepForOS($this->ctx['type'], $this->ctx['module_locale']);
            $file = 'modules/'.$this->ctx['moddir']."/pnlang/xml/$oslocale/pn$ostype.xml";
            $xpath ="/translations/keyEntry[./key/text()='$key']/translation";

            fwrite($this->fp, "\t\t<original file=\"$file\" xpath=\"$xpath\" />\n");
        }
*/
        fwrite($this->fp, "\t\t<translation>".$translation."</translation>\n");
        fwrite($this->fp, "\t\t<references>\n");
        foreach($references as $reference) {
            fwrite($this->fp, "\t\t\t<reference file=\"$reference[file]\" line=\"$reference[line]\" />\n");
        }
        fwrite($this->fp, "\t\t</references>\n");
        fwrite($this->fp, "\t</keyEntry>\n");
    }

}

?>