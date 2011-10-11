<?php
/**
 * Login via a block.
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */

/**
 * Login via a block.
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @author Jim McDonald
 * initialise block
 * @return array
 */
sys::import('modules.registration.xarblocks.login');
class Registration_LoginBlockDisplay extends Registration_LoginBlock implements iBlock
{
/**
 * Display func.
 * @param $data array containing title,content
 */
    function display()
    {
        $data = $this->getContent();
        if (xarUserIsLoggedIn()) {    
            if (!empty($this->showlogout)) {
                $data['name'] = xarUserGetVar('name');
                $this->setTemplateBase('logout');
                if (!empty($this->logouttitle))
                    $this->setTitle($this->logouttitle);
            } else {
                return;
            }
        } elseif (xarServer::getVar('REQUEST_METHOD') == 'GET') {
            xarVarFetch('redirecturl',   'pre:trim:str:1:', 
                $data['return_url']   , xarServer::getCurrentURL(array(),false), XARVAR_NOT_REQUIRED);
        } else {
            xarVarFetch('redirecturl',   'pre:trim:str:1', 
                $data['return_url']   , xarServer::getBaseURL(), XARVAR_NOT_REQUIRED);
        }
        return $data;
    }
}
?>