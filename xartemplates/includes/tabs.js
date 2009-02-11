    function showtab ( selectedindex, items )
    {
        for (i=1;i<=items;i++) {
            document.getElementById('page-' + i).style.display = "none";
            document.getElementById('tab-' + i).className = "nothing";
        }
        document.getElementById('page-' + selectedindex).style.display = "block";
        document.getElementById('tab-' + selectedindex).className = "active";
    }