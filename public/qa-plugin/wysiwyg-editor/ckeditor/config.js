/**
 * Q2A custom config for CKEditor
 * NOTE: if you make changes to this file, make sure that you do not overwrite it when upgrading Q2A!
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar arrangement, two rows of buttons
	config.toolbar = [
		{ name: 'font', items: [ 'Font', 'FontSize', 'Format' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote' ] },
		{ name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
		'/',
		{ name: 'basic', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript' ] },
		{ name: 'color', items: [ 'TextColor', 'BGColor' ] },
		{ name: 'links', items: [ 'Link', 'Unlink' ] },
		{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'Smiley' ] },
		{ name: 'last', items: [ 'Undo', 'Redo', 'RemoveFormat', 'Maximize' ] }
	];

	// Set the most common block elements
	config.format_tags = 'p;h1;h2;h3;pre';
	config.entities = false;

	// Make dialogs simpler
	config.removeDialogTabs = 'image:advanced;link:advanced;table:advanced';

	// Use native spell checking (note: Ctrl+right-click is required for native context menu)
	config.disableNativeSpellChecker = false;

	// Prevent blank paragraphs
	config.fillEmptyBlocks = false;

	// Add custom CSS
	config.contentsCss = [CKEDITOR.getUrl('contents.css'), CKEDITOR.getUrl('contents-custom.css')];
	// ========== LARGE IMAGE PASTE CONFIGURATION ==========

	config.uploadUrl = '/upload_handler.php';
	
	// CRITICAL: Configure file upload to handle pasted images
	config.filebrowserUploadUrl = '/upload_handler.php';
	config.filebrowserImageUploadUrl = '/upload_handler.php';
	
	// Enable automatic upload of pasted images
	config.uploadMethod = 'form';
	
	// Additional plugins needed for paste functionality
	try {
	  config.extraPlugins = 'uploadimage,image2,uploadfile,clipboard';
	} catch (e) {
	  console.warn("Upload plugins not available");
	}
	
	// Allow pasting images from clipboard
	config.pasteFromWordRemoveStyles = false;
	config.pasteFromWordRemoveFontStyles = false;
	
	// Enable clipboard plugin with image support
	config.clipboard_handleImages = true;
	
	// Increase file size limit for pasted/uploaded images (in bytes)
	// Default is 0 (no limit). Set to desired max size, e.g., 10MB = 10485760
	config.fileTools_requestHeaders = {
		'X-Requested-With': 'XMLHttpRequest'
	};
	
	// Alternatively, allow base64 encoding for pasted images (embedded in HTML)
	// Warning: This can make your HTML very large with big images
	config.image2_prefillDimensions = false;
	config.image2_disableResizer = false;
	
	// Set maximum width/height to auto-resize pasted images
	// This helps prevent issues with very large images
	// Set maximum file size (10MB)
	config.fileTools_maxFileSize = 10485760;
	
	// Image plugin settings
	config.image2_prefillDimensions = false;
	config.image2_disableResizer = false;
	config.image_previewText = ' ';

	// CRITICAL: Prevent file:// URLs from being inserted
	// This removes the local file reference before it's inserted
	config.on = {
		paste: function(evt) {
			var data = evt.data;
			// Check if pasted content contains file:// URLs
			if (data.dataValue && data.dataValue.indexOf('file://') !== -1) {
				// Block the paste and show a message
				alert('Local file links are not supported. Please wait for the image to upload.');
				evt.cancel();
			}
           }
	};
	// Configure automatic image resizing on paste
	// Uncomment and adjust these values as needed:
	// config.imageResize = {
	// 	maxWidth: 1200,
	// 	maxHeight: 1200,
	// 	quality: 0.85
	// };
};
