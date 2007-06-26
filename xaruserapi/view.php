<?php
/**
 * View users
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Members module
 */
/**
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 * view users
 */

// Get the query abstraction
sys::import('modules.query.class.query');

// Get the DD properties master class
sys::import('modules.dynamicdata.class.properties.master');

function members_userapi_view($args)
{
    extract($args);

    // Get parameters
    if(!xarVarFetch('startnum', 'int:1', $data['startnum'], 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('phase', 'enum:active:viewall', $phase, 'viewall', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('filetype', 'str:1', $filetype, '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('letter', 'str:1', $letter, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('search', 'str:1:100', $search, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('order', 'str', $order, NULL, XARVAR_DONT_SET)) {return;}
    //if(!xarVarFetch('cids', 'array', $cids, NULL, XARVAR_DONT_SET)) {return;}
    //if(!xarVarFetch('sort', 'str', $sort, 'ASC', XARVAR_NOT_REQUIRED)) {return;}
    $thisstart = xarSession::getVar('members.start')?xarSession::getVar('members.start'):1;

    //get the default selection key - do this early
    $defaultkey = xarModGetUserVar('members', 'defaultselectkey', $regid);
    if (empty($defaultkey))
        throw new BadParameterException(array($localmodule), "No select key chosen on the configuration page for #(1)");

    // this modvar holds the chars we'll be displaying on tabs
    $alphabet = unserialize(xarModGetUserVar('members','alphabet',0));
    $lastcats = xarSession::getVar('members.cats')?xarSession::getVar('members.cats'):array();

     // pick up the categories from the form
    $categories = DataPropertyMaster::getProperty(array('name' => 'categories'));
    $data['cids'] = $categories->returnInput('mycats');

    $letter = isset($letter)?$letter:'';
    $search = isset($search)?$search:'';

    // TODO: make this variable for use in other modules
    $module = xarModGetName();
    $regid = xarMod::getID($module);

    // here we begin assembling the query for displaying a list of members
    $myobject = DataObjectMaster::getObject(array('name' => $object));
    $xartable = xarDB::getTables();
    $q = new Query('SELECT');
    $q->setdistinct();

    // we need the datasource for this object. assume it's not the roles table
    $objectds = $myobject->getDataStores();
    $objecttable = '';
    foreach ($objectds as $key => $value) {
        if ($key != $xartable['roles']) {
            $objecttable = $key;
            break;
        }
    }

    // if no datasource found we're stuck
    if (empty($objecttable))
        throw new BadParameterException(array($object),'Did not find a datasource of the #1 object');

    // add the datasource table we found
    $q->addtable($objecttable,'m');

    // we need the second param because we'll be joining a couple of tables
    $q->addtable($xartable['roles'],'r');
    $q->join('r.id','m.id');

    // we'll put fields into the output of the query that have status active in the object
    $properties = $myobject->getProperties();
    $activefields = array();
    foreach ($properties as $property) {
        if ($property->getDisplayStatus() != DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE) continue;
        // Ignore fields with dd storage for now
        if ($property->source == 'dynamic_data') continue;

        $source = str_replace($xartable['roles'], 'r', $property->source);
        $source = str_replace($objecttable, 'm', $source);



        $alias = str_replace($xartable['roles'] . '.', 'r_', $property->source);
        $alias = str_replace($objecttable . '.', 'm_', $alias);
        $q->addfield($source . ' AS ' . $alias);
        $activefields[$alias] = $property->label;
        $columnfields[$alias] = $property->name;
        $sourcefields[$alias] = $source;
    }
    $data['fields'] = $activefields;
    $data['columns'] = $columnfields;
    //set the keyfield generically for use with table selects
    $tablekeyfield= $properties[$defaultkey]->source;
    $defaultkeyname= $properties[$defaultkey]->label;
    $tablekeyfield=str_replace($objecttable, 'm', $tablekeyfield);
    $keyfieldalias = str_replace('m.', 'm_', $tablekeyfield);

    $data['tablekeyfield'] = $tablekeyfield;
    $data['keyfieldalias'] = $keyfieldalias;
    $data['defaultkeyname'] = $defaultkeyname;
    $data['properties'] = $properties;

    //setup sorting by clicking on table header fields
    $sort = xarSession::getVar('members.sort')?xarSession::getVar('members.sort'):'DESC';
    $lastorder = xarSession::getVar('members.lastorder')?xarSession::getVar('members.lastorder'):'';
    // change  the sort direction if I clicked one of the column names
    // but only if the column name is the same so it acts like a toggle for that field
    // only change sort if column name is clicked, not a letter which will retain the current settings
    if (isset($order) && $data['startnum']== $thisstart){
        if ($order == $lastorder) {
            if($sort == 'ASC') $sort = 'DESC';
               else $sort = 'ASC';
        } else {
            $sort = 'ASC';
        }
        xarSession::setVar('members.sort',$sort);
        xarSession::setVar('members.lastorder',$order);
        $data['search'] = $search; //pass along search
    } elseif (empty($letter) && empty($search) && (!isset($data['cids']) || empty($data['cid']))) {
        //if order is not set - set it to the default key field but keep it at 'DESC'
        $order = $keyfieldalias;
        $sort = 'ASC';
        xarSession::setVar('members.lastorder',$order);
    }

    // we want only users, no groups
    $q->eq('r.type', xarRoles::ROLES_USERTYPE);

    // some users we don't want to display
    $q->ne('r.uname', 'anonymous');
    $q->ne('r.uname', 'myself');

    // are we filtering on any groups?
        if (isset($groups) && is_array($groups)) {
            $q->addtable($xartable['roles'],'r1');
            $q->addtable($xartable['rolemembers'],'rm1');
            $q->join('r.id','rm1.id');
            $q->join('rm1.parentid','r1.id');
            foreach ($groups as $group) $c[] = $q->peq('r1.name',trim($group));
            $q->qor($c);
        }

    // if we have categories filter on  but no general search them
    // this shows how it is done with the query abstraction
    $catnames = '';
    //we don't want to include a cat if it's a base cat (i don't think)
    $basecats = xarModAPIFunc('categories','user','getallcatbases',array('module' => 'members'));

    //pass basecats to template for use in conditional
    $data['basecats'] = $basecats;
    $itembasecats = array();
    foreach ($basecats as $cid) {
        $cat = $cid['category_id'];
        $catinfo=xarModAPIFunc('categories','user','getcatinfo',array('cid'=>$cat));
        $itembasecats[]=$cat;
        $itembasenames[$cat]=$catinfo['name'];
    }
    /*
       a. if cids are the same as our last cats, and not the base cats, and not empty
          we want to select on cids  and pass them back to the template - probably doing a sort
       b. if they are different to the last cats, and not the base cats, and not empty
          we want to select on these new ones, and pass them back to the template
      c.  if they are different to the last cats and are the base cats or empty
          we want to pass them back to the template, but not do any select
    */
    //order of these statements is important here
    $ourcats = array_diff($data['cids'],$itembasecats); //this will be the cats to search on

    $isbase = array();
    $newcids = array();
    if (empty($data['cids']) && empty($letter) && empty($search)) {
        $data['cids'] = $lastcats; //we are probably sorting here
    } elseif (!empty($letter) || !empty($search)) {
        $data['cids'] = $itembasecats;
    }
    //make sure we only go to the cats loop to select if we have something other than base cats
    $ourcats = array_diff($data['cids'],$itembasecats); //this will be the cats to search on
    //if (empty($lastcats)) $lastcats = $itembasecats; //always have something in cats to compare
    //check to see if the cids, if not empty, are the same as our last cats search $lastcats (from session var)
    if (is_array($lastcats) && !empty($data['cids']) ) {
        $newcids = array_diff($data['cids'],$lastcats); //we have some cats different to the last ones
        $isbase = array_diff($itembasecats,$newcids); //this will be empty if they are just base cats
    }


    //no matter what we want to set the session of last cats
    xarSession::setVar('members.cats',$data['cids']);
    //initialize the msg
    $data['msg'] = xarML("All members");

    //if we don't have just base cats
    if (!empty($ourcats) && empty($search) && empty($letter) && !empty($isbase)) {

        //this adds the categories tables to the array of available tables
        sys::import('modules.categories.xartables');
        xarDB::importTables(categories_xartables());
        $xartable = xarDB::getTables();

        // add the categories tables to the query and create the necessary links
        $q->addtable($xartable['categories_linkage'],'cl');
        $q->join('r.id','cl.item_id');
        //We don't need to complicate it with this join  - get the cat name later
        //$q->addtable($xartable['categories'],'c');
        //$q->join('cl.id','c.id');

        // these conditions are not added to the SQL yet, just lined up
        // the conditions go on the cid of the linkage table

        $c = array();

        foreach ($ourcats as $cid) {
                $c[]= $q->peq('cl.category_id', $cid);
        }

        // take the conditions we decided on above and add them to the query as a bunch of ORs
        $q->qor($c);

        //$q->addfield('c.name AS catname');
       //get the cat name and build the msg string
        $catnames = '';
        $countcat  = count($ourcats);
        $count = 0;
        foreach ($ourcats as $cid) {
           if ($count>0) {
              $catnames .= ' or ';
           }
           $catinfo = xarModAPIFunc('categories','user','getcatinfo',array('cid'=>$cid));
           $catnames .= ' "'.$catinfo['name'].'" ';
           $count++;
        }

        $data['msg'] = xarML('All members in categories #(1)',$catnames);

    } elseif ($letter) {

        if ($letter == 'Other') {
            // In this case we create a bunch of SQL conditions
            // this is better than the 1x way: no using SQL functions, and we can accomodate any type of 'alphabet'
            //foreach ($alphabet as $let) $q->notlike('r.name', $let.'%');
            foreach ($alphabet as $let) $q->notlike($tablekeyfield, $let.'%');
            $data['msg'] = xarML(
                'Members where #(1) begins with character not listed in alphabet above (labeled as "Other")',$defaultkeyname
            );
        }elseif ($letter == 'All') {
            $data['msg'] = xarML("All members");
        } else {
        // TODO: handle case-sensitive databases
            //$q->like('r.name', $letter.'%');
            $q->like($tablekeyfield, $letter.'%');
            if(strtolower($phase) == 'active') {
                $data['msg'] = xarML('Members Online where #(1) begins with "#(2)"', $defaultkeyname, $letter);
            } else {
                $data['msg'] = xarML('Members where #(1) begins with "#(2)"', $defaultkeyname, $letter);
            }
        }
         xarSession::setVar('members.cats','');
    } elseif ($search) {

        $qsearch = '%'.$search.'%';

        // Dynamically set on active fields - must have roles id - Search conditions _OR_
        $i = 0;
        $msg = '';
        foreach ($sourcefields as $sourcefield=>$value) {
            if ($value != 'r.id') { //we don't want this id showing do we?
                if ($i >0) {
                    $msg .= ' or';
                }
                $c[$i]= $q->plike($value, $qsearch);
                $msg .= ' '.$activefields[$sourcefield].' ';
                $i++;
            }
        }
        if (xarModGetVar('roles', 'searchbyemail')) {
            $searcharray[]='email';
            // in this case we add another condition
            $c[$i]= $q->plike('r.email', $qsearch);
            $msg .= ' email ';
            //$data['msg'] = xarML('Members whose Display Name or User Name or Email Address contains "#(1)"', $search);
        } else {
            $data['msg'] = xarML('Members whose Display Name or User Name "#(1)"', $search);
        }
        if (!empty($msg) && $i>0) {
            $data['msg'] = xarML('Members where #(1) contain "#(2)"',$msg,$search);
        }

        // take the conditions we decided on above and add them to the query as a bunch of ORs
        $q->qor($c);
         xarSession::setVar('members.cats','');
    } else {
        if(strtolower($phase) == 'active') {
            $data['msg'] = xarML("All members online");
        } else {
            $data['msg'] = xarML("All members");
        }
    }


    // CHECKME: do we need all 3 of these passed to the template
    $data['order'] = $order;
    $data['letter'] = $letter;
    $data['ourcats'] = $ourcats;
    $data['searchstring'] = $search;
    //reset search and cids
    $search = null;
    //jojo - want to pass cids so we can sort on the result set
    //but only sort on this result set if the search criteria have not changed

    switch(strtolower($phase)) {
        case 'active':
            $data['phase'] = 'active';
            $filter = time() - (xarConfigGetVar('Site.Session.Duration') * 60);
            $data['title'] = xarML('Online Members');

            // need to add the sessions table here
            $q->addtable($xartable['session_info'],'si');
            $q->addfield('si.ip_addr AS ipaddr');

            // a left join so we still get roles even if there is no session. perhaps not necessary
            $q->leftjoin('r.id','si.role_id');

            // the condition for 'active' would be the following. haven't tested it
            $q->gt('last_use', $filter);

            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Active Members')));
            break;

        case 'viewall':
            $data['phase'] = 'viewall';
            $data['title'] = xarML('All Members');

            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('All Members')));
            break;
    }

    // set the data display parameters
    // we don't want the whole record set
    $q->setrowstodo(xarModGetVar($module, 'itemsperpage'));
    $q->setstartat($data['startnum']);
    $q->setorder($order,$sort);

    // display the query if I need to
//    $q->qecho();
//    exit;

      if (isset($filetype) && $filetype == 'pdf')
    {

        $qt=xarSession::getVar('members.query')?xarSession::getVar('members.query'):null;
        if (isset($qt)) {
           $q = $qt;
           $q = unserialize($q);
        }
     if (!$q->run()) return;

    } else {
        // run the query
        // it does the bindvars thing automatically away from mine eyes :)
        if (!$q->run()) return;
    }
    // get the total number of rows irrespective of number to be displayed
    $data['total'] = $q->getrows();

    // get the records to be displayed
    $items = $q->output();

    // keep track of the selected id's
    $data['idlist'] = array();

    // Check individual privileges for Edit / Delete
    // still can't do without this step, so we run the loop
    for ($i = 0, $max = count($items); $i < $max; $i++) {
        $item = $items[$i];
        $data['idlist'][] = $item['r_id'];

        // Grab the list of groups this role belongs to
        $groups = xarModAPIFunc('roles', 'user', 'getancestors', array('id' => $item['r_id']));
        foreach ($groups as $group) {
            $items[$i]['groups'][$group['id']] = $group['name'];
        }

        // Change email to a human readible entry.  Anti-Spam protection.
        if (xarUserIsLoggedIn()) {
            $items[$i]['emailurl'] = xarModURL(
                'roles', 'user', 'email',
                array('id' => $item['r_id'])
            );
        } else {
            $items[$i]['emailurl'] = '';
        }

    }
    $data['pmicon'] = '';
    // Add the array of items to the template variables
    $data['items'] = $items;

    $data['itemsperpage']=xarModGetVar('members', 'itemsperpage');

    // a bunch of params the pager will want to see in it's target url
    // order and sort are used by the up and down arrows
    $data['params'] = array(
        'order' => $order,
        'sort' => $sort,
        'startnum' => "%%",
    );
    xarSession::setVar('members.start',$data['startnum']);
    // need this in case this code is turned into a dprop
    $data['regid'] = $regid;

    // give the template the alphabet chars
    $data['alphabet'] = $alphabet;
    xarSession::setVar('members.query',serialize($q));

    return $data;
}

?>
