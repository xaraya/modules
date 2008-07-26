<?php

/**
 * update configuration for a module - hook for ('module','updateconfig','API')
 * Needs $extrainfo['cids'] from arguments, or 'cids' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function categories_adminapi_updateconfighook($args)
{
    sys::import('modules.dynamicdata.class.properties.master');
    $picker = DataPropertyMaster::getProperty(array('name' => 'categorypicker'));
    $picker->checkInput('basecid');

    extract($args);
    return $extrainfo;
}

?>
