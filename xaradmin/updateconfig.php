<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * 
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Update configuration
 */

sys::import('modules.dynamicdata.class.properties.master');

function publications_admin_updateconfig()
{
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // Get parameters
    //A lot of these probably are bools, still might there be a need to change the template to return
    //'true' and 'false' to use those...
    if(!xarVarFetch('settings',          'array',   $settings,      array(), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('usetitleforurl',    'isset', $usetitleforurl,    0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('defaultstate',     'isset', $defaultstate,     0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('defaultsort',       'isset', $defaultsort,  'date',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('usealias',          'isset', $usealias,          0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('ptid',              'isset', $ptid,              xarModVars::get('publications', 'defaultpubtype'),  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'global', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('AdminPublications',1,'Publication',"$ptid:All:All:All")) return;

    if ($data['tab'] == 'global') {
        if(!xarVarFetch('shorturls',         'isset', $shorturls,         0,  XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('defaultpubtype',    'isset', $defaultpubtype,    1,  XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('sortpubtypes',      'isset', $sortpubtypes,   'id',  XARVAR_NOT_REQUIRED)) {return;}
        xarModVars::set('publications', 'SupportShortURLs', $shorturls);
        xarModVars::set('publications', 'defaultpubtype', $defaultpubtype);
        xarModVars::set('publications', 'sortpubtypes', $sortpubtypes);
        if (xarDBGetType() == 'mysql') {
            if (!xarVarFetch('fulltext', 'isset', $fulltext, '', XARVAR_NOT_REQUIRED)) {return;}
            $oldval = xarModVars::get('publications', 'fulltextsearch');
            $index = 'i_' . xarDB::getPrefix() . '_publications_fulltext';
            if (empty($fulltext) && !empty($oldval)) {
                // Get database setup
                $dbconn = xarDB::getConn();
                $xartable = xarDB::getTables();
                $publicationstable = $xartable['publications'];
                // Drop fulltext index on publications table
                $query = "ALTER TABLE $publicationstable DROP INDEX $index";
                $result =& $dbconn->Execute($query);
                if (!$result) return;
                xarModVars::set('publications', 'fulltextsearch', '');
            } elseif (!empty($fulltext) && empty($oldval)) {
                //$searchfields = array('title','summary','body','notes');
                $searchfields = explode(',',$fulltext);
                // Get database setup
                $dbconn = xarDB::getConn();
                $xartable = xarDB::getTables();
                $publicationstable = $xartable['publications'];
                // Add fulltext index on publications table
                $query = "ALTER TABLE $publicationstable ADD FULLTEXT $index (" . join(', ', $searchfields) . ")";
                $result =& $dbconn->Execute($query);
                if (!$result) return;
                xarModVars::set('publications', 'fulltextsearch', join(',',$searchfields));
            }
        }
    } else {

        // Get the publication type for this display and save the settings to it
        $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
        $pubtypeobject->getItem(array('itemid' => $ptid));
        $configsettings = $pubtypeobject->properties['configuration']->getValue();
        foreach ($configsettings as $key => $value)
            if (!isset($settings[$key])) $settings[$key] = 0;
        $pubtypeobject->properties['configuration']->setValue($settings);
        $pubtypeobject->updateItem(array('itemid' => $ptid));

        $pubtypes = xarModAPIFunc('publications','user','getpubtypes');
        if ($usealias) {
            xarModSetAlias($pubtypes[$ptid]['name'],'publications');
        } else {
            xarModDelAlias($pubtypes[$ptid]['name'],'publications');
        }

    //echo "<pre>";var_dump($settings);exit;
        // Pull the base category ids from the template and save them
        $picker = DataPropertyMaster::getProperty(array('name' => 'categorypicker'));
        $picker->checkInput('basecid');
    }
    xarResponseRedirect(xarModURL('publications', 'admin', 'modifyconfig',
                                  array('ptid' => $ptid, 'tab' => $data['tab'])));
    return true;
}
?>
