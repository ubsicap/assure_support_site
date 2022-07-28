$ (document).ready (function () {
  //check sensitive info in title
  $ ('#title').change (function () {
    var warningMessage = checkField (this.value); //validate the text field
    var errorRegion = $ ('#title').parent (); //area for the warning message
    displayWarning (warningMessage, errorRegion);
  });

  
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
      if ($("iframe"). length ) {
        console.log("c")
        clearInterval(interval);
        }    
    }, 100);
        }) .fail (function (jqxhr, settings, exception) {
      console.log ('failed to get editor');
    });

  //check sensitive info in tagbox, either add or remove warning
  $ ('.tagbox > ul').bind ('DOMSubtreeModified', function () {
    var listItems = $ ('.tagbox > ul li');
    var notFound = true;
    listItems.each (function (idx, li) {
      if (idx === listItems.length - 1) return false; //does not check for the input
      var product = $ (li);
      var VAL = product.children (':first').text(); //get the text of tag
      var warningMessage = checkField (VAL); //validate the text field
      var errorRegion = $ ('.tagbox').parent(); //area for the warning message
      displayWarning(warningMessage, errorRegion);

      // var email = new RegExp (
      //   "\\b[A-Za-z\\.0-9!#$%&'*+/=?^_`{|}~-]+@[A-Za-z1-9-]+\\.[A-Za-z0-9-]+\\b"
      // );
      // if (email.test (VAL)) {
      //   notFound = false;
      //   if (
      //     $ ('.tagbox').parent ().find ('.post-validator-error').length === 0
      //   ) {
      //     var warning = createWarning ('email');
      //     $ ('.tagbox').parent ().append (warning);
      //     return false;
      //   }
      // }
    });
    // if (notFound) {
    //   $ ('.tagbox').parent ().find ('.post-validator-error').remove ();
    // }
  });

  //check sensitive info in answer in comments
  $('.qa-q-view-buttons').click(function(){

  });

  //check sensitive info in comments
  $ ('.qa-a-item-buttons, .qa-c-item-buttons').click (function () {
    if ($ (' .qa-c-form ').css ('display') != 'none') {
      $ ('textarea').on ('input', function () {
        var VAL = this.value;
        var email = new RegExp (
          "\\b[A-Za-z\\.0-9!#$%&'*+/=?^_`{|}~-]+@[A-Za-z1-9-]+\\.[A-Za-z0-9-]+\\b"
        );
        if (email.test (VAL)) {
          if (
            $ ('textarea').parent ().find ('.post-validator-error').length === 0
          ) {
            var warning = createWarning ('email');
            $ ('textarea').parent ().append (warning);
          }
        } else {
          $ ('textarea').parent ().find ('.post-validator-error').remove ();
        }
      });
    }
  });
});

