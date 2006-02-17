<?php
/**
 * Surveys table definitions function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Update the internal lists, based on the imported EP data.
 *
 * Very specific function, not needed in module probably
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return int  type and name returned                       [OPTIONAL A REQURIED]
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Nov 2005? To be determined.
 */

require_once "modules/surveys/convert/ConvertCharset.class.php";

function surveys_admin_updatelists() {

    // Get a character conversion object.
    $encoding_object = new ConvertCharset;
    //$NewFileOutput = $encoding_object->Convert($FileText, $FromCharset, $ToCharset, $Entities);
    $FromCharset = 'windows-1252'; //'iso-8859-1'; // 'windows-1251';
    $ToCharset = 'utf-8';

    // Update the indicator lists.
    // Each list takes the name {attribute}-{NACE code}, e.g. EP2-14.02b
    // Attributes are:
    // - desc[_lang]: the indicator text
    // - code: the indicator ID
    // - attribute: the EP attribute code
    // - nace: the NACE code

    if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
        // No privilege for editing survey structures.
        return false;
    }

    // Extend the execution time to allow for extended execution.
    set_time_limit(60*20);

    // Get the type of list to be updated: ep or ema
    xarVarFetch('listtype', 'pre:lower:trim:enum:ema:ep:sectors', $p_listtype, NULL, XARVAR_NOT_REQUIRED);

    // Database stuff.
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    echo "updating $p_listtype <br/>";

    // Sectors and sub-sectors
    if ($p_listtype == 'sectors') {
        // Get the list type
        $listtypes = xarModAPIfunc(
            'lists', 'user', 'getlisttypes',
            array('type_name' => 'sectors')
        );

        if (!empty($listtypes)) {
            $listtype = reset($listtypes);
            $ltid = $listtype['tid'];
        } else {
            echo "*** list type 'sectors' not found <br/>";
            return;
        }

        // **** SECTORS ****
        echo "**** SECTORS<br/>";

        // Get existing list (sectors)
        $list_details = xarModAPIfunc(
            'lists', 'user', 'getlists',
            array('list_name' => 'sectors')
        );
        if (empty($list_details)) {
            echo 'List "sectors" not found';
            return;
        }
        $list_details = reset($list_details);
        $lid = $list_details['lid'];

        $listitems = xarModAPIfunc(
            'lists', 'user', 'getlistitems',
            array('lid' => $lid, 'items_only' => true, 'dd_flag' => true, 'itemkey' => 'code')
        );
        echo " existing size of list=" . count($listitems) . "<br/>";

        // Get the sectors.
        $query = 'SELECT sectors.id, sectors.short_name,' // sectors.long_name
            . ' sectors.short_name_fr, sectors.short_name_de, sectors.short_name_es, sectors.short_name_it'
            . ' FROM remas_ep_sectors AS sectors';
        $result = $dbconn->execute($query);
        if (!$result) {return;}

        while (!$result->EOF) {
            list($id, $short_name, $short_name_fr, $short_name_de, $short_name_es, $short_name_it) = $result->fields;

            if (!empty($FromCharset)) {
                if (!empty($short_name)) $short_name = $encoding_object->Convert($short_name, $FromCharset, $ToCharset, 0);
                if (!empty($short_name_fr)) $short_name_fr = $encoding_object->Convert($short_name_fr, $FromCharset, $ToCharset, 0);
                if (!empty($short_name_de)) $short_name_de = $encoding_object->Convert($short_name_de, $FromCharset, $ToCharset, 0);
                if (!empty($short_name_es)) $short_name_es = $encoding_object->Convert($short_name_es, $FromCharset, $ToCharset, 0);
                if (!empty($short_name_it)) $short_name_it = $encoding_object->Convert($short_name_it, $FromCharset, $ToCharset, 0);
                //echo "french=$text_fr <br/>"; $result->MoveNext(); continue;
            }

            if (!isset($listitems[$id])) {
                echo " notset $short_name <br/>";
                // Add the item to the list.
                $iid = xarModAPIfunc(
                    'lists', 'admin', 'createlistitem',
                    array(
                        'lid' => $lid,
                        'item_code' => $id,
                        'item_long_name' => $short_name,
                        'item_short_name' => $id,
                        'item_order' => '',
                        'item_desc' => '',
                        'dd' => array(
                            'item_long_name_fr' => isset($short_name_fr) ? $short_name_fr : '',
                            'item_long_name_de' => isset($short_name_de) ? $short_name_de : '',
                            'item_long_name_es' => isset($short_name_es) ? $short_name_es : '',
                            'item_long_name_it' => isset($short_name_it) ? $short_name_it : ''
                        )
                    )
                );
            } else {
                echo " updating $short_name <br/>";
                // Update the existing item.
                $res = xarModAPIfunc(
                    'lists', 'admin', 'updatelistitem',
                    array(
                        'iid' => $listitems[$id]['iid'],
                        'item_long_name' => $short_name,
                        'item_short_name' => $id,
                        'item_order' => '',
                        'item_desc' => '',
                        'dd' => array(
                            'item_long_name_fr' => isset($short_name_fr) ? $short_name_fr : '',
                            'item_long_name_de' => isset($short_name_de) ? $short_name_de : '',
                            'item_long_name_es' => isset($short_name_es) ? $short_name_es : '',
                            'item_long_name_it' => isset($short_name_it) ? $short_name_it : ''
                        )
                    )
                );
            }

            $result->MoveNext();
        }


        // **** SUB-SECTORS ****
        echo "**** SUB-SECTORS<br/>";

        // Get existing list (sectors)
        $list_details = xarModAPIfunc(
            'lists', 'user', 'getlists',
            array('list_name' => 'subsectors')
        );
        if (empty($list_details)) {
            echo 'List "subsectors" not found';
            return;
        }
        $list_details = reset($list_details);
        $lid = $list_details['lid'];

        $listitems = xarModAPIfunc(
            'lists', 'user', 'getlistitems',
            array('lid' => $lid, 'items_only' => true, 'dd_flag' => true, 'itemkey' => 'code')
        );
        echo " existing size of list=" . count($listitems) . "<br/>";

        // Get the sectors.
        $query = 'SELECT subsectors.id, subsectors.name, subsectors.nace, subsectors.sector_id,'
            . ' subsectors.name_fr, subsectors.name_de, subsectors.name_es, subsectors.name_it'
            . ' FROM remas_ep_subsectors AS subsectors';
        $result = $dbconn->execute($query);
        if (!$result) {return;}

        while (!$result->EOF) {
            list($id, $name, $nace, $sector_id, $name_fr, $name_de, $name_es, $name_it) = $result->fields;

            if (!empty($FromCharset)) {
                if (!empty($name)) $name = $encoding_object->Convert($name, $FromCharset, $ToCharset, 0);
                if (!empty($name_fr)) $name_fr = $encoding_object->Convert($name_fr, $FromCharset, $ToCharset, 0);
                if (!empty($name_de)) $name_de = $encoding_object->Convert($name_de, $FromCharset, $ToCharset, 0);
                if (!empty($name_es)) $name_es = $encoding_object->Convert($name_es, $FromCharset, $ToCharset, 0);
                if (!empty($name_it)) $name_it = $encoding_object->Convert($name_it, $FromCharset, $ToCharset, 0);
                //echo "french=$text_fr <br/>"; $result->MoveNext(); continue;
            }

            if (!isset($listitems[$id])) {
                echo " notset $name <br/>";
                // Add the item to the list.
                $iid = xarModAPIfunc(
                    'lists', 'admin', 'createlistitem',
                    array(
                        'lid' => $lid,
                        'item_code' => $nace,
                        'item_long_name' => $name,
                        'item_short_name' => $sector_id,
                        'item_order' => '',
                        'item_desc' => '',
                        'dd' => array(
                            'item_long_name_fr' => isset($name_fr) ? $name_fr : '',
                            'item_long_name_de' => isset($name_de) ? $name_de : '',
                            'item_long_name_es' => isset($name_es) ? $name_es : '',
                            'item_long_name_it' => isset($name_it) ? $name_it : ''
                        )
                    )
                );
            } else {
                echo " updating $name <br/>";
                // Update the existing item.
                $res = xarModAPIfunc(
                    'lists', 'admin', 'updatelistitem',
                    array(
                        'iid' => $listitems[$id]['iid'],
                        'item_long_name' => $name,
                        'item_short_name' => $sector_id,
                        'item_order' => '',
                        'item_desc' => '',
                        'dd' => array(
                            'item_long_name_fr' => isset($name_fr) ? $name_fr : '',
                            'item_long_name_de' => isset($name_de) ? $name_de : '',
                            'item_long_name_es' => isset($name_es) ? $name_es : '',
                            'item_long_name_it' => isset($name_it) ? $name_it : ''
                        )
                    )
                );
            }

            $result->MoveNext();
        }
    }

    // EP type lists
    if ($p_listtype == 'ep') {
        xarVarFetch('numitems', 'int', $numitems, 10000, XARVAR_NOT_REQUIRED);
        xarVarFetch('startitem', 'int', $startitem, 0, XARVAR_NOT_REQUIRED);

        xarLogMessage("Start EP");

        // Get each unique list name (i.e. unique combinations of attribute/sub-sector.
        $query = 'SELECT DISTINCT subsector_attributes.id, attributes.code, subsectors.nace'
            . ' FROM remas_ep_subsectors AS subsectors'

            . ' INNER JOIN remas_ep_subsector_indicators AS subsector_indicators'
            . ' ON subsector_indicators.subsector_id = subsectors.id'

            . ' INNER JOIN remas_ep_indicators AS indicators'
            . ' ON indicators.id = subsector_indicators.indicator_id'

            . ' INNER JOIN remas_ep_attributes AS attributes'
            . ' ON attributes.id = indicators.attribute_id'

            . ' INNER JOIN remas_ep_subsector_attributes AS subsector_attributes'
            . ' ON subsector_attributes.subsector_id = subsectors.id'
            . ' AND subsector_attributes.attribute_id = attributes.id'
            . ' AND subsector_attributes.validation IN (\'MANDATORY\', \'OPTIONAL\')'
            . ' AND subsector_attributes.imported = \'N\''

            //. ' AND subsector_attributes.subsector_id = 8' // TEMPORARY: Pulp
            ;

        $result = $dbconn->SelectLimit($query, $numitems, $startitem);
        if (!$result) {return;}

        $lists = array();
        //$count = 0;
        while (!$result->EOF) {
            list($subsector_attributes_id, $attribute, $nace) = $result->fields;
            //$count += 1;
            //echo "<div>$count: $attribute $nace</div>";
            $lists[] = array(
                'subsector_attributes_id'=>$subsector_attributes_id,
                'attribute'=>$attribute,
                'nace'=>$nace,
                'name'=>$attribute . '-' . $nace
            );
            $result->MoveNext();
        }
        xarLogMessage("Got EP lists");
        echo "Number of lists: " . count($lists) . "<br/>";

        $listtypes = xarModAPIfunc(
            'lists', 'user', 'getlisttypes',
            array('type_name' => 'subsector_indicators')
        );

        if (!empty($listtypes)) {
            $listtype = reset($listtypes);
            $ltid = $listtype['tid'];
        } else {
            echo "*** list type 'subsector_indicators' not found <br/>";
            return;
        }

        // Loop for each list, and create it if it does not exist.
        foreach($lists as $list) {
            xarLogMessage("Processing list '$list[name]'");
            echo "Processing list '$list[name]' <br/>";

            // Get existing list if it exists.
            $list_details = xarModAPIfunc(
                'lists', 'user', 'getlists',
                array('list_name' => $list['name'])
            );
            if (empty($list_details)) {
                // List does not exist.
                echo "List '$list[name]' does not exist<br/>";

                echo "Creating list $list[name] <br/>";
                // Create the list.
                $lid = xarModAPIfunc(
                    'lists', 'admin', 'createlist',
                    array(
                        'list_name' => $list['name'],
                        'list_desc' => 'Sub-sector/attribute list ' . $list['name'],
                        //'order_columns' => '',
                        'tid' => $ltid
                    )
                );
            } else {
                // TODO: update the list details.
                $list_details = reset($list_details);
                $lid = $list_details['lid'];
            }

            // Now to determine what needs to be updated in the list.
            // Start by fetching the current list items.
            $listitems = xarModAPIfunc(
                'lists', 'user', 'getlistitems',
                array('lid' => $lid, 'items_only' => true, 'dd_flag' => true, 'itemkey' => 'code')
            );
            xarLogMessage(" existing size of list=" . count($listitems));
            echo " existing size of list=" . count($listitems) . "<br/>";

            // Now select the list items we want to replace it with.
            $query = 'SELECT DISTINCT indicators.id, indicators.name,'
                . ' indicators.name_fr, indicators.name_de,'
                . ' indicators.name_es, indicators.name_it'
                . ' FROM remas_ep_subsectors AS subsectors'

                . ' INNER JOIN remas_ep_subsector_indicators AS subsector_indicators'
                . ' ON subsector_indicators.subsector_id = subsectors.id'

                . ' INNER JOIN remas_ep_indicators AS indicators'
                . ' ON indicators.id = subsector_indicators.indicator_id'

                . ' INNER JOIN remas_ep_attributes AS attributes'
                . ' ON attributes.id = indicators.attribute_id'

                . ' INNER JOIN remas_ep_subsector_attributes AS subsector_attributes'
                . ' ON subsector_attributes.subsector_id = subsectors.id'
                . ' AND subsector_attributes.attribute_id = attributes.id'
                . ' AND subsector_attributes.validation IN (\'MANDATORY\', \'OPTIONAL\')'
                . ' WHERE attributes.code = ? AND subsectors.nace = ?'
                //. ' LIMIT 5'
                ;

            $result = $dbconn->execute($query, array($list['attribute'], $list['nace']));
            if (!$result) {return;}

            $newitems = array();
            //$count = 0;
            while (!$result->EOF) {
                list($indicator_id, $indicator_name,
                    $indicator_name_fr, $indicator_name_de, $indicator_name_es, $indicator_name_it) = $result->fields;

                if (!empty($FromCharset)) {
                    if (!empty($indicator_name)) $indicator_name = $encoding_object->Convert($indicator_name, $FromCharset, $ToCharset, 0);
                    if (!empty($indicator_name_fr)) $indicator_name_fr = $encoding_object->Convert($indicator_name_fr, $FromCharset, $ToCharset, 0);
                    if (!empty($indicator_name_de)) $indicator_name_de = $encoding_object->Convert($indicator_name_de, $FromCharset, $ToCharset, 0);
                    if (!empty($indicator_name_es)) $indicator_name_es = $encoding_object->Convert($indicator_name_es, $FromCharset, $ToCharset, 0);
                    if (!empty($indicator_name_it)) $indicator_name_it = $encoding_object->Convert($indicator_name_it, $FromCharset, $ToCharset, 0);
                    //echo "french=$text_fr <br/>"; $result->MoveNext(); continue;
                }

                //$count += 1;
                //echo "<div>$count: $indicator_id $indicator_name</div>";
                $newitems[$indicator_id] = array(
                    'indicator_name' => $indicator_name,
                    'indicator_name_fr' => $indicator_name_fr,
                    'indicator_name_de' => $indicator_name_de,
                    'indicator_name_es' => $indicator_name_es,
                    'indicator_name_it' => $indicator_name_it,
                );
                $result->MoveNext();
            }
            xarLogMessage("Newitem count=" . count($newitems));
            echo "Newitem count=" . count($newitems) . "<br/>";
            //var_dump($newitems); echo "<br/><br/>";
            //var_dump($listitems); echo "<br/><br/>";
            //die;

            // Update the list in two passes.
            // Pass 1: new items to add to the list.
            foreach ($newitems as $indicator_id => $newitem) { //var_dump($indicator_id); var_dump($listitems); die;
                extract($newitem);

                if (!isset($listitems[$indicator_id])) { //echo " notset $indicator_id ";
                    // Add the item to the list.

                    $iid = xarModAPIfunc(
                        'lists', 'admin', 'createlistitem',
                        array(
                            'lid' => $lid,
                            'item_code' => $indicator_id,
                            'item_short_name' => $list['attribute'],
                            'item_long_name' => $list['nace'],
                            'item_desc' => $indicator_name,
                            'dd' => array(
                                'item_desc_fr' => isset($indicator_name_fr) ? $indicator_name_fr : '',
                                'item_desc_de' => isset($indicator_name_de) ? $indicator_name_de : '',
                                'item_desc_es' => isset($indicator_name_es) ? $indicator_name_es : '',
                                'item_desc_it' => isset($indicator_name_it) ? $indicator_name_it : ''
                            )
                        )
                    );
                } else {
                    // Update the existing item.
                    $res = xarModAPIfunc(
                        'lists', 'admin', 'updatelistitem',
                        array(
                            'iid' => $listitems[$indicator_id]['iid'],
                            'item_desc' => $indicator_name,
                            'dd' => array(
                                'item_desc_fr' => isset($indicator_name_fr) ? $indicator_name_fr : $listitems[$indicator_id]['indicator_name_fr'],
                                'item_desc_de' => isset($indicator_name_de) ? $indicator_name_de : $listitems[$indicator_id]['indicator_name_de'],
                                'item_desc_es' => isset($indicator_name_es) ? $indicator_name_es : $listitems[$indicator_id]['indicator_name_es'],
                                'item_desc_it' => isset($indicator_name_it) ? $indicator_name_it : $listitems[$indicator_id]['indicator_name_it']
                            )
                        )
                    );
                }
            }
            xarLogMessage("Updated/created indicators");

            // Pass 2: remove items we no longer need.
            //var_dump($listitems); var_dump($newitems); die;
            foreach ($listitems as $indicator_id => $listitem) {
                if (!isset($newitems[$indicator_id])) {
                    echo " Item removed <br/>";
                    $res = xarModAPIfunc(
                        'lists', 'admin', 'deletelistitem',
                        array('iid' => $listitem['iid'])
                    );
                }
            }
            xarLogMessage("Removed old indicators");

            // Update the flag to indicate as done.
            $query = 'UPDATE remas_ep_subsector_attributes'
                . ' SET imported = \'Y\''
                . ' WHERE id = ? AND imported = \'N\'';
            $resulty = $dbconn->execute($query, array((int)$list['subsector_attributes_id']));
            if (!$resulty) {return;}
        }
    }

    // EMA type lists
    if ($p_listtype == 'ema') {
        $listtypes = xarModAPIfunc(
            'lists', 'user', 'getlisttypes',
            array('type_name' => 'ema_multichoice')
        );

        if (!empty($listtypes)) {
            $listtype = reset($listtypes);
            $ltid = $listtype['tid'];
        } else {
            echo "*** list type 'ema_multichoice' not found <br/>";
            return;
        }

        // Get each list to be imported.
        $query = 'SELECT questions.id, questions.code, questions.questionno, questions.emaset, questions.ema_code,'
            . ' questions.text, questions.text_fr, questions.text_de, questions.text_es, questions.text_it'
            . ' FROM remas_ema_questions AS questions'
            . ' WHERE imported = \'N\''
            . ' ORDER BY questions.id';
        $result = $dbconn->execute($query);
        if (!$result) {return;}

        $lists = array();
        //$count = 0;
        while (!$result->EOF) {
            list($id, $code, $questionno, $emaset, $ema_code,
                $text, $text_fr, $text_de, $text_es, $text_it) = $result->fields;
            echo "<div>$id: $ema_code $text</div>";
            xarLogMessage("EMA_ID=$id: $ema_code $text", XARLOG_LEVEL_WARNING);
            if (!empty($FromCharset)) {
                if (!empty($text)) $text = $encoding_object->Convert($text, $FromCharset, $ToCharset, 0);
                if (!empty($text_fr)) $text_fr = $encoding_object->Convert($text_fr, $FromCharset, $ToCharset, 0);
                if (!empty($text_de)) $text_de = $encoding_object->Convert($text_de, $FromCharset, $ToCharset, 0);
                if (!empty($text_es)) $text_es = $encoding_object->Convert($text_es, $FromCharset, $ToCharset, 0);
                if (!empty($text_it)) $text_it = $encoding_object->Convert($text_it, $FromCharset, $ToCharset, 0);
                //echo "french=$text_fr <br/>"; $result->MoveNext(); continue;
            }

            // Get existing list if it exists.
            $list_details = xarModAPIfunc(
                'lists', 'user', 'getlists',
                array('list_name' => $ema_code)
            );

            if (empty($list_details)) {
                // List does not exist.
                echo "List '$ema_code' does not exist<br/>";

                echo "Creating list $code <br/>";

                // Create the list.
                $lid = xarModAPIfunc(
                    'lists', 'admin', 'createlist',
                    array(
                        'list_name' => $ema_code,
                        'list_desc' => $text,
                        //'order_columns' => '',
                        'tid' => $ltid,
                        'dd' => array(
                            'emaset' => $emaset,
                            'questionno' => $questionno,
                            'questioncode' => $code,
                            'list_name_fr' => isset($text_fr) ? $text_fr : '',
                            'list_name_de' => isset($text_de) ? $text_de : '',
                            'list_name_es' => isset($text_es) ? $text_es : '',
                            'list_name_it' => isset($text_it) ? $text_it : ''
                        )
                    )
                );
            } else {
                $list_details = reset($list_details);
                $lid = $list_details['lid'];

                // Update the list.
                if (!empty($lid) && !empty($text)) {
                    $res = xarModAPIfunc(
                        'lists', 'admin', 'updatelist',
                        array(
                            'lid' => $lid,
                            //'list_name' => $ema_code,
                            'list_desc' => (isset($text) && !empty($text) ? $text : $list_details['list_desc']),
                            //'order_columns' => '',
                            //'tid' => $ltid,
                            'dd' => array(
                                'emaset' => $emaset,
                                'questionno' => $questionno,
                                'questioncode' => $code,
                                'list_desc_fr' => (isset($text_fr) && !empty($text_fr) ? $text_fr : $list_details['list_desc_fr']),
                                'list_desc_de' => (isset($text_de) && !empty($text_de) ? $text_de : $list_details['list_desc_de']),
                                'list_desc_es' => (isset($text_es) && !empty($text_es) ? $text_es : $list_details['list_desc_es']),
                                'list_desc_it' => (isset($text_it) && !empty($text_it) ? $text_it : $list_details['list_desc_it'])
                            )
                        )
                    );
                }
            }

            // Now do the list items.

            // Now to determine what needs to be updated in the list.
            // Start by fetching the current list items.
            $listitems = xarModAPIfunc(
                'lists', 'user', 'getlistitems',
                array('lid' => $lid, 'items_only' => true, 'dd_flag' => true, 'itemkey' => 'code')
            );
            echo " existing size of list=" . count($listitems) . "<br/>";

            // Get the new list items.
            $query = 'SELECT id, code, score, `order`, `text`, text_fr, text_de, text_es, text_it'
                . ' FROM remas_ema_options'
                . ' WHERE question_id = ? ORDER BY id';
            $result2 = $dbconn->execute($query, array((int)$id));
            if (!$result2) {return;}

            $newitems = array();
            //$count = 0;
            while (!$result2->EOF) {
                list($newitem_id, $newitem_code, $newitem_score, $newitem_order,
                    $newitem_text, $newitem_text_fr, $newitem_text_de, $newitem_text_es, $newitem_text_it) = $result2->fields;

                if (!empty($FromCharset)) {
                    if (!empty($newitem_text)) $newitem_text = $encoding_object->Convert($newitem_text, $FromCharset, $ToCharset, 0);
                    if (!empty($newitem_text_fr)) $newitem_text_fr = $encoding_object->Convert($newitem_text_fr, $FromCharset, $ToCharset, 0);
                    if (!empty($newitem_text_de)) $newitem_text_de = $encoding_object->Convert($newitem_text_de, $FromCharset, $ToCharset, 0);
                    if (!empty($newitem_text_es)) $newitem_text_es = $encoding_object->Convert($newitem_text_es, $FromCharset, $ToCharset, 0);
                    if (!empty($newitem_text_it)) $newitem_text_it = $encoding_object->Convert($newitem_text_it, $FromCharset, $ToCharset, 0);
                    //echo "french=$text_fr <br/>"; $result->MoveNext(); continue;
                }


                //$count += 1;
                //echo "<div>$count: $indicator_id $indicator_name</div>";
                $newitems[$newitem_id] = array(
                    'code' => $newitem_code,
                    'score' => $newitem_score,
                    'order' => $newitem_order,
                    'text' => $newitem_text,
                    'text_fr' => $newitem_text_fr,
                    'text_de' => $newitem_text_de,
                    'text_es' => $newitem_text_es,
                    'text_it' => $newitem_text_it
                );
                $result2->MoveNext();
            }
            echo "Newitem count=" . count($newitems) . "<br/>";

            // Update the list in two passes.
            // Pass 1: new items to add to the list. (we won't bother with a pass 2)
            foreach ($newitems as $newitem_id => $newitem) { //var_dump($indicator_id); var_dump($listitems); die;
                if (!isset($listitems[$newitem['code']])) {
                    echo " notset $newitem[code] ";
                    // Add the item to the list.
                    $iid = xarModAPIfunc(
                        'lists', 'admin', 'createlistitem',
                        array(
                            'lid' => $lid,
                            'item_code' => $newitem['code'],
                            'item_long_name' => $newitem['score'],
                            'item_short_name' => $newitem['code'], // Just anything, as it can't be NULL
                            'item_order' => $newitem['order'],
                            //'item_long_name' => $list['nace'],
                            'item_desc' => $newitem['text'],
                            'dd' => array(
                                'item_desc_fr' => isset($newitem['text_fr']) ? $newitem['text_fr'] : '',
                                'item_desc_de' => isset($newitem['text_de']) ? $newitem['text_de'] : '',
                                'item_desc_es' => isset($newitem['text_es']) ? $newitem['text_es'] : '',
                                'item_desc_it' => isset($newitem['text_it']) ? $newitem['text_it'] : ''
                            )
                        )
                    );
                } else {
                    $old_item_record =& $listitems[$newitem['code']];
                    // Update the existing item.
                    $res = xarModAPIfunc(
                        'lists', 'admin', 'updatelistitem',
                        array(
                            'iid' => $listitems[$newitem['code']]['iid'],
                            'item_long_name' => $newitem['score'],
                            'item_short_name' => $newitem['code'], // Just anything, as it can't be NULL
                            'item_order' => $newitem['order'],
                            'item_desc' => (!empty($newitem['text']) ? $newitem['text'] : $old_item_record['item_desc']),
                            'dd' => array(
                                'item_desc_fr' => isset($newitem['text_fr']) && !empty($newitem['text_fr']) ? $newitem['text_fr'] : $old_item_record['item_desc_fr'],
                                'item_desc_de' => isset($newitem['text_de']) && !empty($newitem['text_de']) ? $newitem['text_de'] : $old_item_record['item_desc_de'],
                                'item_desc_es' => isset($newitem['text_es']) && !empty($newitem['text_es']) ? $newitem['text_es'] : $old_item_record['item_desc_es'],
                                'item_desc_it' => isset($newitem['text_it']) && !empty($newitem['text_it']) ? $newitem['text_it'] : $old_item_record['item_desc_it']
                            )
                        )
                    );
                    //var_dump($old_item_record); die;
                }
            }

            $query = 'UPDATE remas_ema_questions SET imported = \'Y\''
                . ' WHERE id = ? AND imported = \'N\'';
            $resulty = $dbconn->execute($query, array((int)$id));
            if (!$resulty) {return;}

            // Get next item.
            $result->MoveNext();
        }
    }
}

?>