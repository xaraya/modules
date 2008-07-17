<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * Produce the configuration file
 */
function logconfig_adminapi_produceconfig()
{
    $itemsnum = xarModGetVar('logconfig','itemstypenumber');

    $loggers = array();
    $loggers_i = 0;
    for ($i=1; $i <= $itemsnum; $i++) {

        $mylist = xarModAPIFunc('dynamicdata','user','getitems',
                              array('module'    => 'logconfig',
                                       'itemtype'  => $i,
                                       'getobject' => 1));    // get back the object list
        $items = $mylist->getItems();

        foreach ($items as $itemid => $properties) {
            $loggers[$loggers_i] = array();
            $loggers[$loggers_i]['type'] = $properties['loggerType'];

            unset($properties['loggerType']);
            unset($properties['description']);

            //fix log level:
            $translation = array ('Emergency' => 1,
                                  'Alert'     => 2,
                                  'Critical'  => 4,
                                  'Error'     => 8,
                                  'Warning'   => 16,
                                  'Notice'    => 32,
                                  'Info'      => 64,
                                  'Debug'     => 128);

            $newlevel = 0;
            $level = unserialize($properties['logLevel']);
            foreach ($level as $name => $state) {
                if ($state == 'ON') {
                    $newlevel += $translation[$name];
                }
            }
            $properties['logLevel'] = $newlevel;

            $loggers[$loggers_i]['config'] = $properties;
            $loggers_i++;
        }
    }


    $config_file = '';

    foreach ($loggers as $logger) {
        $config_file .= xarModAPIFunc('logconfig','admin','structuretophp', array('structure'=>$logger));
        $config_file .= ", \n";
    }

    $config_file .= "); \n";


    $config_file = "\$xarLogConfig = array ( \n" . $config_file;

    return $config_file;
}

?>