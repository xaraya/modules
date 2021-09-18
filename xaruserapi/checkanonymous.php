<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**
 * Check to see if a message was anonymous
 * @param int	$id the message id
 * @return boolean
 */

function messages_userapi_checkanonymous($args)
{
    extract($args);

    if ($id == 0) {
        return false;
    }

    sys::import('modules.dynamicdata.class.objects.master');

    $object = DataObjectMaster::getObject(['name' => 'messages_messages']);
    $object->getItem(['itemid' => $id]);
    $postanon = $object->properties['postanon']->value;

    return $postanon;
}
