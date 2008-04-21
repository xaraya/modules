<?php
/**
 * @author  Marc Lutolf
 * get a particular field value of a particular member
 * @param int   id  user's role id
 * @param str   field  field name (dataprooerty name) 
 * @return mixed
 */

function registration_userapi_getmemberfield($args)
{
    if (!isset($args['field'])) return false;
    if (!isset($args['id'])) $args['id'] = xarSession::getVar('role_id');
    $object = DataObjectMaster::getObject(array('objectid' => xarModVars::get('registration', 'registrationobject')));
    $item = $object->getItem(array('itemid' => $args['id']));
    $fields = $object->getFieldValues();
    if (!isset($fields[$args['field']])) return false;
    return $fields[$args['field']];
}
?>