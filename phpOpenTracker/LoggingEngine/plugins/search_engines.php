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
// $Id: search_engines.php,v 1.3 2003/02/09 08:21:15 bergmann Exp $
//

require_once POT_INCLUDE_PATH . 'LoggingEngine/Plugin.php';
require_once POT_INCLUDE_PATH . 'Parser.php';

/**
* Stores information about search engines and search engine keywords.
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.3 $
* @since    phpOpenTracker-Search_Engines 1.0.0
*/
class phpOpenTracker_LoggingEngine_Plugin_search_engines extends phpOpenTracker_LoggingEngine_Plugin {
  /**
  * @var string $table
  */
  var $table = 'pot_search_engines';

  /**
  * @return array
  * @access public
  */
  function post() {
      $this->table = xarDBGetSiteTablePrefix() . '_pot_search_engines'; //added for xaraya
    if ($this->container['first_request'] &&
        !empty($this->container['referer_orig'])) {
      if (!$ignoreRules = @file(POT_CONFIG_PATH . 'search_engines.ignore.php')) {
        return phpOpenTracker::handleError(
          sprintf(
            'Cannot open "%s".',
            POT_CONFIG_PATH . 'search_engines.ignore.php'
          ),
          E_USER_ERROR
        );
      }

      if (!$matchRules = @file(POT_CONFIG_PATH . 'search_engines.match.php')) {
        return phpOpenTracker::handleError(
          sprintf(
            'Cannot open "%s".',
            POT_CONFIG_PATH . 'search_engines.match.php'
          ),
          E_USER_ERROR
        );
      }

      $ignore = false;

      foreach ($ignoreRules as $ignoreRule) {
        if (preg_match(trim($ignoreRule), $this->container['referer_orig'])) {
          $ignore = true;
          break;
        }
      }

      if (!$ignore) {
        foreach ($matchRules as $matchRule) {
          if (preg_match(trim($matchRule), $this->container['referer_orig'], $tmp)) {
            $keywords = $tmp[1];
          }
        }

        $searchEngineName = phpOpenTracker_Parser::match(
          $this->container['referer_orig'],
          phpOpenTracker_Parser::readRules(
            POT_CONFIG_PATH . 'search_engines.group.php'
          )
        );
      }

      if (isset($keywords) && isset($searchEngineName)) {
        $this->db->query(
          sprintf(
            "INSERT INTO %s
                         (accesslog_id,
                          search_engine, keywords)
                  VALUES (%d,
                          '%s', '%s')",

            $this->table,
            $this->container['accesslog_id'],
            $this->db->prepareString($searchEngineName),
            $this->db->prepareString($keywords)
          )
        );

        $this->container['referer']      = '';
        $this->container['referer_orig'] = '';
        $this->container['referer_id']   = 0;
      }
    }

    return array();
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
