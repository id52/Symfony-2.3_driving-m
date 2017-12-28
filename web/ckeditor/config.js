CKEDITOR.editorConfig = function (config) {
    config.removeDialogTabs = 'image:advanced;link:advanced;flash:advanced';
    config.extraPlugins = 'video';
    config.filebrowserBrowseUrl = '/pdw_file_browser/index.php?editor=ckeditor';
    config.filebrowserImageBrowseUrl = '/pdw_file_browser/index.php?editor=ckeditor&filter=image';
    config.filebrowserFlashBrowseUrl = '/pdw_file_browser/index.php?editor=ckeditor&filter=flash';
    config.filebrowserVideoBrowseUrl = '/pdw_file_browser/index.php?editor=ckeditor&filter=video';
//    config.contentsCss = '/v2/css/style.css';
    config.allowedContent = true;
};
