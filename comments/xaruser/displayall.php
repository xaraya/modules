<?php

/**
 * Display comments from one or more modules and item types
 *
 */
function comments_user_displayall($args) 
{

    $modarray = array();
    if (empty($args['modid'])) {
        $args['modid'] = xarVarCleanFromInput('modid');
        if (!$args['modid']) {
            $modarray[] = 'all';
        }  else      {
            $modarray = $args['modid'];
        }
    }   else {     
        $modarray=$args['modid'];
    }        

    if (!isset($args['itemtype'])) {
        $args['itemtype'] = xarVarCleanFromInput('itemtype');
        if (!$args['itemtype']) {
            $itemtype = null;
        }  else      {
            $itemtype = $args['itemtype'];
        }
    }   else {     
        $itemtype=$args['itemtype'];
    }        
   
    if (empty($args['order'])) {
        $args['order'] = xarVarCleanFromInput('order');
        if (!$args['order']) {
            $args['order']='DESC';
        }
    }
    if (empty($args['howmany'])) {
        $args['howmany'] = xarVarCleanFromInput('howmany');
        if (!$args['howmany']) {
            $args['howmany']='20';
        }
    }
    if (empty($args['first'])) {
        $args['first'] = xarVarCleanFromInput('first');
        if (!$args['first']) {
            $args['first']='1';
        }
    }
    if (empty($args['block_is_calling'])) {
        $args['block_is_calling']=0;
    } 
    if (empty($args['truncate'])) {
        $args['truncate']='';
    }
    if (empty($args['addmodule'])) {
        $args['addmodule']='off';
    }
    if (empty($args['addobject'])) {
        $args['addobject']=21;
    }
    if (empty($args['addcomment'])) {
        $args['addcomment']=20;
    }
    if (empty($args['adddate'])) {
        $args['adddate']='on';
    }
    if (empty($args['adddaysep'])) {
        $args['adddaysep']='on';
    }
    if (empty($args['addauthor'])) {
        $args['addauthor']=1;
    }
    if (empty($args['addprevious'])) {
        $args['addprevious']=0;
    }

    $args['returnurl'] = '';

    // get the list of modules+itemtypes that comments is hooked to
    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'comments'));

    // initialize list of module and pubtype names    
    $modlist = array();
    $modname = array();
    $modview = array();
    $modlist['all'] = xarML('All');
    // make sure we only retrieve comments from hooked modules
    $todolist = array();
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $module => $value) {
            $modid = xarModGetIDFromName($module);
            if (!isset($modname[$modid])) $modname[$modid] = array();
            if (!isset($modview[$modid])) $modview[$modid] = array();
            $modname[$modid][0] = ucwords($module);
            $modview[$modid][0] = xarModURL($module,'user','view');
            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc($module,'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
            if (!empty($mytypes) && count($mytypes) > 0) {
                 foreach (array_keys($mytypes) as $itemtype) {
                     $modname[$modid][$itemtype] = $mytypes[$itemtype]['label'];
                     $modview[$modid][$itemtype] = $mytypes[$itemtype]['url'];
                 }
            }
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                foreach ($value as $itemtype => $val) {
                    $todolist[] = "$module.$itemtype";
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                    }
                    $modlist["$module.$itemtype"] = ucwords($module) . ' - ' . $type;
                }
            } else {
                $todolist[] = $module;
                $modlist[$module] = ucwords($module);
                // allow selecting individual item types here too (if available)
                if (!empty($mytypes) && count($mytypes) > 0) {
                    foreach ($mytypes as $itemtype => $mytype) {
                        if (!isset($mytype['label'])) continue;
                        $modlist["$module.$itemtype"] = ucwords($module) . ' - ' . $mytype['label'];
                    }
                }
            }
        }
    }

    // replace 'all' with the list of hooked modules (+ xarbb if necessary ?)
    if (count($modarray) == 1 && $modarray[0] == 'all') {
        $args['modarray'] = $todolist;
    } else {
        $args['modarray'] = $modarray;
    }

    $comments = xarModAPIFunc('comments','user','get_multipleall',$args);
    $settings = xarModAPIFunc('comments','user','getoptions');

    if (!empty($args['order'])) {
        $settings['order']=$args['order'];        
    }

    // get title and link for all module items (where possible)
    $items = array();
    if (!empty($args['addobject'])) {
        // build a list of item ids per module and item type
        foreach (array_keys($comments) as $i) {
            $modid = $comments[$i]['xar_modid'];
            $itemtype = $comments[$i]['xar_itemtype'];
            if (!isset($items[$modid])) $items[$modid] = array();
            if (!isset($items[$modid][$itemtype])) $items[$modid][$itemtype] = array();
            $itemid = $comments[$i]['xar_objectid'];
            $items[$modid][$itemtype][$itemid] = '';
        }
        // for each module and itemtype, retrieve the item links (if available)
        foreach ($items as $modid => $itemtypes) {
            $modinfo = xarModGetInfo($modid);
            foreach ($itemtypes as $itemtype => $itemlist) {
                $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                                           array('itemtype' => $itemtype,
                                                 'itemids' => array_keys($itemlist)),
                                           // don't throw an exception if this function doesn't exist
                                           0);
                if (!empty($itemlinks) && count($itemlinks) > 0) {
                    foreach ($itemlinks as $itemid => $itemlink) {
                        $items[$modid][$itemtype][$itemid] = $itemlink;
                    }
                }
            }
        }
    }

    $timenow = time();   
    $hoursnow = xarLocaleFormatDate("%H",$timenow);
    $dateprev = '';

    // add title, url and truncate comments if requested
    $numcomments = count($comments);
    for ($i=0;$i<$numcomments;$i++) {   
        $modid = $comments[$i]['xar_modid'];
        $itemtype = $comments[$i]['xar_itemtype'];
        $itemid = $comments[$i]['xar_objectid'];
        if (!empty($args['addobject']) && !empty($items[$modid][$itemtype][$itemid])) {
            $comments[$i]['xar_title'] = $items[$modid][$itemtype][$itemid]['label'];
            $comments[$i]['objurl'] = $items[$modid][$itemtype][$itemid]['url'];
        }
        $comments[$i]['modname']=$modname[$modid][$itemtype];
        $comments[$i]['modview']=$modview[$modid][$itemtype];

        //$comments[$i]['returnurl'] = urlencode($modview[$modid][$itemtype]);
        $comments[$i]['returnurl'] = null;
        if ($args['truncate']) {
            if ( strlen($comments[$i]['xar_subject']) >$args['truncate']+3 )  {
                $comments[$i]['xar_subject']=substr($comments[$i]['xar_subject'],0,$args['truncate']).'...';
            }
            if ( !empty($comments[$i]['xar_title']) && strlen($comments[$i]['xar_title']) >$args['truncate']-3 ) {
                $comments[$i]['xar_title']=substr($comments[$i]['xar_title'],0,$args['truncate']).'...';
            }
        }
        if ($args['adddaysep']=='on') {
        // find out whether to change day separator        
            $msgunixtime=$comments[$i]['xar_datetime'];
            $msgdate=xarLocaleFormatDate("%b %d, %Y",$msgunixtime);
            $msgday=xarLocaleFormatDate("%A",$msgunixtime);          

            $comments[$i]['daychange']=0;

            $hoursdiff=($timenow - $msgunixtime)/3600;        
            if($hoursdiff<$hoursnow && $msgdate!=$dateprev) {
                $comments[$i]['daychange']=xarML('Today');
            }
            elseif($hoursdiff>=$hoursnow && $hoursdiff<$hoursnow+24 && ($msgdate!=$dateprev) ) {
                $comments[$i]['daychange']=xarML('Yesterday');
            }
            elseif($hoursdiff>=$hoursnow+24 && $hoursdiff<$hoursnow+48 && $msgdate!=$dateprev) {
                $comments[$i]['daychange']=xarML('Two days ago');
            }
            elseif ($hoursdiff>=$hoursnow+48 && $hoursdiff<$hoursnow+144 && $msgdate!=$dateprev) {
                $comments[$i]['daychange']=xarML("$msgday");
            }
            elseif ($hoursdiff>=$hoursnow+144 && $msgdate!=$dateprev) {
                $comments[$i]['daychange']=$msgdate;
            }
            $dateprev=$msgdate;
        }        
    }                       

    // prepare for output
    $templateargs['order']          =$args['order'];
    $templateargs['howmany']        =$args['howmany'];
    $templateargs['first']          =$args['first'];
    $templateargs['adddate']        =$args['adddate'];
    $templateargs['adddaysep']      =$args['adddaysep'];
    $templateargs['addauthor']      =$args['addauthor'];
    $templateargs['addmodule']      =$args['addmodule'];    
    $templateargs['addcomment']     =$args['addcomment'];
    $templateargs['addobject']      =$args['addobject'];
    $templateargs['addprevious']    =$args['addprevious'];
    $templateargs['modarray']       =$modarray;
    $templateargs['modid']          =$modarray;
    $templateargs['itemtype']       =$itemtype;
    $templateargs['modlist']        =$modlist;
    $templateargs['decoded_returnurl'] = rawurldecode(xarModURL('comments','user','displayall'));
    $templateargs['decoded_nexturl'] = xarModURL('comments','user','displayall',array(
                                                                            'first'=>$args['first']+$args['howmany'],
                                                                            'howmany'=>$args['howmany'],
                                                                            'modid'=>$modarray                                                              
                                                                            )
                                                            );
    $templateargs['commentlist']    =$comments;
    $templateargs['order']          =$settings['order'];
    
    if ($args['block_is_calling']==0 )   {         
        $output=xarTplModule('comments', 'user','displayall', $templateargs);    
    } else {            
        $templateargs['olderurl']=xarModURL('comments','user','displayall', 
                                            array(
                                                'first'=>   $args['first']+$args['howmany'],
                                                'howmany'=> $args['howmany'],
                                                'modid'=>$args['modid'] 
                                                )
                                            );
        $output=xarTplBlock('comments', 'latestcommentsblock', $templateargs );    
    }
    
    return $output;
}

?>
