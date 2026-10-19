/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.height = '250px';	
	config.filebrowserBrowseUrl = 'js/ckeditor/plugins/limage/browse.php?opener=ckeditor&type=files&lng=' + (typeof window.__ckLang !== 'undefined' ? window.__ckLang : 'tr');
	config.filebrowserImageBrowseUrl = 'js/ckeditor/plugins/limage/browse.php?opener=ckeditor&type=images&lng=' + (typeof window.__ckLang !== 'undefined' ? window.__ckLang : 'tr');
	config.filebrowserFlashBrowseUrl = 'js/ckeditor/plugins/limage/browse.php?opener=ckeditor&type=flash&lng=' + (typeof window.__ckLang !== 'undefined' ? window.__ckLang : 'tr');
	config.filebrowserUploadUrl = 'js/ckeditor/plugins/limage/upload.php?opener=ckeditor&type=files&lng=' + (typeof window.__ckLang !== 'undefined' ? window.__ckLang : 'tr');
	config.filebrowserImageUploadUrl = 'js/ckeditor/plugins/limage/upload.php?opener=ckeditor&type=images&lng=' + (typeof window.__ckLang !== 'undefined' ? window.__ckLang : 'tr');
	config.filebrowserFlashUploadUrl = 'js/ckeditor/plugins/limage/upload.php?opener=ckeditor&type=flash&lng=' + (typeof window.__ckLang !== 'undefined' ? window.__ckLang : 'tr');	
	config.extraPlugins = 'tliyoutube2,mediaembed';
	config.toolbar = 'ozel';
	config.toolbar_ozel =
	[
		{ name: 'document', items : [ 'Source','-','NewPage','DocProps','Preview','Print','-'] },
		{ name: 'clipboard', items : ['-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace' ] },
		{ name: 'text', items : ['NumberedList','BulletedList','Outdent','Indent','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Table','Iframe','Subscript','Superscript','Smiley','SpecialChar','Blockquote','HorizontalRule'] },
		'/',
		{ name: 'links', items : [ 'Image','tliyoutube2','MediaEmbed','-','Link','Unlink','Anchor' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','-','RemoveFormat'] },
		{ name: 'paragraph', items : [ '-',] },
		{ name: 'insert', items : [ 'Styles','Format','FontSize','TextColor','BGColor','Maximize', 'ShowBlocks','-' ] },
	];
    config.allowedContent = {
        script: true,
        $1: {
            // This will set the default set of elements
            elements: CKEDITOR.dtd,
            attributes: true,
            styles: true,
            classes: true
        }
    };	
};