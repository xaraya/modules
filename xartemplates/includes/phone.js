function autotab(original,destination){
    if (original.getAttribute&&original.value.length==original.getAttribute("maxlength"))
        destination.focus();
}