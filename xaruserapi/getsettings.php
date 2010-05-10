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
 
 * @author M. Lutolf
 */
/**
 * retrieve the settings of a publication type
 *
 * @param $args array containing the publication type
 * @return array of setting keys and values
 */
 
 sys::import('modules.dynamicdata.class.objects.master');
 
function publications_userapi_getsettings($data)
{
    if (empty($data['ptid']))
        throw new Exception('Missing publication type for caching');
    if (xarCore::isCached('publications', 'context' . $data['ptid']))
        return xarCore::getCached('publications', 'context' . $data['ptid']);
        
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $pubtypesettings = $pubtypeobject->properties['configuration']->getValue();
    $globalsettings = publications_userapi_getglobalsettings();
    $settings = $pubtypesettings + $globalsettings;
    xarCore::setCached('publications', 'context' . $data['ptid'], serialize($settings));
    return $settings;
}

function publications_userapi_getglobalsettings()
{
    $settings = array(
                'number_of_columns' => 1,
                'items_per_page' => 20,
                'defaultview' => "Sections",
                'showcategories' => false,
                'showcatcount' => false,
                'showprevnext' => true,
                'showcomments' => false,
                'showhitcounts' => false,
                'showratings' => false,
                'showarchives' => false,
                'showmap' => true,
                'showpublinks' => false,
                'showpubcount' => true,
                'dotransform' => true,
                'titletransform' => true,
                'prevnextart' => true,
                'usealias' => false,
                'page_template' => "",
                'defaultstate' => 2,
                'defaultsort' => "name",
                'showkeywords' => false,
                'showsubmit' => false,
                'admin_items_per_page' => 20,
                'defaultprocessstate' => 0,
                'allowtranslations' => true,
                    );
    return $settings;
}
?>
