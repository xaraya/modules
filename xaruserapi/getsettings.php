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
    xarCore::setCached('publications', 'context' . $data['ptid'], $pubtypeobject->properties['configuration']->value);
    $settings = $pubtypeobject->properties['configuration']->getValue();
    return $settings;
}

?>
