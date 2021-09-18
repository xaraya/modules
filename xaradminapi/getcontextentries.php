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
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_adminapi_getcontextentries($args)
{
    extract($args);

    $dnType = $dntype;          // themes, modules etc.
    $dnName = $dnname;          // theme, module name
    $ctxType = $subtype;        // filepath
    $ctxName = $subname;        // filename

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarMod::apiFunc('translations', 'admin', 'create_backend_instance', $args);

    if (!isset($backend)) {
        return;
    }
    if (!$backend->bindDomain($dnType, $dnName)) {
        $dnTypeText = xarMLSContext::getContextTypeText($dnType);
        $msg = xarML('Could not bind translation backend to #(1) \'#(2)\'. Try regenerating skeletons.', $dnTypeText, $dnName);
        throw new Exception($msg);
    }
    if (!$backend->loadContext($ctxType, $ctxName)) {
        return;
    }

    if ($locale != 'en_US.utf-8') {
        // Load an english backend for original key translations
        $args['interface'] = 'ReferencesBackend';
        $args['locale'] = 'en_US.utf-8';
        $en_backend = xarMod::apiFunc('translations', 'admin', 'create_backend_instance', $args);
        if (!isset($en_backend)) {
            return;
        }
        if ($en_backend->bindDomain($dnType, $dnName) &&
            !$en_backend->loadContext($ctxType, $ctxName)) {
            return;
        }
    } else {
        $en_backend =& $backend;
    }

    $maxReferences = xarModVars::get('translations', 'maxreferences');

    if (!$parsedWorkingLocale = xarMLS::parseLocaleString($locale)) {
        return false;
    }
    if (!$parsedSiteLocale = xarMLS::parseLocaleString(xarMLS::getCurrentLocale())) {
        return false;
    }
    $workingCharset = $parsedWorkingLocale['charset'];
    $siteCharset = $parsedSiteLocale['charset'];
    if ($siteCharset != $workingCharset) {
        sys::import('xaraya.transforms.xarCharset');
        $newEncoding = new xarCharset();
    }

    $numEntries = 0;
    $numEmptyEntries = 0;
    $entries = [];
    while ([$string, $translation] = $backend->enumTranslations()) {
        if ($siteCharset != $workingCharset) {
            $translation = $newEncoding->convert($translation, $workingCharset, $siteCharset, 0);
        }
        $entry = [
            'string' => htmlspecialchars($string),
            'translation' => htmlspecialchars($translation),
            'tid' => $backend->getTransientId($string), ];
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
        if (empty($translation)) {
            $numEmptyEntries++;
        }
    }

    $numKeyEntries = 0;
    $numEmptyKeyEntries = 0;
    $keyEntries = [];
    while ([$key, $translation] = $backend->enumKeyTranslations()) {
        if ($siteCharset != $workingCharset) {
            $translation = $newEncoding->convert($translation, $workingCharset, $siteCharset, 0);
        }
        $keyEntry = [
            'key' => htmlspecialchars($key),
            'translation' => htmlspecialchars($translation), ];
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
        if (empty($translation)) {
            $numEmptyKeyEntries++;
        }
    }

    return [
        'entries'=>$entries,
        'numEntries'=> $numEntries,
        'numEmptyEntries'=>$numEmptyEntries,
        'keyEntries'=>$keyEntries,
        'numKeyEntries'=> $numKeyEntries,
        'numEmptyKeyEntries'=> $numEmptyKeyEntries,];
}

function translations_grab_source_code($references, $maxReferences = null)
{
    $result = [];
    //static $files = array(); <-- this just takes too much memory
    $showContext = xarModVars::get('translations', 'showcontext');
    if (!$showContext) {
        $result[] = xarML('References have been disabled');
        return $result;
    }

    $files = [];
    $result = [];
    $currentFileData = '';
    $currentFileName = '';
    $referencesCount = count($references);
    $maxCodeLines = xarModVars::get('translations', 'maxcodelines');
    if ($maxReferences == null) {
        $maxReferences = $referencesCount;
    }
    for ($i = 0; $i < $referencesCount && $i < $maxReferences; $i++) {
        $ref = $references[$i];
        if ($ref['file'] != $currentFileName) {
            $currentFileName = $ref['file'];
            if (file_exists($ref['file'])) {
                // FIXME: this is potentially very memory hungry, cant we do this more efficient?
                $currentFileData = file($ref['file']);
            } else {
                // FIXME need more information about outdated references
                $currentFileData = [];
            }
        }
        $j = $ref['line'] - ($maxCodeLines/2) - 1;
        if ($j < 0) {
            $j = 0;
        }
        $source = ['pre'=>'', 'code'=>'', 'post'=>''];
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
