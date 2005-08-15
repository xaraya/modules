function insertEmotion(file_name) {
	tinyMCE.insertImage(tinyMCE.baseURL + "/plugins/emotions/images/" + file_name);
	tinyMCEPopup.close();
}
