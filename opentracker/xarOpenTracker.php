<?php
/**
 * Opentracker wrapper for xaraya
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage opentracker
 * @author Chris "Alley" van de Steeg
 */
require_once(dirname(__FILE__) . '/phpOpenTracker.php');
require_once POT_INCLUDE_PATH . 'LoggingEngine.php';

class xarOpenTracker extends phpOpenTracker
{
  /**
  * Invokes the phpOpenTracker Logging Engine.
  *
  * @param  optional array $parameters
  * @return boolean
  * @access public
  * @static
  */
  function log($parameters = array()) {
    include_once POT_INCLUDE_PATH . 'LoggingEngine.php';

    $le = new xarOpenTracker_LoggingEngine($parameters);

    return $le->log(
      isset($parameters['add_data']) ? $parameters['add_data'] : array()
    );
  }
	
}

class xarOpenTracker_LoggingEngine extends phpOpenTracker_LoggingEngine 
{
  /**
  * Stores the request information.
  *
  * @access private
  */
  function _storeRequestData() {
    if (!$this->container['first_request']) {
      $this->_runPostPlugins();
    }
    
	list($modName, $modType, $funcName) = xarRequestGetInfo();
    $xarInstanceId = xarVarGetCached('opentracker', 'xarinstanceid');
    
    //autodetect some instanceid's
    if (!isset($xarInstanceId) && $modName == 'articles') {
    	xarVarFetch('aid','int', $xarInstanceId,   NULL, XARVAR_NOT_REQUIRED);
    }
    if (!isset($xarInstanceId) && $modName == '"profiles') {
    	xarVarFetch('uid','int', $xarInstanceId,   NULL, XARVAR_NOT_REQUIRED);
    }
    if (!isset($xarInstanceId)) {
    	xarVarFetch('objectid','int', $xarInstanceId,   NULL, XARVAR_NOT_REQUIRED);
    }
    if (!isset($xarInstanceId)) {
    	xarVarFetch('id','int', $xarInstanceId,   NULL, XARVAR_NOT_REQUIRED);
    }
    $this->db->query(
      sprintf(
        "INSERT
           INTO %s
                (client_id,   accesslog_id,
                 document_id, timestamp,
        		 xar_uid,
        		 xar_modname,
        		 xar_modtype,
        		 xar_modfunc,
        		 xar_instanceid,
                 entry_document)
         VALUES (%d, %d,
                 %d, %d,
        		 %d,
        		 '%s',
        		 '%s',
        		 '%s',
        		 %d,
                 '%d')",

        $this->config['accesslog_table'], $this->container['client_id'],
        $this->container['accesslog_id'], $this->container['document_id'],
        $this->container['timestamp'],
        xarUserGetVar('uid'),
	    $modName,
	    $modType, 
	    $funcName,
        $xarInstanceId,
        $this->container['first_request'] ? 1 : 0
      )
    );
  }
	
}

?>
