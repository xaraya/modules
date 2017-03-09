<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * retrieve the settings of a publication type
 *
 * @param $args array containing the publication type
 * @return array of setting keys and values
 */
  
function publications_userapi_getsettings($data)
{
    if (empty($data['ptid']))
        throw new Exception('Missing publication type for caching');
        
    // If already cached, then get that
    if (xarCore::isCached('publications', 'settings_' . $data['ptid']))
        return xarCore::getCached('publications', 'settings_' . $data['ptid']);

    sys::import('modules.dynamicdata.class.objects.master');
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));

    $pubtypesettings = array();
    try {
        $pubtypesettings = unserialize($pubtypeobject->properties['configuration']->getValue());
    } catch (Exception $e) {}

    $globalsettings = publications_userapi_getglobalsettings();
    if (is_array($pubtypesettings)) {
        $settings = $pubtypesettings + $globalsettings;
    } else {
        $settings = $globalsettings;
    }

    xarCore::setCached('publications', 'settings_' . $data['ptid'], $settings);
    return $settings;
}

function publications_userapi_getglobalsettings()
{
    $settings = array(
                'number_of_columns'     => 1,
                'items_per_page'        => 20,
                'defaultview'           => "Sections",
                'defaultsort'           => "name",
                'show_categories'       => false,
                'show_catcount'         => false,
                'namestring'            => 'pub',
                'show_prevnext'         => true,
                'show_keywords'         => false,
                'show_comments'         => false,
                'show_hitcount'         => false,
                'show_ratings'          => false,
                'show_archives'         => false,
                'show_map'              => false,
                'show_publinks'         => false,
                'show_pubcount'         => true,
                'do_transform'          => true,
                'title_transform'       => true,
                'usealias'              => false,                //CHECKME
                'defaultstate'          => 2,
                'defaultprocessstate'   => 0,
                'showsubmit'            => false,              //CHECKME
                'summary_template'      => '',
                'detail_template'       => '',
                'page_template'         => "",
                'theme'                 => '',
                    );
    return $settings;
}
?>