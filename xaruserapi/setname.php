<?php
/**
 * Set name
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Members module
 */
/**
 * @author  jojodee
 * create the the roles username value to be used in the members object and roles
 */

function members_userapi_setname($args)
{
    $myobject = xarModApiFunc('dynamicdata','user','getobject', array('name' => 'members_members'));
    $properties = $myobject->getProperties();
  $myobject->checkInput();
  $valuestouse =  xarModVars::get('members', 'usernamevars');
  $valuestouse = explode('.',$valuestouse);
  if (empty($valuestouser) || count($valuestouse==0)) {
     $valuestouse = array('last_name','first_name'); //fall back here
  }
  $uname = ''; //username string to use
  $propname = '';//string var
  $propvalue = '';
  foreach ($properties as $property)
  {
    $propname =$property->name;
    if (in_array($propname,$valuestouse))
      {
              $propvalue =$myobject->properties[$propname]->value;
              $uname .= $propvalue.'.';
      }
 
  }
  $inttime = time(); //need this for uniqueness!! FIX to use eg id
  $uname .= $inttime;
  return $uname;

}
?>