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

function translations_admin_translate_update()
{
    // Security Check
    if(!xarSecurity::check('AdminTranslations')) return;

    // FIXME voll context validation
    //$contexts = Load all contexts types;
    //$regexstring = "";
    //$i=0;
    //foreach($contexts as $context) {
    //    if ($i>0) $regexstring .= "|";
    //    $regexstring .= context_get_Name();
    //    $i++;
    //}
    //$regexstring = 'regexp:/^(' . $regexstring . ')$/';
    //if (!xarVar::fetch('subtype', $regexstring, $subtype)) return;

    if (!xarVar::fetch('subtype', 'str:1:', $subtype)) return;
    if (!xarVar::fetch('subname', 'str:1:', $subname)) return;
    if (!xarVar::fetch('numEntries', 'int:0:', $numEntries)) return;
    if (!xarVar::fetch('numKeyEntries', 'int:0:', $numKeyEntries)) return;

    if (!xarVar::fetch('dnType','int',$dnType)) return;
    if (!xarVar::fetch('dnName','str:1:',$dnName)) return;
    if (!xarVar::fetch('extid','int',$extid)) return;

    $ctxType = $subtype;
    $ctxName = $subname;

    $locale = translations_working_locale();

    $args['interface'] = 'ReferencesBackend';
    $args['locale'] = $locale;
    $backend = xarMod::apiFunc('translations','admin','create_backend_instance',$args);
    if (!isset($backend)) return;
    if (!$backend->bindDomain($dnType, $dnName)) {
        throw new Exception('Unknown');
    }
    if (!$backend->loadContext($ctxType, $ctxName)) return;

    $args['interface'] = 'ReferencesGenerator';
    $args['locale'] = $locale;
    $gen = xarMod::apiFunc('translations','admin','create_generator_instance',$args);
    if (!isset($gen)) return;
    if (!$gen->bindDomain($dnType, $dnName)) return;
    if (!$gen->create($ctxType, $ctxName)) return;

    if (!$parsedWorkingLocale = xarMLS::parseLocaleString($locale)) return false;
    if (!$parsedSiteLocale = xarMLS::parseLocaleString(xarMLS::getCurrentLocale())) return false;
    $workingCharset = $parsedWorkingLocale['charset'];
    $siteCharset = $parsedSiteLocale['charset'];
    if ($siteCharset != $workingCharset) {
        sys::import('xaraya.transforms.xarCharset');
        $newEncoding = new xarCharset;
    }

    for ($i = 0; $i < $numEntries; $i++) {
        unset($translation);
        if (!xarVar::fetch('tid'.$i, 'str::', $translation, '', xarVar::POST_ONLY)) return;
        if ($siteCharset != $workingCharset) {
            $translation = $newEncoding->convert($translation, $siteCharset, $workingCharset, 0);
        }
        // Lookup the string bounded to the tid$i transient id
        $e = $backend->lookupTransientId($i);
        if ($e) {
            $gen->addEntry($e['string'], $e['references'], $translation);
        }
    }
    while (list($key, $translation) = $backend->enumKeyTranslations()) {
        unset($translation);
        if (!xarVar::fetch('key'.$key, 'str::', $translation, '', xarVar::POST_ONLY)) return;
        if ($siteCharset != $workingCharset) {
            $translation = $newEncoding->convert($translation, $siteCharset, $workingCharset, 0);
        }
        $e = $backend->getEntryByKey($key);
        if ($e) {
            $gen->addKeyEntry($key, $e['references'], $translation);
        }
    }

    // Finish writing the file, close it and rename it from extension swp to xt
    $gen->close();

    // Jump to the next page
    xarController::redirect(xarController::URL('translations', 'admin', 'translate_subtype',
       array(
           'dnType' => $dnType,
           'dnName' => $dnName,
           'extid' => $extid,
           'defaultcontext'=>$subtype.':'.$subname)));
    return true;
}

?>