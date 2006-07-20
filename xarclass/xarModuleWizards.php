<?php
/**
 * Purpose of file:  Module Wizards API
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage wizards
 * @link http://xaraya.com/index.php/release/3007.html
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
/**
 * Get the available wizards
 * Just returns an array
 */
function getwizards($info)
{
    $collection = new xarModuleWizardCollection($info);
    return $collection->getItems();
}

/**
 * List the available wizards
 * Run a wizard if the user clicks
 */
function listwizards()
{
    if(!xarSecurityCheck('RunWizard')) return;

    if (!xarVarFetch('info', 'isset', $info, NULL)) {return;}
    if (!xarVarFetch('wizard', 'str', $wizard, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('user', 'str', $user, NULL, XARVAR_NOT_REQUIRED)) {return;}

    $wizards = getwizards($info);
    $data['collection'] = $wizards;
    $data['module'] = ucfirst($info[0]);
    $data['type'] = ucfirst($info[1]);
    $data['actionurl'] = xarModUrl($info[0], $info[1], 'listscripts', array('info' =>$info));

    $data['message'] = "";
    if (isset($wizard)) {
        $wizards[$wizard]->run();
        $data['message'] = $wizards[$wizard]->getMessage();
    }
    // Send to BL.
    return xarTplModule('wizards','admin', 'listscripts',$data);
}

/**
 * xarModuleWizardCollection: class for the collection of wizards of a module
 *
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 * @access  public
 * @throws  none
 * @todo    none
*/

class xarModuleWizardCollection
{
    var $module;
    var $type;
    var $wizards;

/**
 * xarModuleWizardCollection: constructor for the class
 *
 * Just gets the calling module name
 *
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 * @access  public
 * @param   none
 * @return  xarModuleWizardCollection object
 * @throws  none
 * @todo    none
*/
    function xarModuleWizardCollection($info)
    {
        $this->module = $info[0];
        $this->type = $info[1];
        $this->loadwizarddir();
        $this->loadwizards();
    }

    function loadwizarddir()
    {
        $path = "modules/" . $this->module  . "/xarwizards" . "/" . $this->type;
        if(!file_exists($path) || !is_dir($path)) $path = '';
        $this->wizarddir = $path;
    }

    function loadwizards()
    {
        $wizardfiles = array();
        if (!$this->wizarddir == '') {
            $wizard = new xarModuleWizard();
            $dir = opendir($this->wizarddir);
            while ($file = readdir($dir)) {
                if (stristr($file,'wizard') !== false) {
                    include_once $this->wizarddir . "/" . $file;
                    $found = false;
                    foreach ($wizardfiles as $file) {
                        if ($file == $wizard) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $wizard->setModule($this->module);
                        $wizardfiles[$wizard->getName()] = $wizard;
                    }
                }
            }
        }
        $this->wizards = $wizardfiles;
    }
    function getItems()
    {
        return $this->wizards;
    }
}

/**
 * xarModuleWizard: parent class for module wizards
 *
 *
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 * @access  public
 * @throws  none
 * @todo    none
*/

class xarModuleWizard
{
    var $module;
    var $name;
    var $description;
    var $status = "active";
    var $message;

    function xarModuleWizard($name ='Generic Wizard',$description='No description of this wizard is available')
    {
        $this->setName($name);
        $this->setDescription($description);
//        $this->setStatus($status);
        $this->setMessage("No action performed");
    }

    function run()
    {
        return true;
    }
    function getName()
    {
        return $this->name;
    }
    function getDescription()
    {
        return $this->description;
    }
    function getStatus()
    {
        return $this->status;
    }
    function getMessage()
    {
        return $this->message;
    }

    function setModule($x)
    {
        $this->module = $x;
    }
    function setName($x)
    {
        $this->name = $x;
    }
    function setDescription($x)
    {
        $this->description = $x;
    }
    function setStatus($x)
    {
        $this->status = $x;
    }
    function setMessage($x)
    {
        $this->message = $x;
    }
}

?>