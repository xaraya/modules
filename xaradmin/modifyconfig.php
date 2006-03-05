<?php
/**
 * Tasks module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Tasks Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Tasks Module Development Team
 */
/**
 * @author Chad Kraeft
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author Tasks module development team
 * @return array
 */
function tasks_admin_modifyconfig()
{
    $data=array();

//     if (!xarSecAuthAction(0, 'tasks::', '', ACCESS_ADMIN)) {
//         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
//         return true;
//     }

    // Construct maximum depth combobox
    $maxdepthdropdown = array();
    for($x=0; $x<10; $x++) {
        $maxdepthdropdown[] = array('id'=>$x, 'name'=>$x);
    }
    $data['maxdepthdropdown']=$maxdepthdropdown;

    // Construct date format combobox
    $dateformatlist = xarModAPIFunc('tasks', 'user', 'dateformatlist');
    $dateformatdropdown = array();
    foreach($dateformatlist as $formatid=>$format) {
        $dateformatdropdown[] = array('id'    => $formatid,
                                    'name'    => strftime($format,time()));
    }
    $data['dateformatdropdown']=$dateformatdropdown;
    $data['dateformat']=xarModGetVar('tasks','dateformat');
    $data['showoptions']= xarModGetVar('tasks','showoptions');
    $data['showoptionschecked'] = xarModGetVar('tasks','showoptions') ? true : false;
    // WHICH ID TO RETURN DISPLAY TO (CURRENT | PARENT)
    $returnfromoptions = array(array('id' => 0, 'name' => xarML('Current task')),
                               array('id' => 1, 'name' => xarML('Parent task'))
                               );
    $data['returnfromoptions']=$returnfromoptions;
    $data['returnfromadd']=xarModGetVar('tasks','returnfromadd');
    $data['returnfromedit']=xarModGetVar('tasks','returnfromedit');
    $data['returnfromsurface']=xarModGetVar('tasks','returnfromsurface');
    $data['returnfrommigrate']=xarModGetVar('tasks','returnfrommigrate');
    $data['submitbutton']=xarML("Update tasks config");

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();

    return $data;
}

?>