<?php // $Id: s.admin.php 1.5 02/11/28 23:18:27+00:00 miko@miko.homelinux.org $
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Language defines for xaradmin.php
// ----------------------------------------------------------------------
//
define('_MULTISITES_UNABLE_MODIF_CONFIG','Not able to create that file: <strong>#NOM_FICHIER#</strong>. Mod needed: <strong>666</strong>');

define('_MULTISITES','Management of multisites');
define("_MULTISITES_CREATION","Initialization of multisites");
define('_MULTISITES_EXPLAIN',"
This operation is going to transform your installation from 'monosite' to a 'multisites' with only one site.<br />
This operation cannot be reversed or modified, except by hacking the sources.<br />
So ... 
");
define('_MULTISITES_SORRY','Sorry, already initialized.');
define('_MULTISITES_SERVERNAME',"
Multisites are uses 2 possible variables, HTTP_HOST, or SERVER_NAME. One or the other may not always work on your system, depending the way your server is configured. If the domain name you see below in front of the checked box is ok, do not change anything. Otherwise ...
");
define('_MULTISITES_WHEREIS',"
You will have to say where you want to install the folder which is going to hold all your subsites informations. Originally, it was the folder 'parameters'. Dont forget the last /, 'folder name/' for a folder installed in the root of the site.<br />
<strong>Unfortunately</strong>, this folder HAS TO BE created before, for question of rights, difficult to manage as 'nobody' on a http server. And it has to be writable by this software. I noticed it has to be 777 chmoded, not 755 ...
");
define('_MULTISITESWHEREIS',"
The sites' configurations will be in: 
");
define('_MULTISITES_SUITE1',"
We are going to create a folder with the name defined as above, and in this folder, we will create an other folder for this precises site, '
");
define('_MULTISITES_SUITE2',"
', and in this one, a folder 'images', a folder 'themes', in the folder 'images', we will create a folder 'topics'.<br />
Then we will create a script named 'whoisit.inc.php', in the top folder (sitesettings).<br />
In the folder '
");
define('_MULTISITES_SUITE3',"
', we are going to copy the original config.php and make a backup of it<br />Then we will write a new config.php in the top, for that reason, <strong>the original config.php must have a 666 authorisation.</strong>
");

define('_MULTISITES_CREATEFOLDER',"
This folder MUST have been created before
");

if (!defined('_MULTISITESNOAUTH')) {
	define('_MULTISITESNOAUTH','Not authorised to access Multisites module');
}

define('_MULTISITES_MASTER_EXPLAIN',"
Here you will be able to create new sub sites.<br />
You already know the name of the domain you are installing, with all its levels, IS www.domain.com or sub.domain.com.<br />
You have already created the database which is going to be used for this one.<br />
We are going to create, in the folder <strong>".xarModGetVar('multisites','whereisperso')."</strong>, a folder with the name of your new domain, without its extension and without the 3 level 'www.'.<br />
Then we will copy the complete folder ".xarModGetVar('multisites','masterfolder')." in the folder ".xarModGetVar('multisites','whereisperso')." changing its name. At the same time, we will modify the value of \$xarconfig['dbuname'] to give it the name of the database created.<br />
Finally, you will have a new site, identical to the master site, ready to be administred normally.
");

define('_MULTISITES_DN_EXPLAIN',"
The complete domain name to use, ie: www2.domain.com:
");
define('_MULTISITES_DATABASE_EXPLAIN',"
The name of the database used by this domain:
");
define('_MULTISITES_PREFIX_EXPLAIN',"
The name of the prefix used by this domain:
");
define('_MULTISITES_SUB_EXPLAIN','Vous allez pouvoir ici modifier certains paramètres propres à ce site.');

define("_MULTISITES_UNDECLARED",
" die(\"This domain name:<ul type='circle'>
	<li>HTTP_HOST: \".\$HTTP_HOST.\",
	<li>SERVER_NAME: \".\$SERVER_NAME.\"
	</ul>has not been declared as the sub-site of
	\".\$xarconfig['masterURL'].\", which is a multiste.<p>
	In order to do this, first you have to create a database, wich is going to be used for this domain name, and it has to be a .71 database.<br />This mean it can be a new database, or an old updated database. <p>
	Then go back on <a href=http://\".\$xarconfig['masterURL'].\">http://\".\$xarconfig['masterURL'].\"</a> in order to register it, if you are an administrator of course! Thanks.\");
");

define('_MULTISITES_UPDATE', 'Confirm the creation of this sub site');


/*
define('_ADDTEMPLATE', 'Add example item');
define('_CANCELTEMPLATEDELETE', 'Cancel deletion');
define('_CONFIRMTEMPLATEDELETE', 'Confirm deletion of example item');
define('_CREATEFAILED', 'Creation attempt failed');
define('_DELETEFAILED', 'Deletion attempt failed');
define('_DELETETEMPLATE', 'Delete example item');
define('_EDITTEMPLATE', 'Edit example item');
define('_EDITTEMPLATECONFIG', 'Edit example intems configuration');
define('_LOADFAILED', 'Load of module failed');
define('_NEWTEMPLATE', 'New example item');
define('_TEMPLATE', 'Template (example)');
define('_TEMPLATEADD', 'Add example item');
define('_TEMPLATECREATED', 'Example item created');
define('_TEMPLATEDELETED', 'Example item deleted');
define('_TEMPLATEDISPLAYBOLD', 'Display item names in bold');
define('_TEMPLATEMODIFYCONFIG', 'Modify example items configuration');
define('_TEMPLATENAME', 'Example item name');
define('_TEMPLATENOSUCHITEM', 'No such item');
define('_TEMPLATENUMBER', 'Example item number');
define('_TEMPLATEOPTIONS', 'Options');
define('_TEMPLATEUPDATE', 'Update example item');
define('_TEMPLATEUPDATED', 'Example item updated');
define('_VIEWTEMPLATE', 'View example items');
define('_TEMPLATEITEMSPERPAGE', 'Items per page');
*/

if (!defined('_CONFIRM')) {
	define('_CONFIRM', 'Confirm');
}
?>