function CheckAll(checkornot)
{
for (var i = 0; i < document.SelectedTablesForm.elements.length; i++) {
  document.SelectedTablesForm.elements[i].checked = checkornot; 
}                                                                         
}
