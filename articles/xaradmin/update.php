<?php

/**
 * update item from articles_admin_modify
 */
function articles_admin_update()
{
    // Get parameters
    if(!xarVarFetch('aid',      'isset', $aid,       NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('title',    'isset', $title,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('summary',  'isset', $summary,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('bodyfile', 'isset', $bodyfile,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('bodytext', 'isset', $bodytext,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('notes',    'isset', $notes,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('pubdate',  'isset', $pubdate,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('status',   'isset', $status,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cids',     'isset', $cids,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('ptid',     'isset', $ptid,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('language', 'isset', $language,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('preview',  'isset', $preview,   NULL, XARVAR_DONT_SET)) {return;}


    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if (empty($aid) || !is_numeric($aid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item id', 'admin', 'update', 'Articles');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
    if (empty($ptid) || !isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'publication type', 'admin', 'update', 'Articles');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Get original article information
    $article = xarModAPIFunc('articles',
                            'user',
                            'get',
                            array('aid' => $aid));

// TODO: check local/user time
    if (isset($pubdate) && is_array($pubdate)) {
        if (!isset($pubdate['sec'])) {
            $pubdate['sec'] = 0;
        }
        $pubdate = mktime($pubdate['hour'],$pubdate['min'],$pubdate['sec'],
                          $pubdate['mon'],$pubdate['mday'],$pubdate['year']);
    } else {
        $pubdate = '';
    }

    if (!isset($bodytext) || !is_string($bodytext)) {
        $bodytext = '';
    }

    // Get relevant text
    // Note : $bodyfile is no longer set in PHP 4.2.1+
    if (!empty($_FILES) && !empty($_FILES['bodyfile']) && !empty($_FILES['bodyfile']['tmp_name'])
        // is_uploaded_file() : PHP 4 >= 4.0.3
        && is_uploaded_file($_FILES['bodyfile']['tmp_name']) && $_FILES['bodyfile']['size'] > 0 && $_FILES['bodyfile']['size'] < 1000000) {
        
        if (xarModIsHooked('uploads')) 
        {
            
            $magicLink = xarModAPIFunc('uploads',
                                       'user',
                                       'uploadmagic',
    		  						   array('uploadfile'=>'bodyfile',
    									     'mod'=>'articles',
    										 'modid'=>0,
    										 'utype'=>'file'));
            
            $body = $bodytext .' '. $magicLink;
            
        } else {
            $body = join('', @file($_FILES['bodyfile']['tmp_name']));
        }
    } else {
        $body = $bodytext;
    }

    if (!isset($status)) {
        if (empty($pubtypes[$ptid]['config']['status']['label'])) {
            $status = 2;
        } else {
            $status = 0;
        }
    }
    foreach ($pubtypes[$ptid]['config'] as $field => $value) {
        if ($value['format'] == 'calendar' && isset($$field) && is_array($$field)) {
            $var = $$field;
            if (!isset($var['sec'])) {
                $var['sec'] = 0;
            }
            $$field = mktime($var['hour'],$var['min'],$var['sec'],
                             $var['mon'],$var['mday'],$var['year']);
        }
        if (!isset($$field)) {
            $$field = '';
        }
    }
    if (!isset($language)) {
        $language = 'eng';
    }
    if (!empty($cids) && count($cids) > 0) {
        $cids = array_values(preg_grep('/\d+/',$cids));
    } else {
        $cids = array();
    }

    // check that we have a title when we need one, or fill in a dummy one
    if (empty($title)) {
        if (empty($pubtypes[$ptid]['config']['title']['label'])) {
            $title = ' ';
        } else {
            $title = xarML('This field is required');
            // show this to the user
            $preview = 1;
        }
    }

    // fill in the new values
    $article['title'] = $title;
    $article['summary'] = $summary;
    $article['body'] = $body;
    $article['notes'] = $notes;
    $article['pubdate'] = $pubdate;
    $article['status'] = $status;
    $article['ptid'] = $ptid;
    $article['cids'] = $cids;
// really ?
    $article['pubtypeid'] = $ptid;
    $article['language'] = $language;

    if ($preview) {
        $data = xarModFunc('articles','admin','modify',
                             array('preview' => true, 'article' => $article));
        unset($article);
        if (is_array($data)) {
            return xarTplModule('articles','admin','modify',$data);
        } else {
            return $data;
        }
    }

    // Pass to API
    if (!xarModAPIFunc('articles', 'admin', 'update', $article)) {
        return;
    }
    unset($article);

    // Success
    xarSessionSetVar('statusmsg', xarML('Article Updated'));

    xarResponseRedirect(xarModURL('articles', 'admin', 'view',
                                  array('ptid' => $ptid)));

    return true;
}

?>
