<?php

/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage module name
 * @link  link to where more info can be found
 * @author author name <author@email> (this tag means responsible person)
*/


function translations_admin_translate_update()
{
// Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('subtype', 'regexp:/^(file|template|incltempl|blktempl|block)$/', $subtype)) return;
    if (!xarVarFetch('subname', 'str:1:', $subname)) return;
    if (!xarVarFetch('numEntries', 'int:0:', $numEntries)) return;
    if (!xarVarFetch('numKeyEntries', 'int:0:', $numKeyEntries)) return;

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    if ($subtype == 'file') $ctxType = XARMLS_CTXTYPE_FILE;
    elseif ($subtype == 'template') $ctxType = XARMLS_CTXTYPE_TEMPLATE;
    elseif ($subtype == 'incltempl') $ctxType = XARMLS_CTXTYPE_INCLTEMPL;
    elseif ($subtype == 'blktempl') $ctxType = XARMLS_CTXTYPE_BLKTEMPL;
    else $ctxType = XARMLS_CTXTYPE_BLOCK;
    $ctxName = $subname;

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
    if (!isset($backend)) return;
    if (!$backend->bindDomain($dnType, $dnName)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }
    if (!$backend->loadContext($ctxType, $ctxName)) return;

    $args['interface'] = 'ReferencesGenerator';
    $args['locale'] = $locale;
    $gen = xarModAPIFunc('translations','admin','create_generator_instance',$args);
    if (!isset($gen)) return;
    if (!$gen->bindDomain($dnType, $dnName)) return;
    if (!$gen->create($ctxType, $ctxName)) return;

    for ($i = 0; $i < $numEntries; $i++) {
        unset($translation);
        if (!xarVarFetch('tid'.$i, 'str::', $translation, '', XARVAR_POST_ONLY)) return;
        // Lookup the string bounded to the tid$i transient id
        $e = $backend->lookupTransientId($i);
        if ($e) {
            $gen->addEntry($e['string'], $e['references'], $translation);
        }
    }
    while (list($key, $translation) = $backend->enumKeyTranslations()) {
        unset($translation);
        if (!xarVarFetch('key'.$key, 'str::', $translation, '', XARVAR_POST_ONLY)) return;
        $e = $backend->getEntryByKey($key);
        if ($e) {
            $gen->addKeyEntry($key, $e['references'], $translation);
        }
    }

    $gen->close();

    xarResponseRedirect(xarModURL('translations', 'admin', 'translate_subtype', array('subtype'=>$subtype, 'subname'=>$subname)));
}
?>