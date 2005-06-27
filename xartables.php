<?php
/**
 * Return example table names to xaraya
 * 
 * @return array 
 */
function authldap_xartables()
{ 
    return array('authldap_usercache'
		 => xarDBGetSiteTablePrefix().'_authldap_usercache');
} 
