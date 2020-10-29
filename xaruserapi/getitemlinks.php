<?php
/**
 * Publications
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * utility function to pass individual item links to a caller
 *
 * @param $args['itemids'] array of item ids to get
 * @return array Array containing the itemlink(s) for the item(s).
 */
 
function publications_userapi_getitemlinks($args)
{
    $itemlinks = array();
    
    sys::import('xaraya.structures.query');
    $xartable =& xarDB::getTables();
    $q = new Query('SELECT', $xartable['publications']);
    $q->addfield('id');
    $q->addfield('title');
    $q->addfield('description');
    $q->addfield('pubtype_id');
    $q->addfield('modify_date AS modified');
    $q->in('state', array(3,4));
    if (!empty($args['itemids'])) {
        if (is_array($args['itemids'])) {
            $itemids = $args['itemids'];
        } else {
            $itemids = explode(',', $args['itemids']);
        }
        $q->in('id', $itemids);
    }
    $q->addorder('title');
    $q->run();
    $result = $q->output();
    
    if (empty($result)) {
        return $itemlinks;
    }

    foreach ($result as $item) {
        if (empty($item['title'])) {
            $item['title'] = xarML('Display Publication');
        }
        $itemlinks[$item['id']] = array('url'   => xarModURL(
            'publications',
            'user',
            'display',
            array('itemid' => $item['id'])
        ),
                                    'title' => $item['title'],
                                    'label' => $item['description'],
                                    'modified' => $item['modified'],
                                    );
    }
    return $itemlinks;
}
