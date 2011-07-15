<?php
/**
 * Display module overview
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Contact Form Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Overview displays standard Overview page
 *
 * @returns array xarTplModule with $data containing template data
 * @return array containing the menulinks for the overview item on the main manu
 * @since 14 Oct 2005
 */
function contactform_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminContactForm',0)) return;

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */

     $filters['where'] = 'name eq "contactform_default"';

     $object = DataObjectMaster::getObjectList(array(
                                    'name' => 'objects',
                                ));
     $items = $object->getItems($filters);
     $item = end($items);
     $data['objectid'] = $item['objectid'];

    return xarTplModule('contactform', 'admin', 'overview', $data);
}

?>