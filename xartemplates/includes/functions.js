function filemanager_update_selected(prefix) {
    pListName  = prefix + "filemanager_attachment_list";
    pTotalName = prefix + "filemanager_attachment_total";

    p = window.opener;

    pList = p.document.getElementById(pListName);
    pTotal = p.document.getElementById(pTotalName);

    lList = document.getElementById('filemanager_attachment_list');
    lTotal = document.getElementById('filemanager_attachment_total');

    pTotal.innerHTML = lTotal.value;
    pList.value = lList.value;

    this.window.close();
}

function filemanager_popup(URL, prefix) {
    day = new Date();
    id = prefix + 'filemanager_attachment_manager';
    window.open(URL, id, 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=720,height=780');
}

function filemanager_test(prefix) {
    ListName  = prefix + "filemanager_attachment_list";
    TotalName = prefix + "filemanager_attachment_total";
    lList  = document.getElementById(ListName);
    lTotal = document.getElementById(TotalName);
    alert("List: " + lList.value + "\nTotal: " + lTotal.innerHTML);
}
