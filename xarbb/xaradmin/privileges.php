<?php

/**
 * Manage definition of instances for privileges (unfinished)
 */
function xarbb_admin_privileges($args)
{
    extract($args);

    // Priviledge Mask
    if (!xarVarFetch('fid',          'isset', $fid,        'All')) {return;}       // Forum ID
    if (!xarVarFetch('cid',          'isset', $cid,         'All', XARVAR_NOT_REQUIRED)) {return;}      // Categorie ID
    if (!xarVarFetch('cids',         'isset', $cids,         NULL, XARVAR_DONT_SET)) {return;}      // Categorie IDs


    // General Parameters
    if (!xarVarFetch('apply',        'isset', $apply,        NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extpid',       'isset', $extpid,       NULL, XARVAR_DONT_SET)) {return;}      // Privilidge ID
    if (!xarVarFetch('extname',      'isset', $extname,      NULL, XARVAR_DONT_SET)) {return;}      // Priviledge Name
    if (!xarVarFetch('extrealm',     'isset', $extrealm,     NULL, XARVAR_DONT_SET)) {return;}      // Priv Realm
    if (!xarVarFetch('extmodule',    'isset', $extmodule,    NULL, XARVAR_DONT_SET)) {return;}      // ....
    if (!xarVarFetch('extcomponent', 'isset', $extcomponent, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extinstance',  'isset', $extinstance,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extlevel',     'isset', $extlevel,     NULL, XARVAR_DONT_SET)) {return;}

    if (!empty($extinstance)) {
        $parts = explode(':',$extinstance);
        if (count($parts) > 0 && !empty($parts[0])) $cid = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $fid = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $fname = $parts[2];
//        if (count($parts) > 3 && !empty($parts[3])) $aid = $parts[3];
    }

    if (!xarSecurityCheck('AdminxarBB',1,'Forum',"$cid:$fid")) return;

    // TODO: figure out how to handle more than 1 category in instances
    if (isset($cids) && is_array($cids)) {
        foreach ($cids as $catid) {
            if (!empty($catid)) {
                $cid = $catid;
                // bail out for now
                break;
            }
        }
    }

  /*  $title = '';
    if (!empty($aid)) {
        $article = xarModAPIFunc('articles','user','get',
                                 array('aid'      => $aid,
                                       'withcids' => true));
        if (empty($article)) {
            $aid = 0;
        } else {
            // override whatever other params we might have here
            $ptid = $article['pubtypeid'];
        // TODO: review when we can handle multiple categories and/or subtrees in privilege instances
            if (!empty($article['cids']) && count($article['cids']) > 0) {
                // if we don't have a category, or if we have one but this article doesn't belong to it
                if (empty($cid) || !in_array($cid, $article['cids'])) {
                    // we'll take the first category available... (for now)
                    $cid = $article['cids'][0];
                }
            } else {
                $cid = 0;
            }
            $uid = $article['authorid'];
            $title = $article['title'];
        }
    }*/

// TODO: figure out how to handle groups of users and/or the current user (later)
 /*   if (empty($uid) || $uid == 'All' || !is_numeric($uid)) {
        $uid = 0;
        if (!empty($author)) {
            $user = xarModAPIFunc('roles', 'user', 'get',
                                  array('name' => $author));
            if (!empty($user) && !empty($user['uid'])) {
                if (strtolower($author) == 'myself') $uid = 'Myself';
                else $uid = $user['uid'];
            } else {
                $author = '';
            }
        }
    } else {
        $author = ''; */
/*
        $user = xarModAPIFunc('roles', 'user', 'get',
                              array('uid' => $uid));
        if (!empty($user) && !empty($user['name'])) {
            $author = $user['name'];
        }
*/
 //   }


    $filter = array();
    if($cid != "All")
	    $filter["catids"] = array($cid);
    if($fid != "All")
	    $filter["fid"] = $fid;

    $numitems = xarModAPIFunc('xarbb','user','countforums',array("filter" => $filter));

	if($cid != 'All')
		$fids = xarModAPIFunc('xarbb','user','getallforums',array("assoc" => "fid", "catid" => $cid));
    else
		$fids = xarModAPIFunc('xarbb','user','getallforums',array("assoc" => "fid"));



    if(!in_array($fid,array_keys($fids)))
    	$fid = 'All';

    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($cid) ? 'All' : $cid;
    $newinstance[] = empty($fid) ? 'All' : $fid;

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,$newinstance,$extlevel);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarResponseRedirect(xarModURL('privileges', 'admin', 'modifyprivilege',
                                      array('pid' => $pid)));
        return true;
    }

    // get the list of current authors
/*  $authorlist =  xarModAPIFunc('articles','user','getauthors',
                                 array('ptid' => $ptid,
                                       'cids' => empty($cid) ? array() : array($cid)));
    if (!empty($author) && isset($authorlist[$uid])) {
        $author = '';
    }    */

    $data = array(
                  'cid' 		 => $cid,
                  'fid' 		 => $fid,
                  'cids' 		 => $cids,
                  'fids' 		 => $fids,
                  'numitems'     => $numitems,
                  'extpid'       => $extpid,
                  'extname'      => $extname,
                  'extrealm'     => $extrealm,
                  'extmodule'    => $extmodule,
                  'extcomponent' => $extcomponent,
                  'extlevel'     => $extlevel,
                  'extinstance'  => xarVarPrepForDisplay(join(':',$newinstance)),
                 );

    $catlist = array();
    $cidstring = xarModGetVar('xarBB', 'mastercids.1');       // Forum = Itemtype 1

    if (!empty($cidstring)) {
        $rootcats = explode (';', $cidstring);
        foreach ($rootcats as $catid) {
            $catlist[$catid] = 1;
        }
    }

    $seencid = array();
    if (!empty($cid)) {
        $seencid[$cid] = 1;
/*
        $data['catinfo'] = xarModAPIFunc('categories',
                                         'user',
                                         'getcatinfo',
                                         array('cid' => $cid));
*/
    }

    $data['cats'] = array();
    foreach (array_keys($catlist) as $catid) {
        $data['cats'][] = xarModAPIFunc('categories',
                                        'visual',
                                        'makeselect',
                                        Array('cid' => $catid,
                                              'return_itself' => true,
                                              'values' => &$seencid,
                                              'multiple' => 0,
                                              'javascript' => 'onchange="submit()"'));
    }

    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;
}

?>