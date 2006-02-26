<?php
function dailydelicious_admin_get($args)
{
    if(!xarSecurityCheck('DailyDelicious')) return;
    extract($args);
    $GLOBALS['content'] = '';
    $GLOBALS['empty'] = true;
    $dt             = date("Y-m-d");
    $del_user       = xarModGetVar('dailydelicious', 'username');
    $del_password   = xarModGetVar('dailydelicious', 'password');

    // Loop over the last 7 days gathering up the del.ico.us entries
 
    for ($i = 0; $i < 7; $i++) {
        $somedate   = mktime(0, 0, 0, date("m"),  date("d")-$i,  date("Y"));
        $dt         = date("Y-m-d", $somedate);
        $fp         = fopen("http://$del_user:$del_password@del.icio.us/api/posts/get?dt=$dt", "r");

        sleep(1); 			// del.ici.ous API asks not to hammer them too fast
  
        $xml_parser = xml_parser_create();
        xml_set_element_handler($xml_parser, "startElement", "endElement");
  
        while ($data = fread($fp, 4096)) {
            if (!xml_parse($xml_parser, $data, feof($fp))) {
                die(sprintf("XML error: %s at line %d",
		        xml_error_string(xml_get_error_code($xml_parser)),
		        xml_get_current_line_number($xml_parser)));
            }
        }
        xml_parser_free($xml_parser);
    }
    
    $GLOBALS['content'] = $GLOBALS['content'] . "</ul>";
    // Return if no entries
    if ($GLOBALS['empty']) return;

    $importpubtype = xarModGetVar('dailydelicious','importpubtype');
    if (empty($importpubtype)) {
        $importpubtype = xarModGetVar('articles','defaultpubtype');
        if (empty($importpubtype)) {
            $importpubtype = 1;
        }
        xarModSetVar('dailydelicious','importpubtype',1);
    }
    $subject            = xarModGetVar('dailydelicious', 'title');
    $defaultstatus      = xarModGetVar('dailydelicious', 'defaultstatus');
    if (empty($defaultstatus)){
        $defaultstatus = 0;
    }
    $article['title']   = $subject;
    $article['summary'] = $GLOBALS['content'];
    $article['aid']     = 0;
    $article['ptid']    = $importpubtype;
    $article['status']  = $defaultstatus;
    xarModAPIFunc('articles', 'admin', 'create', $article);
    xarResponseRedirect(xarModURL('articles', 'admin', 'view'));
    return;
}

function startElement($parser, $name, $attrs) 
{
    if ($name == "POSTS") {
        if (strlen($GLOBALS['content']) == 0) { 

              $GLOBALS['content'] = $GLOBALS['content'] . "Shared bookmarks for <a href=\"http://del.icio.us/\">del.icio.us</a> user " . "<a href=\"http://del.icio.us/" . $attrs["USER"]
            . "\"> " . $attrs["USER"] . "</a><ul>\n";
        }
    } else {
        $GLOBALS['empty'] = false;

        $GLOBALS['content'] = $GLOBALS['content'] . '    <li><a href="'
          . htmlspecialchars($attrs['HREF'])	// this is the URL of the bookmark
          . '" title="'
          . htmlspecialchars($attrs['HREF']) // The title you may have given it
          . '">' 
          . htmlspecialchars($attrs['DESCRIPTION'])  #
          . "</a> -- \n"
          . htmlspecialchars(isset($attrs['EXTENDED']))
          . ' Tagged as: [' . htmlspecialchars($attrs['TAG']) . "]</li>\n";
    }
}

function endElement($parser, $name)
{
    return;
}
?>