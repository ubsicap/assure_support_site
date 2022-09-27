$ (document).ready (function () 
{
  //on edit answer/comment
  $.getScript ('/qa-plugin/wysiwyg-editor/ckeditor/ckeditor.js?1.8.6')
    .done (function (script, textStatus) {
      var interval = setInterval(function() {
        $('iframe').contents ().find ('body').bind ('DOMSubtreeModified', function () {onCKEditor("question-edit");});
        if ($("iframe").contents().find('body').length )
          clearInterval(interval);  
      }, 100);
    }) 
    .fail (function (jqxhr, settings, exception) {
      console.log('Failed to get editor!')
    });
});