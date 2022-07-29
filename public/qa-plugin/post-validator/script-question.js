$ (document).ready (function () 
{
  //check sensitive info in answer
  $('.qa-q-view-buttons').children().last().click(function()
  {
    //check sensitive info in body
    $.getScript ('/qa-plugin/wysiwyg-editor/ckeditor/ckeditor.js?1.8.6')
    .done (function (script, textStatus) {
      var interval = setInterval(function() {
        $('iframe').contents ().find ('body').bind ('DOMSubtreeModified', function () {onCKEditor("ask");}); 
        if ($("iframe").contents().find('body').length ) 
          clearInterval(interval);
      }, 100)
      ;}) 
    .fail (function (jqxhr, settings, exception) {
      console.log ('Failed to get editor!');
    });
  });

  //check sensitive info in comment
  $('.qa-a-item-buttons').children().last().click(function()
  {
    //check sensitive info in body
    $.getScript ('/qa-plugin/wysiwyg-editor/ckeditor/ckeditor.js?1.8.6')
    .done (function (script, textStatus) {
      var interval = setInterval(function() {
        $('iframe').contents ().find ('body').bind ('DOMSubtreeModified', function () {onCKEditor("ask");}); 
        if ($("iframe").contents().find('body').length ) 
          clearInterval(interval);
      }, 100)
      ;}) 
    .fail (function (jqxhr, settings, exception) {
      console.log ('Failed to get editor!');
    });
  });

  //check sensitive info in reply (comment)
  $('.qa-c-item-buttons').children().last().click(function()
  {
    //check sensitive info in body
    $.getScript ('/qa-plugin/wysiwyg-editor/ckeditor/ckeditor.js?1.8.6')
    .done (function (script, textStatus) {
      var interval = setInterval(function() {
        $('iframe').contents ().find ('body').bind ('DOMSubtreeModified', function () {onCKEditor("ask");}); 
        if ($("iframe").contents().find('body').length ) 
          clearInterval(interval);
      }, 100)
      ;}) 
    .fail (function (jqxhr, settings, exception) {
      console.log ('Failed to get editor!');
    });
  });
  
});
