<?php

function ievents_user_test($args)
{
    extract($args);

    $external_source = 'INTEGRA';

    // Must set the 'update' paramater to 'true' to do an actual update, otherwise it is just in text mode.
    xarVarFetch('update', 'enum:true:false', $do_update, 'false', XARVAR_NOT_REQUIRED);

    // Get the remote events as an array.
    $events = ievents_user_test_get_events();

    if (empty($events)) return "NO EVENTS FOUND";

    foreach($events as $event) {
        echo "<hr />Processing event " . $event['code'] . ': ' . $event['title'] . "<br />\n";

        // See if there is an existing event that matches.
        $stored_event = xarModAPIfunc('ievents', 'user', 'getevent',
            array('external_source' => $external_source, 'external_ref' => $event['code'])
        );

        if (empty($stored_event)) {
            echo "Event is new<br />\n";

            $categories = array();
            $calendar = 0;
            $flags = array();

            // Work out which region the event belongs to.
            // These are prefixes mapped to category IDs.
            $regions_map = array(
                'SE' => array(84, 'SE - South East'),
                'YH' => array(93, 'YH - Yorkshire and Humber'),
                'NI' => array(78, 'NI - Northern Ireland'),
                'WA' => array(91, 'WA - Wales'),
                'MID' => array(70, 'MID - Midlands'),
                'MI' => array(70, 'MI - Midlands'),
                'M' => array(70, 'M - Midlands'),
                'SW' => array(87, 'SW - South West'),
                'NE' => array(74, 'NE - North East'),
                'NW' => array(77, 'NW - North West'),
                'EE' => array(300, 'EE - East of England'),
                'NS' => array(297, 'NS - North Scotland'),
                'SN' => array(297, 'SN - North Scotland'),
                'CS' => array(81, 'CS - Central Scotland'),
                'SC' => array(81, 'SC - Central Scotland'),
                'WS' => array(80, 'WS - West Scotland'),
                'ROI' => array(88, 'ROI - Republic of Ireland'),
                'RI' => array(88, 'RI - Republic of Ireland'),
                'IRE' => array(88, 'IRE - Republic of Ireland'),
            );

            // TODO: other prefixed: ANCONF ANCON EIA SILCAF SILCTD INVALID

            foreach($regions_map as $region_prefix => $region_cat) {
                if (preg_match("/^${region_prefix}[0-9]/", $event['code'])) {
                    $categories[] = $region_cat[0];
                    echo "Region: " . $region_cat[1] . "<br />\n";
                    break;
                }
            }

            // TODO: A suffix of 'A' or 'F' indicates an associate or full member workshop
            // but only if 'workshop' appears in the title (since 'A' could also be a suffix
            // where more than one event happens in the same month.
            // TODO: what about open book assessments (rather than just workshops)?
            // This also happens to duplicate the categories set by the Integra category,
            // so this may not actually be needed.
            if (preg_match('/workshop/i', $event['title'])) {
                if (preg_match('/A$/', $event['code'])) $categories[] = 374;
                if (preg_match('/F$/', $event['code'])) $categories[] = 371;
            }

            // TODO: other suffixes 'S' (SiLC training)

            // Look at the Integra category to determine the calendar.
            // They will map into a mixture of categories and calendars.
            // Array is: (calendar, category, description) where 0 = N/A
            // Calendars:
            $calendar_list = array(
                1 => 'regional events',
                2 => 'conferences',
                3 => 'training',
                4 => 'external events',
            );

            $category_mapping = array(
                'AWORK' => array(3, 374, 'Ass Work - Associate Membership Workshop'),
                'CONF' => array(2, 0, 'Conf - Conference'),
                'EXT EV' => array(4, 0, 'ExtEv - External Event'),
                'FORUM' => array(2, 0, 'Forum - Forum'),
                'FWORK' => array(3, 371, 'FWork - Full Membership Workshop'),
                'PEA' => array(3, 0, 'PEA - PEA Examiner Training'),
                'REGEV' => array(1, 0, 'RegEv - Regional Event'),
                'SILC' => array(3, 0, 'SiLCT - SiLC Training'),
                'CEAM' => array(1, 0, 'CEAM - CEAM Event'),
                'WRK' => array(3, 0, 'Wrk - Workshop'),
                'LAUN' => array(2, 0, 'Launch - Launch'),
            );

            foreach($category_mapping as $cat_code => $cat_map) {
                if ($event['category']['code'] == $cat_code) {
                    if (!empty($cat_map[0])) {
                        $calendar = $cat_map[0];
                        echo "Calendar $calendar: " . $calendar_list[$calendar] . "<br />";
                    }
                    if (!empty($cat_map[1])) {
                        $categories[] = $cat_map[1];
                    }
                }
            }

            // TODO: sort out the flags.
            // F,Featured;V,Changed venue;T,Rescheduled;N,Places full;C,Cancelled;P,Provisional;L,Details locked
            if (!empty($event['soldout'])) {
                $flags[] = 'N';
                echo "No more places (full)<br />\n";
            }
            // Going out on a limb with this one.
            if (preg_match('/cancelled/i', $event['title'])) {
                $flags[] = 'C';
                echo "Cancelled<br />\n";
            }

            // Now attempt to create the event.
            $event_record = array(
                'title' => $event['title'],
                'summary' => $event['summary'],
                'description' => $event['detail'],
                'all_day' => 'A',
                'startdate' => $event['startdate'],
                'enddate' => $event['enddate'],
                'location_venue' => isset($event['venue']) ? $event['venue'] : '',
                'contact_email' => $event['email'],
                'url' => $event['url'],
                'cid' => $calendar,
                'external_source' => $external_source,
                'external_ref' => $event['code'],
                'status' => 'ACTIVE',
                'flags' => $flags,
                'catids' => $categories,
            );

            if ($do_update == 'true') {
                //$eid = xarModAPIfunc('ievents', 'admin', 'modify', $event_record);
                if (empty($eid)) echo "Failed to create event<br />\n";
            } else {
                echo "Not creating event (testing only)<br />\n";
            }

        } else {
            echo "Event already exists<br />\n";
            //$stored_event

            // Start with the assumption that no update is necessary
            $update_flag = false;
            $flags = array_keys($stored_event['flags_arr']);
            if (in_array('L', $flags)) {
                echo "Skipping - event is locked<br />\n";
                continue;
            }

            // Event is now full.
            if (!in_array('N', $flags) && $event['soldout']) {
                $update_flag = true;
                $flags[] = 'N';
                echo "Event is now full<br />\n";
            }

            // Event has been cancelled.
            if (!in_array('C', $flags) && preg_match('/cancelled/i', $event['title'])) {
                $update_flag = true;
                $flags[] = 'C';
                echo "Event has been cancelled<br />\n";
            }

            // Dates have changed.
            if ($stored_event['startdate'] != $event['startdate'] || $stored_event['enddate'] != $event['enddate']) {
                $update_flag = true;
                $flags[] = 'T';
                $stored_event['startdate'] = $event['startdate'];
                $stored_event['enddate'] = $event['enddate'];
                echo "Dates have changed<br />\n";
            }

            if ($stored_event['location_venue'] != (isset($event['venue']) ? $event['venue'] : '')) {
                $update_flag = true;
                $flags[] = 'V';
                $stored_event['location_venue'] = (isset($event['venue']) ? $event['venue'] : '');
                echo "Venue has changed<br />\n";
            }

            // All the flags changes have been done.
            $stored_event['flags'] = $flags;

            // Title has changed
            if ($stored_event['title'] != $event['title']) {
                $update_flag = true;
                $stored_event['title'] = $event['title'];
                echo "Title has changed<br />\n";
            }

            // Description has changed
            if ($stored_event['description'] != $event['detail']) {
                $update_flag = true;
                $stored_event['description'] = $event['detail'];
                echo "Description has changed<br />\n";
            }

            // URL has changed
            if ($stored_event['url'] != $event['url']) {
                $update_flag = true;
                $stored_event['url'] = $event['url'];
                echo "URL has changed<br />\n";
            }

            if ($stored_event['contact_email'] != $event['email']) {
                $update_flag = true;
                $stored_event['contact_email'] = $event['email'];
                echo "Contact email has changed<br />\n";
            }

            if ($do_update == 'true') {
                $eid = xarModAPIfunc('ievents', 'admin', 'modify', $stored_event);
                if (empty($eid)) echo "Failed to update event<br />\n";
            } else {
                echo "Not creating event (testing only)<br />\n";
            }
        }

        //break;
    }

    return "DONE";
}

function ievents_user_test_get_events()
{
    $events = array();

    require('modules/base/xarclass/xmlParser.php');

    $curl = xarModAPIfunc('base', 'user', 'newcurl');
    $curl->seturl('http://members.iema.net/membersarea/custom/getevents1.asp');
    $text = $curl->exec();

    $parser = new XMLParser();
    $parser->SetXmldata($text);
    $parser->buildXmlTree();
    $xml = $parser->getXmlTree();

    // Remove root
    $xml = reset($xml);

    // Top tag should be 'events'
    if (preg_match('/:EVENTS$/', $xml['tag'])) {
        $xml = $xml['children'];

        // Loop for each event
        foreach($xml as $event_xml) {
            $event = array();
            // Extract the event into an array
            // Top tag for each event should be 'event'
            if (preg_match('/:EVENT$/', $event_xml['tag'])) {
                $event_xml = $event_xml['children'];

                // Loop over each tag in the event.
                foreach($event_xml as $event_xml_tag) {
                    switch (preg_replace('/.+:/', '', $event_xml_tag['tag'])) {
                        case 'ID':
                            $event['id'] = (int)implode('', $event_xml_tag['children']);
                            break;
                        case 'CODE':
                            // TODO: extract the region from the code, if available.
                            $event['code'] = implode('', $event_xml_tag['children']);
                            break;
                        case 'NAME':
                            $event['title'] = implode('', $event_xml_tag['children']);
                            break;
                        case 'START':
                            $event['startdate'] = strtotime(implode('', $event_xml_tag['children']));
                            break;
                        case 'END':
                            $event['enddate'] = strtotime(implode('', $event_xml_tag['children']));
                            break;
                        case 'EMAIL':
                            $event['email'] = implode('', $event_xml_tag['children']);
                            break;
                        case 'SUMMARY':
                            $event['summary'] = isset($event_xml_tag['children']) ? implode('', $event_xml_tag['children']) : '';
                            break;
                        case 'DETAIL':
                            $event['detail'] = implode('', $event_xml_tag['children']);
                            break;
                        case 'CATEGORY':
                            // TODO: parse child elements.
                            $category = array();
                            foreach($event_xml_tag['children'] as $cat_child) {
                                switch(preg_replace('/.+:/', '', $cat_child['tag'])) {
                                    case 'CODE':
                                        $category['code'] = implode('', $cat_child['children']);
                                        break;
                                    case 'SHORT':
                                        $category['short'] = implode('', $cat_child['children']);
                                        break;
                                    case 'LONG':
                                        $category['long'] = implode('', $cat_child['children']);
                                        break;
                                    default:
                                        break;
                                }
                            }
                            $event['category'] = $category;
                            break;
                        case 'MENU':
                            // TODO: parse child elements.
                            $menu = array();
                            foreach($event_xml_tag['children'] as $menu_child) {
                                switch(preg_replace('/.+:/', '', $menu_child['tag'])) {
                                    case 'ID':
                                        $menu['id'] = isset($menu_child['children']) ? implode('', $menu_child['children']) : '';
                                        break;
                                    case 'TITLE':
                                        $menu['title'] = isset($menu_child['children']) ? implode('', $menu_child['children']) : '';
                                        break;
                                    default:
                                        break;
                                }
                            }
                            $event['menu'] = $menu;
                            break;
                        case 'BOOKABLE':
                            if (!empty($event_xml_tag['children']) && is_array($event_xml_tag['children'])) {
                                $tag_value = implode('', $event_xml_tag['children']);
                                $event['bookable'] = (!empty($tag_value) && $tag_value == 'Y') ? true : false;
                            } else {
                                $event['bookable'] = false;
                            }
                            break;
                        case 'SOLDOUT':
                            if (!empty($event_xml_tag['children']) && is_array($event_xml_tag['children'])) {
                                $tag_value = implode('', $event_xml_tag['children']);
                                $event['soldout'] = (!empty($tag_value) && ($tag_value == 'Y' || $tag_value == 'True')) ? true : false;
                            } else {
                                $event['soldout'] = false;
                            }
                            break;
                        case 'URL':
                            $event['url'] = implode('', $event_xml_tag['children']);
                            break;
                        default:
                            //echo " tag=$tag_name ";
                            break;
                    }
                }
                //echo "<pre>"; var_dump($event); echo "</pre>";
                if (!empty($event)) $events[] = $event;
            }
            //echo "<pre>"; var_dump($event_xml); echo "</pre>";
        }
    }

    //echo "<pre>"; var_dump($xml); echo "</pre>";

    //$parser = new xarXmlParser();
    //$parser->parseFile('http://members.iema.net/membersarea/custom/getevents1.asp');

    return $events;
}

?>