<?php
/**
 * Event API for dojo activator
 *
 * @package modules
 * @subpackage dojo
 * @copyright The Digital Development Foundation, 2006
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/

/**
 * Module Load event handler
 *
 * Reacts on the module load event to activate dojo, once. We can probably 
 * usually trust on one such event occurring, unless a site is heavily optimized
 * and uses something like sessionless stuff or aggressive output caching.
 *
 * @return bool
 * @author Marcel van der Boom
 * @todo give this a spin on a heavily optimizid site wrt sessionless browsing and output caching
 **/
function dojo_eventapi_OnModLoad($args)
{
  static $firstRun = true;
  
  if($firstRun) {
      // Activate dojo
      xarLogMessage('EVT: activating dojo javascript framework');
      // Let a template handle what we need to do, so we expose this 'client'
      // stuff as soon as posible to the templates and it can be customized
      xarTplModule('dojo','util','activate');
      $firstRun = false;
  }  
  return true;
}
?>