<?php

/**
 * create item from xarModFunc('articles','admin','new')
 */
function articles_admin_create()
{
    // Get parameters
    if(!xarVarFetch('title',    'isset', $title,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('summary',  'isset', $summary,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('body',     'str',   $body,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('notes',    'isset', $notes,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('pubdate',  'isset', $pubdate,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('preview',  'isset', $preview,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cids',     'isset', $cids,      NULL, XARVAR_DONT_SET)) {return;}

    if (!xarVarFetch('ptid',     'notempty', $ptid))                                 {return;}
    if (!xarVarFetch('status',   'isset',    $status,   NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('language', 'isset',    $language, 'eng', XARVAR_NOT_REQUIRED)) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
    if (!isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type', 'admin', 'create',
                    'Articles');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if ($status === NULL) {
        if (empty($pubtypes[$ptid]['config']['status']['label'])) {
            $status = 2;
        } else {
            $status = 0;
        }
    }
// TODO: check local/user time
    if (isset($pubdate) && is_array($pubdate)) {
        if (!isset($pubdate['sec'])) {
            $pubdate['sec'] = 0;
        }
        $pubdate = mktime($pubdate['hour'],$pubdate['min'],$pubdate['sec'],
                          $pubdate['mon'],$pubdate['mday'],$pubdate['year']);
    } else {
//        $pubdate = '';
        $pubdate = time();
    }

    if (!isset($body) || !is_string($body)) {
        $body = '';
    }

    // Get relevant text
    // Note : $body_upload is no longer set in PHP 4.2.1+
    if (!empty($_FILES) && !empty($_FILES['body_upload']) && !empty($_FILES['body_upload']['tmp_name'])
        // is_uploaded_file() : PHP 4 >= 4.0.3
        && is_uploaded_file($_FILES['body_upload']['tmp_name']) && $_FILES['body_upload']['size'] > 0 && $_FILES['body_upload']['size'] < 1000000) {

        if (xarModIsHooked('uploads', 'articles', $ptid)) 
        {
            $magicLink = xarModAPIFunc('uploads',
                                       'user',
                                       'uploadmagic',
                                       array('uploadfile'=>'body_upload',
                                             'mod'=>'articles',
                                             'modid'=>0,
                                             'utype'=>'file'));
            
            $body .= ' '. $magicLink;
        } else {
            // this doesn't work on some configurations
            //$body = join('', @file($_FILES['body_upload']['tmp_name']));
            $tmpdir = xarCoreGetVarDirPath();
            $tmpdir .= '/cache/templates';
            $tmpfile = tempnam($tmpdir, 'art');
            if (move_uploaded_file($_FILES['body_upload']['tmp_name'], $tmpfile) && file_exists($tmpfile)) {
                $body = join('', file($tmpfile));
                unlink($tmpfile);
            }
        }
    }

// TEST: grab the title from the webpage
$isfile = '';
    foreach ($pubtypes[$ptid]['config'] as $field => $value) {
if ($value['format'] == 'webpage') {
$isfile = $field;
} elseif ($value['format'] == 'calendar' && isset($$field) && is_array($$field)) {
    $var = $$field;
    if (!isset($var['sec'])) {
        $var['sec'] = 0;
    }
    $$field = mktime($var['hour'],$var['min'],$var['sec'],
                     $var['mon'],$var['mday'],$var['year']);
} elseif ($value['format'] == 'url' && isset($$field) && $$field == 'http://') {
    $$field = '';
} elseif ($value['format'] == 'image' && isset($$field) && $$field == 'http://') {
    $$field = '';
}
        if (!isset($$field)) {
            $$field = '';
        }
    }

    if (!empty($cids) && count($cids) > 0) {
        $cids = array_values(preg_grep('/\d+/',$cids));
    } else {
        $cids = array();
    }

// TEST: grab the title from the webpage
if (empty($title) && !empty($isfile)) {
    $basedir = '/home/mikespub/www/pictures';
    $curfile = $basedir . '/' . $$isfile;
    if (file_exists($curfile) && is_file($curfile)) {
        $fd = fopen($curfile,'r');
        if (!empty($fd)) {
            $head = fread($fd, 4096);
            fclose($fd);
            if (preg_match('#<title>(.*?)</title>#is',$head,$matches)) {
                $title = $matches[1];
            }
        }
    }
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

// TODO: make $status dependent on permissions

    $authorid = xarUserGetVar('uid');
    if (empty($authorid)) {
        $authorid = 1;
    }

    // Fill in the new values
    $article = array('title' => $title,
                     'summary' => $summary,
                     'body' => $body,
                     'notes' => $notes,
                     'pubdate' => $pubdate,
                     'status' => $status,
                     'ptid' => $ptid,
                     'cids' => $cids,
                  // for preview
                     'pubtypeid' => $ptid,
                     'authorid' => $authorid,
                     'aid' => 0);
    if ($preview) {
        $data = xarModFunc('articles','admin','new',
                             array('preview' => true, 'article' => $article));
        unset($article);
        if (is_array($data)) {
            return xarTplModule('articles','admin','new',$data);
        } else {
            return $data;
        }
    }

    // Pass to API
    $aid = xarModAPIFunc('articles', 'admin', 'create', $article);

    if ($aid == false) {
        // Throw back any system exceptions (e.g. database failure)
        if (xarExceptionMajor() == XAR_SYSTEM_EXCEPTION) {
            return; // throw back
        }
        // Handle the user exceptions yourself
        $status = xarML('Creating article failed');
        // Get the information about the exception (in HTML or string format)
        // $reason = xarExceptionValueHTML();
        $reason = xarExceptionValue();
        if (!empty($reason)) {
            $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
        }
        // Free the exception to tell Xaraya that you handled it
        xarExceptionFree();
        return $status;
    }

    // Success
    xarSessionSetVar('statusmsg', xarML('Article Created'));

    // if we can edit articles, go to admin view, otherwise go to user view
    if (xarSecurityCheck('EditArticles',0,'Article',$ptid.':All:All:All')) {
        xarResponseRedirect(xarModURL('articles', 'admin', 'view',
                                      array('ptid' => $ptid)));
    } else {
        xarResponseRedirect(xarModURL('articles', 'user', 'view',
                                      array('ptid' => $ptid)));
    }

    return true;
}

?>
