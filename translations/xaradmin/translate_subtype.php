<?php

/**
 * File: $Id$
 *
 * Translate on screen for subtypes of a module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_translate_subtype()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('subtype', 'regexp:/^(file|template|incltempl|blktempl|block)$/', $subtype)) return;
    if (!xarVarFetch('subname', 'str:1:', $subname)) return;

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
        if ($dnType == XARMLS_DNTYPE_MODULE) {
            $msg = xarML('Could not bind translation backend to module \'#(1)\'. Try regenerating skeletons.', $dnName);
        } elseif ($dnType == XARMLS_DNTYPE_THEME) {
            $msg = xarML('Could not bind translation backend to theme \'#(1)\'. Try regenerating skeletons.', $dnName);
        } elseif ($dnType == XARMLS_DNTYPE_CORE) {
            $msg = xarML('Could not bind translation backend to core. Try regenerating skeletons.');
        } else {
            $msg = xarML('Could not bind translation: unknown domain type');
        }
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN', $msg);
        return;
    }
    if (!$backend->loadContext($ctxType, $ctxName)) return;

    if ($locale != 'en_US.utf-8') {
        // Load an english backend for original key translations
        $args['interface'] = 'ReferencesBackend';
        $args['locale'] = 'en_US.utf-8';
        $en_backend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
        if (!isset($en_backend)) return;
        if ($en_backend->bindDomain($dnType, $dnName) &&
            !$en_backend->loadContext($ctxType, $ctxName)) return;
    } else {
        $en_backend =& $backend;
    }

    $maxReferences = 5;

    $numEntries = 0;
    $entries = array();
    while (list($string, $translation) = $backend->enumTranslations()) {
        $entry = array('string' => htmlspecialchars($string), 'translation' => htmlspecialchars($translation), 'tid' => $backend->getTransientId($string));
        $e = $backend->getEntry($string);
        $entry['references'] = translations_grab_source_code($e['references'], $maxReferences);
        if (count($e['references']) > $maxReferences) {
            $entry['otherReferences'] = true;
            $entry['numOtherReferences'] = count($e['references']) - $maxReferences;
        } else {
            $entry['otherReferences'] = false;
        }
        $entries[] = $entry;
        $numEntries++;
    }

    $numKeyEntries = 0;
    $keyEntries = array();
    while (list($key, $translation) = $backend->enumKeyTranslations()) {
        $keyEntry = array('key' => htmlspecialchars($key), 'translation' => htmlspecialchars($translation));
        $e = $backend->getEntryByKey($key);
        $keyEntry['references'] = translations_grab_source_code($e['references'], $maxReferences);
        if (count($e['references']) > $maxReferences) {
            $keyEntry['otherReferences'] = true;
            $keyEntry['numOtherReferences'] = count($e['references']) - $maxReferences;
        } else {
            $keyEntry['otherReferences'] = false;
        }
        $en_translation = $en_backend->translateByKey($key);
        if (!$en_translation) {
            $en_translation = xarML('(Original translation not found)');
        }
        $keyEntry['en_translation'] = $en_translation;
        $keyEntries[] = $keyEntry;
        $numKeyEntries++;
    }

    $action = xarModURL('translations', 'admin', 'translate_update', array('subtype'=>$subtype, 'subname'=>$subname, 'numEntries'=>$numEntries, 'numKeyEntries'=>$numKeyEntries));
    $tplData = array('entries'=>$entries, 'keyEntries'=>$keyEntries, 'action'=>$action);

    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar($subtype,$subname);
    $druidbar = translations_create_translate_druidbar(TRANSLATE);

    $tplData = array_merge($tplData, $opbar, $trabar, $druidbar);
    $tplData['dnType'] = translations__dnType2Name($dnType);

    xarTplAddStyleLink('translations', 'translate_subtype');
    return $tplData;
}

function translations_grab_source_code($references, $maxReferences = NULL)
{
    static $files = array();
    $result = array();
    if ($maxReferences == NULL) {
        $maxReferences = count($references);
    }
    for ($i = 0; $i < count($references) && $i < $maxReferences; $i++) {
        $ref = $references[$i];
        if (!isset($files[$ref['file']])) {
            $files[$ref['file']] = file($ref['file']);
        }
        $j = $ref['line'] - 3;
        if ($j < 0) $j = 0;
        $source = array('pre'=>'', 'code'=>'', 'post'=>'');
        for ($c = 0; $c < 5 && $j < count($files[$ref['file']]); $c++, $j++) {
            if ($j < $ref['line'] - 1) {
                $source['pre'] .= htmlspecialchars($files[$ref['file']][$j]).'<br/>';
            } elseif ($j == $ref['line'] - 1) {
                $source['code'] = htmlspecialchars($files[$ref['file']][$j]).'<br/>';
            } else {
                $source['post'] .= htmlspecialchars($files[$ref['file']][$j]).'<br/>';
            }
        }
        $ref['source'] = $source;
        $result[] = $ref;
    }
    return $result;
}


?>
