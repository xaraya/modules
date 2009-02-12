<?php

function labaccounting_admin_test() {

    $xartable =& xarDBGetTables();

            xarRemoveInstances('labaccounting');

            $query1 = "SELECT DISTINCT journalid FROM $xartable[labaccounting_journals]";
            $query2 = "SELECT DISTINCT journaltype FROM $xartable[labaccounting_journals]";
            $query3 = "SELECT DISTINCT account_title FROM $xartable[labaccounting_journals]";
            
            $instances = array(
                                array('header' => 'Journal ID:',
                                        'query' => $query1,
                                        'limit' => 20
                                    ),
                                array('header' => 'Journal Type:',
                                        'query' => $query2,
                                        'limit' => 20
                                    ),
                                array('header' => 'Account Title:',
                                        'query' => $query3,
                                        'limit' => 20
                                    )
                            );
            xarDefineInstance('labaccounting','Journals',$instances);
        
            $query1 = "SELECT DISTINCT ledgerid FROM $xartable[labaccounting_ledgers]";
            $query2 = "SELECT DISTINCT accttype FROM $xartable[labaccounting_ledgers]";
            $query3 = "SELECT DISTINCT account_title FROM $xartable[labaccounting_ledgers]";
            
            $instances = array(
                                array('header' => 'Ledger ID:',
                                        'query' => $query1,
                                        'limit' => 20
                                    ),
                                array('header' => 'Ledger Type:',
                                        'query' => $query2,
                                        'limit' => 20
                                    ),
                                array('header' => 'Account Title:',
                                        'query' => $query3,
                                        'limit' => 20
                                    )
                            );
            xarDefineInstance('labaccounting','Ledgers',$instances);
            
            return "Completed";

}

?>