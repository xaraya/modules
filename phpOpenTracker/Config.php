<?php
//
// +---------------------------------------------------------------------+
// | phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
// +---------------------------------------------------------------------+
// | Copyright (c) 2000-2003 Sebastian Bergmann. All rights reserved.    |
// +---------------------------------------------------------------------+
// | This source file is subject to the phpOpenTracker Software License, |
// | Version 1.0, that is bundled with this package in the file LICENSE. |
// | If you did not receive a copy of this file, you may either read the |
// | license online at http://phpOpenTracker.de/license/1_0.txt, or send |
// | a note to license@phpOpenTracker.de, so we can mail you a copy.     |
// +---------------------------------------------------------------------+
// | Author: Sebastian Bergmann <sebastian@phpOpenTracker.de>            |
// +---------------------------------------------------------------------+
//
// $Id: Config.php,v 1.9 2003/01/11 15:39:22 bergmann Exp $
//

/**
* phpOpenTracker Configuration Container
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.9 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_Config {
  /**
  * Loads the configuration.
  *
  * @return array
  * @access public
  * @static
  */
  function &singleton() {
    static $config;

    if (!isset($config)) {
      if (!$config = @parse_ini_file(POT_CONFIG_PATH . 'phpOpenTracker.php')) {
        die('phpOpenTracker Error: Could not open ' . POT_CONFIG_PATH . 'phpOpenTracker.php');
      }
      
      $config = array_change_key_case($config, CASE_LOWER);

      if ($config['debug_level'] > 1) {
        error_reporting(E_ALL);
      }

      if ($config['get_parameter_filter']) {
        $config['get_parameter_filter'] = explode(
          ',',
          str_replace(
            ' ',
            '',
            $config['get_parameter_filter']
          )
        );
      } else {
        $config['get_parameter_filter'] = array();
      }

      if ($config['logging_engine_plugins']) {
        $config['logging_engine_plugins'] = explode(
          ',',
          str_replace(
            ' ',
            '',
            $config['logging_engine_plugins']
          )
        );
      } else {
        $config['logging_engine_plugins'] = array();
      }
    }

      //modification for Xaraya:
        if (xarCore_getSystemVar('DB.Type') == "postgres") {
            $config["db_type"] = 'pgsql';
        } else {
            $config["db_type"] = xarCore_getSystemVar('DB.Type');
        }
        if (strpos(xarCore_getSystemVar('DB.Host'), ":") != false) {
            list($config["db_host"], $config["db_port"]) =
                    split(":", xarCore_getSystemVar('DB.Host'));
        } else {
            $config["db_host"] = xarCore_getSystemVar('DB.Host');
        }
          $config["db_database"] = xarCore_getSystemVar('DB.Name');
          $config["db_user"] = xarCore_getSystemVar('DB.UserName');
          $config["db_password"] = xarCore_getSystemVar('DB.Password');
          $config["jpgraph_path"] = POT_CONFIG_PATH . "../../jpgraph/";
          
          $prefix = xarDBGetSiteTablePrefix();
          $config['additional_data_table']   = $prefix."_pot_add_data";
        $config['accesslog_table']         = $prefix."_pot_accesslog";
        $config['documents_table']         = $prefix."_pot_documents";
        $config['exit_targets_table']      = $prefix."_pot_exit_targets";
        $config['hostnames_table']         = $prefix."_pot_hostnames";
        $config['operating_systems_table'] = $prefix."_pot_operating_systems";
        $config['referers_table']          = $prefix."_pot_referers";
        $config['user_agents_table']       = $prefix."_pot_user_agents";
        $config['visitors_table']          = $prefix."_pot_visitors";

      //end mod
    
    return $config;
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
