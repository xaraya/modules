f_open = new Image();
f_open.src = 'modules/filemanager/xarimages/folderopen.gif';
f_more = new Image();
f_more.src = 'modules/filemanager/xarimages/foldermore.gif';
f_closed = new Image();
f_closed.src = 'modules/filemanager/xarimages/folder.gif';

var tree_current;

function treeToggle(e){

    // e is the link we clicked
    // li is the parent of the link
    var li = e.parentNode;

    // open the closed folder
    if(li.getAttribute('class') == 'filemanager-browser-folder-closed'){
        openNode(e);
    }
    else{
        closeNode(e);
    }
}

function openNode(e){
    // e is the link we clicked
    // li is the parent of the link
    var li = e.parentNode;
    // ul is the list toggled by the link
    var ul = e.parentNode.childNodes[2];

    var img = e.childNodes[0];

    if(tree_current != null){
        tree_current.setAttribute('class', '');
    }
    tree_current = e;
    tree_current.setAttribute('class', 'filemanager-browser-current');

    li.setAttribute('class', 'filemanager-browser-folder-open');
    if(ul != undefined && ul.nodeName == 'UL'){
        ul.setAttribute('class', 'filemanager-browser-folder-open');
        ul.style.display = 'block';

        // loop thru inner list to find nested lists
        var kids = ul.childNodes.length;
        var i;
        for(i = 0; i < kids; i++){
            // see if child node is an li
            if(ul.childNodes[i].nodeName == 'LI'){
                // see if child node contains another list
                if(ul.childNodes[i].childNodes.length >= 4 && ul.childNodes[i].childNodes[2].nodeName == 'UL'){
                    if(ul.childNodes[i].childNodes[0].childNodes[0].src == f_closed.src){
                        ul.childNodes[i].childNodes[0].childNodes[0].src = f_more.src;
                    }
                }
                img.src = f_open.src;
            }
        }
    }
    updatePath();

}

function closeNode(e){
    // e is the link we clicked
    // li is the parent of the link
    var li = e.parentNode;
    // ul is the list toggled by the link
    var ul = e.parentNode.childNodes[2];

    var img = e.childNodes[0];

    if(tree_current != null){
        tree_current.setAttribute('class', '');
    }
    tree_current = e;
    tree_current.setAttribute('class', 'filemanager-browser-current');

    li.setAttribute('class', 'filemanager-browser-folder-closed');

    if(ul != undefined && ul.nodeName == 'UL'){
        img.src = f_more.src;
        ul.setAttribute('class', 'filemanager-browser-folder-closed');
        ul.style.display = 'none';
    }

    updatePath();
}

function updatePath(){
    var e = tree_current;
    var p = e.childNodes[1].nodeValue;
    var l = true;
    while(l){
        e = e.parentNode.parentNode.parentNode.childNodes[0];
        if(e.nodeName == 'A'){
            p = e.childNodes[1].nodeValue + '/' + p;
        }
        else{
            l = false;
        }
    }
    document.getElementById('filemanager-browser-filepath').value =  '/' + p;
}

function openPath(){
    p = document.getElementById('filemanager-browser-filepath').value;
    nodelist = p.split('/');
    nodelist.shift();
//    alert(nodelist[0]);

    return false;
}













