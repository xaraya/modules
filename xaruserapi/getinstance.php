<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
    Addition to the workflow module when there is a need
    to retrieve the actual instance rather than just an
    array of values. This can be used in conjunction with
    the "findinstances" api.

    @author Mike Dunn submitted by Court Shrock
    @access public
    @param $instaceId (required)
    @return id workflow Instance
*/
function workflow_userapi_getInstance($args)
{

    //this has to be include, instead of include_once #cls
    include('modules/workflow/tiki-setup.php');

    //make sure this user an access this instance
    if (!xarSecurityCheck('ReadWorkflow')) return;

    extract($args);

    //if not instance is set send this back we cannon continue
    if(!isset($instanceId)) return;

    //check to see if this hasn't alredy been done
    if(!function_exists("getInstance")){
        include_once(GALAXIA_LIBRARY.'/API.php');
    }

    $inst = new Instance();
    $inst->getInstance($instanceId);

    return $inst;
}

?>
