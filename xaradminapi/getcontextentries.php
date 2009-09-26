<?php
/**
 * Get context entries
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @param $args['subtype'] translation subtype
 * @param $args['subname'] translation subname
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_adminapi_getcontextentries($args)
{
    extract($args);

    $dnType = $dntype;
    $dnName = $dnname;
    $ctxType = $subtype;
    $ctxName = $subname;

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarMod::apiFunc('translations','admin','create_backend_instance',$args);

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
        throw new Exception($msg);
    }
    if (!$backend->loadContext($ctxType, $ctxName)) return;

    if ($locale != 'en_US.utf-8') {
        // Load an english backend for original key translations
        $args['interface'] = 'ReferencesBackend';
        $args['locale'] = 'en_US.utf-8';
        $en_backend = xarMod::apiFunc('translations','admin','create_backend_instance',$args);
        if (!isset($en_backend)) return;
        if ($en_backend->bindDomain($dnType, $dnName) &&
            !$en_backend->loadContext($ctxType, $ctxName)) return;
    } else {
        $en_backend =& $backend;
    }

    $maxReferences = xarModVars::get('translations', 'maxreferences');

    if (!$parsedWorkingLocale = xarMLS__parseLocaleString($locale)) return false;
    if (!$parsedSiteLocale = xarMLS__parseLocaleString(xarMLSGetCurrentLocale())) return false;
    $workingCharset = $parsedWorkingLocale['charset'];
    $siteCharset = $parsedSiteLocale['charset'];
    if ($siteCharset != $workingCharset) {
        sys::import('xaraya.transforms.xarCharset');
        $newEncoding = new xarCharset;
    }

    $numEntries = 0;
    $numEmptyEntries = 0;
    $entries = array();
    while (list($string, $translation) = $backend->enumTranslations()) {
        if ($siteCharset != $workingCharset) {
            $translation = $newEncoding->convert($translation, $workingCharset, $siteCharset, 0);
        }
        $entry = array(
            'string' => htmlspecialchars($string),
            'translation' => htmlspecialchars($translation),
            'tid' => $backend->getTransientId($string));
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
        if (empty($translation)) $numEmptyEntries++;
    }

    $numKeyEntries = 0;
    $numEmptyKeyEntries = 0;
    $keyEntries = array();
    while (list($key, $translation) = $backend->enumKeyTranslations()) {
        if ($siteCharset != $workingCharset) {
            $translation = $newEncoding->convert($translation, $workingCharset, $siteCharset, 0);
        }
        $keyEntry = array(
            'key' => htmlspecialchars($key),
            'translation' => htmlspecialchars($translation));
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
        if (empty($translation)) $numEmptyKeyEntries++;
    }

    return array(
        'entries'=>$entries,
        'numEntries'=> $numEntries,
        'numEmptyEntries'=>$numEmptyEntries,
        'keyEntries'=>$keyEntries,
        'numKeyEntries'=> $numKeyEntries,
        'numEmptyKeyEntries'=> $numEmptyKeyEntries,);
}

function translations_grab_source_code($references, $maxReferences = NULL)
{
    $result = array();
    //static $files = array(); <-- this just takes too much memory
    $showContext = xarModVars::get('translations','showcontext');
    if(!$showContext) {
        $result[] = xarML('References have been disabled');
        return $result;
    }

    $files = array();
    $result = array();
    $currentFileData = '';
    $currentFileName = '';
    $referencesCount = count($references);
    $maxCodeLines = xarModVars::get('translations', 'maxcodelines');
    if ($maxReferences == NULL) {
        $maxReferences = $referencesCount;
    }
    for ($i = 0; $i < $referencesCount && $i < $maxReferences; $i++) {
        $ref = $references[$i];
        if ($ref['file'] != $currentFileName) {
            $currentFileName = $ref['file'];
            if (file_exists($ref['file']))  {
                // FIXME: this is potentially very memory hungry, cant we do this more efficient?
                $currentFileData = file($ref['file']);
            } else {
                // FIXME need more information about outdated references
                $currentFileData = array();
            }
        }
        $j = $ref['line'] - ($maxCodeLines/2) - 1;
        if ($j < 0) $j = 0;
        $source = array('pre'=>'', 'code'=>'', 'post'=>'');
        $linesCount = count($currentFileData);
        for ($c = 0; $c < $maxCodeLines && $j < $linesCount; $c++, $j++) {
            if ($j < $ref['line'] - 1) {
                $source['pre'] .= htmlspecialchars($currentFileData[$j]).'<br/>';
            } elseif ($j == $ref['line'] - 1) {
                $source['code'] = htmlspecialchars($currentFileData[$j]).'<br/>';
            } else {
                $source['post'] .= htmlspecialchars($currentFileData[$j]).'<br/>';
            }
        }
        $ref['source'] = $source;
        $result[] = $ref;
    }
    return $result;
}

?>