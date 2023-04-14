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

function translations_admin_translate_subtype()
{
    // Security Check
    if(!xarSecurity::check('AdminTranslations')) return;

    if (!xarVar::fetch('dnType','int',$dnType)) return;
    if (!xarVar::fetch('dnName','str:1:',$dnName)) return;
    if (!xarVar::fetch('extid','int',$extid)) return;

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

    // FIXME voll do we use subtype,subname really?
    if (!xarVar::fetch('defaultcontext', 'str:1:', $defaultcontext)) {
        if (!xarVar::fetch('subtype', 'str:1:', $subtype)) return;
        if (!xarVar::fetch('subname', 'str:1:', $subname)) return;
    } else {
        list($subtype1,$subtype2,$subname) = explode(':',$defaultcontext);
        $subtype = $subtype1.':'.$subtype2;
    }

    $args = array();
    $args['dntype'] = $dnType;
    $args['dnname'] = $dnName;
    $args['subtype'] = $subtype;
    $args['subname'] = $subname;
    $entries = xarMod::apiFunc('translations','admin','getcontextentries',$args);

    $args = array();
    $args['dntype'] = $dnType;
    $args['dnname'] = $dnName;
    $args['subtype'] = 'modules:';
    $args['subname'] = 'fuzzy';
    $fuzzyEntries = xarMod::apiFunc('translations','admin','getcontextentries',$args);

    $entries['fuzzyEntries'] = $fuzzyEntries['entries'];
    $entries['fuzzyNumEntries'] = $fuzzyEntries['numEntries'];
    $entries['fuzzyNumEmptyEntries'] = $fuzzyEntries['numEmptyEntries'];
    $entries['fuzzyKeyEntries'] = $fuzzyEntries['keyEntries'];
    $entries['fuzzyNumKeyEntries'] = $fuzzyEntries['numKeyEntries'];
    $entries['fuzzyNumEmptyKeyEntries'] = $fuzzyEntries['numEmptyKeyEntries'];

    $data = $entries;
    $data['action'] = xarController::URL('translations', 'admin', 'translate_update', array('subtype'=>$subtype, 'subname'=>$subname, 'numEntries'=>$entries['numEntries'], 'numKeyEntries'=>$entries['numKeyEntries'], 'numEmptyEntries'=>$entries['numEmptyEntries'], 'numEmptyKeyEntries'=>$entries['numEmptyKeyEntries']));

    $opbar = translations_create_opbar(TRANSLATE, $dnType, $dnName, $extid);
    $trabar = translations_create_trabar($dnType, $dnName, $extid, $subtype,$subname);
    $druidbar = translations_create_druidbar(TRAN, $dnType, $dnName, $extid);
    $data = array_merge($data, $opbar, $trabar, $druidbar);

    $data['dnType'] = $dnType;
    $data['dnTypeText'] = xarMLSContext::getContextTypeText($dnType);
    $data['dnName'] = $dnName;
    $data['extid'] = $extid;

    return $data;
}

?>