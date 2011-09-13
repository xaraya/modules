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
    try {
        $pubtypesettings = unserialize($pubtypeobject->properties['configuration']->getValue());
    } catch (Exception $e) {
        $pubtypesettings = array();
    }
    $globalsettings = publications_userapi_getglobalsettings();
    $settings = $pubtypesettings + $globalsettings;
    xarCore::setCached('publications', 'context' . $data['ptid'], $settings);
    return $settings;
}

function publications_userapi_getglobalsettings()
{
    $settings = array(
                'number_of_columns' => 1,
                'items_per_page' => 20,
                'defaultview' => "Sections",
                'defaultsort' => "name",
                'show_categories' => false,
                'show_catcount' => false,
                'show_prevnext' => true,
                'show_keywords' => false,
                'show_comments' => false,
                'show_hitcount' => false,
                'show_ratings' => false,
                'show_archives' => false,
                'show_map' => true,
                'show_publinks' => false,
                'show_pubcount' => true,
                'do_transform' => true,
                'title_transform' => true,
                'prevnextart' => true,
                'usealias' => false,                //CHECKME
                'defaultstate' => 2,
                'defaultprocessstate' => 0,
                'showsubmit' => false,              //CHECKME
                'summary_template' => '',
                'detail_template' => '',
                'page_template' => "",
                'theme' => '',
                'allow_translations' => true,
                    );
    return $settings;
}
?>