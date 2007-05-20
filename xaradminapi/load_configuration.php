<?php
/* --------------------------------------------------------------
   $Id: languages.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(languages.php,v 1.5 2002/11/22); www.oscommerce.com
   (c) 2003  nextcommerce (languages.php,v 1.6 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

function commerce_adminapi_load_configuration() {
    sys::import('modules.xen.xarclasses.xenquery');
    xarModAPILoad('commerce');
    $xartables = xarDB::getTables();
    $q = new xenQuery('SELECT',$xartables['commerce_configuration'], array('configuration_key AS cfgKey','configuration_value AS cfgValue'));
    if(!$q->run()) return;
    $configuration_array = array();
    foreach ($q->output() as $configuration) {
        $varname = strtolower($configuration['cfgKey']);
        $configuration_array[$varname] = $configuration['cfgValue'];
    }
    return $configuration_array;
}
?>