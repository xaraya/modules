<?php
/**
 * Yea or Nay on the trackback
 *
 * @author   John Cox
 * @access   public
 * @returns  array      returns whatever needs to be parsed by the BlockLayout engine
 */
function trackback_userapi_receive($args) 
{
    extract($args);
    // Security Check

    if (!empty($error)){
          $output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
          $output .= "<response>\n";
          $output .= "<error>1</error>\n";
          $output .= "<message>$error[errordata]</message>\n";
          $output .= "</response>\n";
          echo $output;
          return true;
    }

    if (!xarSecurityCheck('Viewtrackback')) return;

    // explode the id
    list($module, $itemtype, $objectid) = explode(",", $id);

    if (!empty($title)){
        $subject        = xarML('Trackback') . ' :: ' . $title;
    } else {
        $subject        = xarML('Trackback') . ' :: ' . $url;
    }
    if (empty($blogname)) {
        $comment        = '<strong>' . xarML('TrackBack from') . ' <a href="' . $url . '">' . $url . '</a></strong>';
        if (!empty($excerpt)){
            $comment    .= '<br /><br />';
            $comment    .= $excerpt;
        }
    } else {
        $comment        = '<strong>' . xarML('TrackBack from') . ' <a href="' . $url . '">' . $blogname . '</a></strong>';
        if (!empty($excerpt)){
            $comment    .= '<br /><br />';
            $comment    .= $excerpt;
        }
    }

    $module     = xarVarPrepForDisplay($module);
    $itemtype   = xarVarPrepForDisplay($itemtype);
    $objectid   = xarVarPrepForDisplay($objectid);
    $subject    = xarVarPrepForDisplay($subject);
    $comment    = xarVarPrepHTMLDisplay($comment);

    $addtrackback = xarModAPIFunc('comments','user','add',
                                array('modid'    => $module,
                                      'itemtype' => $itemtype,
                                      'objectid' => $objectid,
                                      'comment'  => $comment,
                                      'title'    => $subject,
                                      'author'   => 2));

    $noerror = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
    $noerror .= "<response>\n";
    $noerror .= "<error>0</error>\n";
    $noerror .= "</response>\n";
    echo $noerror;

    return true;
}
?>