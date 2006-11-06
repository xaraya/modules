<?php
/**
 * @package modules
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Get fields containing links for different modules/itemtypes
 * @author mikespub
 * @returns array
 * @return array of module titles and their link fields
 * @throws DATABASE_ERROR
*/
function sitetools_adminapi_getlinkfields($args)
{
    extract($args);

    $modules = array();

    $proptypes = xarModAPIFunc('dynamicdata','user','getproptypes');

    // find relevant fields for articles
    if (xarModIsAvailable('articles')) {
        $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
        $fieldformats = xarModAPIFunc('articles','user','getpubfieldformats');
        foreach ($pubtypes as $pubid => $pubtype) {
            $fields = array();
            foreach ($pubtype['config'] as $field => $info) {
                if (empty($info['label'])) continue;
                switch ($info['format'])
                {
                    case 'url':
                    case 'image':
                // skip imagelists here
                    //case 'imagelist':
                    case 'urltitle':
                        if (isset($fieldformats[$info['format']])) {
                            $type = $fieldformats[$info['format']];
                        } else {
                            $type = $info['format'];
                        }
                        $fields[] = array('name' => $info['label'],
                                          'field' => 'articles.' . $pubid . '.' . $field,
                                          'type' => $type);
                        break;
                    default:
                        break;
                }
            }
            $object = xarModAPIFunc('dynamicdata','user','getobject',
                                    array('module' => 'articles',
                                          'itemtype' => $pubid));
            if (!empty($object) && count($object->properties) > 0) {
                foreach ($object->properties as $name => $property) {
                    switch ($proptypes[$property->type]['name'])
                    {
                        case 'url':
                        case 'image':
                    // skip imagelists here
                        //case 'imagelist':
                        case 'urlicon':
                        case 'urltitle':
                            $fields[] = array('name' => $property->label,
                                              'field' => 'articles.' . $pubid . '.' . $name,
                                              'type' => $proptypes[$property->type]['label']);
                            break;
                        default:
                            break;
                    }
                }
            }
            if (count($fields) > 0) {
                $modules[$pubtype['descr']] = $fields;
            }
        }
    }

    // find relevant fields for roles
    // only 1 itemtype for now, but groups might have separate DD fields later on
    $rolesobject = xarModAPIFunc('dynamicdata','user','getobject',
                                 array('module' => 'roles'));
    if (!empty($rolesobject) && count($rolesobject->properties) > 0) {
        $fields = array();
        foreach ($rolesobject->properties as $name => $property) {
            switch ($proptypes[$property->type]['name'])
            {
                case 'url':
                case 'image':
            // skip imagelists here
                //case 'imagelist':
                case 'urlicon':
                case 'urltitle':
                    $fields[] = array('name' => $property->label,
                                      'field' => 'roles.0.' . $name,
                                      'type' => $proptypes[$property->type]['label']);
                    break;
                default:
                    break;
            }
        }
        if (count($fields) > 0) {
            $descr = xarML('Users');
            $modules[$descr] = $fields;
        }
    }

    // TODO: find relevant fields for ...

    return $modules;
}

?>