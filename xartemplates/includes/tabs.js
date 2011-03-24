    function showtab2 ( selectedindex, items )
    {
        for (i=1;i<=items;i++) {
            document.getElementById('page-' + i).style.display = "none";
            document.getElementById('tab-' + i).className = "nothing";
        }
        document.getElementById('page-' + selectedindex).style.display = "block";
        document.getElementById('tab-' + selectedindex).className = "active";
    }
    
    function showtab (selectedindex, items)
    {
        for (i=1;i<=items;i++) {
            document.getElementById('page-' + i).style.display = "none";
            document.getElementById('tab-' + i).className = "xar-tab";
            document.getElementById('href-' + i).className = "xar-norm xar-norm-outline";
        }
        document.getElementById('page-' + selectedindex).style.display = "block";
        document.getElementById('tab-' + selectedindex).className = "xar-tab-active";
        document.getElementById('href-' + selectedindex).className = "xar-accent xar-accent-outline";
    }