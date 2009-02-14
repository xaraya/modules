
function select_gl_num(gl_id) {
    
    if(gl_id == "") alert("test");

    if(gl_id >= 1000 && gl_id < 2000) 
    {
        // ASSETS
        document.getElementById('accttype').selectedIndex = 0;
        document.getElementById('normalbalance').selectedIndex = 1;
        alert("Normal balance for Assets is on the Debit side.");
        
    } else if(gl_id >= 2000 && gl_id < 3000) {
        // LIABILITIES
        document.getElementById('accttype').selectedIndex = 1;
        document.getElementById('normalbalance').selectedIndex = 0;
        alert("Normal balance for Liabilities is on the Credit side.");
        
    } else if(gl_id >= 3000 && gl_id < 4000) {
        // EQUITY
        document.getElementById('accttype').selectedIndex = 2;
        document.getElementById('normalbalance').selectedIndex = 0;
        alert("Normal balance for Equity is on the Credit side.");
        
    } else if(gl_id >= 4000 && gl_id < 5000) {
        // REVENUE
        document.getElementById('accttype').selectedIndex = 3;
        document.getElementById('normalbalance').selectedIndex = 0;
        alert("Normal balance for Revenue is on the Credit side.");
        
    } else if(gl_id >= 5000 && gl_id < 6000) {
        // COST OF GOODS SOLD
        document.getElementById('accttype').selectedIndex = 4;
        document.getElementById('normalbalance').selectedIndex = 0;
        alert("Normal balance for Cost of Goods Sold is on the Credit side.");
        
    } else {
        // EXPENSES
        document.getElementById('accttype').selectedIndex = 5;
        document.getElementById('normalbalance').selectedIndex = 1;
        alert("Normal balance for Expenses is on the Debit side.");
    }

}