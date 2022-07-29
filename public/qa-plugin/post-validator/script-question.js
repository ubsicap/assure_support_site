$ (document).ready (function () {
  //check sensitive info in answer in comments
  $('.qa-q-view-buttons').click(function()
  {
    //check sensitive info in body
    $.getScript ('/qa-plugin/wysiwyg-editor/ckeditor/ckeditor.js?1.8.6')
    .done (function (script, textStatus) {
      console.log("script")
      var interval = setInterval(function() {
        console.log("load")
        $ ('iframe').contents ().find ('body').bind ('DOMSubtreeModified', function () {
          console.log("bind")
          var bodies = $ ('iframe').contents ().find ('body');
          var warningMessage = checkField ($(bodies).textWithLineBreaks()); //validate the text field (plaintext)
          
          if(checkImage($(bodies).html())) //special case for image in text
          {
            if(warningMessage == null) //image but no other warnings
              warningMessage = createSimpleWarning("Make sure images don't contain sensitive information!");
            else //otherwise insert in the warning
              warningMessage = insertInWarning(warningMessage,"Make sure images don't contain sensitive information!");
          }
          var errorRegion = $ ('.cke_inner').parent ().parent(); //area for the warning message
          displayWarning (warningMessage, errorRegion);
        });
       
      }, 100)
      if ($("iframe"). length ) {
        console.log("c")
        clearInterval(interval);
      }   
      ;}) 
 
    .fail (function (jqxhr, settings, exception) {
      console.log ('failed to get editor');
    });
  });

  //check sensitive info in comments
  $('.qa-a-item-buttons, .qa-c-item-buttons').click(function() 
  {
    if ($ (' .qa-c-form ').css ('display') != 'none') 
    {
      $ ('textarea').on ('input', function () {
        var warningMessage = checkField (this.value); //validate the text field
        if(warningMessage != null) {
          warningMessage = '<tr><td class="qa-form-tall-data">'+warningMessage+'</td></tr>';
    var errorRegion = $ ('textarea').parent ().parent().parent(); //area for the warning message
    displayWarningForComment (warningMessage, errorRegion);
        }
      });
    }
  });
});

function displayWarningForComment(warning, region) //add message to proper place in the html
{
  var found = region.find('.post-validator-error');
  if($(found)) {
    $(found).parent().parent().remove();
  }
  //there is a warning, add it
  if (warning != null)  {
    console.log(region.last())
    $(warning).insertBefore(region.last());
  }
    // region.append (warning);
}
