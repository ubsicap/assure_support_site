$ (document).ready (function () {
  //check sensitive info in title
  $ ('#title').change (function () {
  //   this.value.filter(function(){
  //     var emailRegex = /\b[A-Za-z\.0-9!#$%&'*+/=?^_`{|}~-]+@[A-Za-z1-9-]+\.[A-Za-z0-9-]+\b/g;
  //     return text.match (emailRegex);
  // }).wrap('<span style="color: red" />');
    var warningMessage = checkField (this.value); //validate the text field
    var errorRegion = $ ('#title').parent (); //area for the warning message
    displayWarning (warningMessage, errorRegion);
  });

  //check sensitive info in body
  $.getScript ('/qa-plugin/wysiwyg-editor/ckeditor/ckeditor.js?1.8.6')
    .done (function (script, textStatus) {
      console.log("script")
      console.log($("iframe"))
      $ ('iframe').on ('load', function () {
        console.log("load")
        $ ('iframe').contents ().find ('body').bind ('DOMSubtreeModified', function () {
          console.log("bind")
          var bodies = $ ('iframe').contents ().find ('body');
          var warningMessage = checkField ($(bodies).textWithLineBreaks()); //validate the text field
          var errorRegion = $ ('#cke_content').parent (); //area for the warning message
          displayWarning (warningMessage, errorRegion);
        
        }); 
      });
    })
    .fail (function (jqxhr, settings, exception) {
      console.log ('failed to get editor');
    });

  //check sensitive info in tagbox, either add or remove warning
  $ ('.tagbox > ul').bind ('DOMSubtreeModified', function () {
    var listItems = $ ('.tagbox > ul li');
    var notFound = true;
    listItems.each (function (idx, li) {
      if (idx === listItems.length - 1) return false; //does not check for the input
      var product = $ (li);
      var VAL = product.children (':first').text (); //get the text of tag
      var warningMessage = checkField (VAL); //validate the text field
      var errorRegion = $ ('.tagbox').parent (); //area for the warning message
      displayWarning (warningMessage, errorRegion);

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

  //check sensitive info in comments
  $ ('.qa-form-light-button-comment').click (function () {
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

function checkField (text) {

  var warnings = '';
  emailMatches = checkEmail (text);
  if (
    emailMatches != null //add all the warnings (if there are any)
  )
    for (var match of emailMatches)
      warnings = warnings + '<br>' + match;

  if (warnings.length == 0)
    return null; //no warning needed
  else return createWarning (warnings); //format with warning message
}

function checkEmail (
  text //return null (no match) or an array of all the matches
) {
  var matches;
  var emailRegex = /\b[A-Za-z\.0-9!#$%&'*+/=?^_`{|}~-]+@[A-Za-z1-9-]+\.[A-Za-z0-9-]+\b/g;
  return text.match (emailRegex);
}

function createWarning (entries) {
  var warning =
    '<div class="post-validator-error">Sensitive information detected: ' +
    entries +
    '<br>Please refer to: <a href="./best-practices" target="_blank" rel="noopener noreferrer">our best practice page.</a></div>';
  return warning;
}

function displayWarning (
  warning,
  region //add message to proper place in the html
) {
  region.find ('.post-validator-error').remove (); //remove previous warning if there was one
  if (
    warning != null //there is a warning, add it
  )
    region.append (warning);
}

//reference: https://github.com/antialias/textWithLineBreaks
(function ($) {
  'use strict';
  $.fn.textWithLineBreaks = function () {
    console.log("fun")
      var onnewline = true,
          f = function (n) {
              var ret = "";
              $(n).contents().each(function (i, c) {
                  var content = "";
                  if (c.nodeType === 3) {
                      content = $(c).text();
                      onnewline = false;
                  } else {
                      if ($(c).is("br")) {
                          content = "\n";
                          onnewline = true;
                      } else {
                          if ("block" === $(c).css('display') && !onnewline) {
                              content = "\n";
                              onnewline = true;
                          }
                          content = content + f(c);
                      }
                  }
                  ret = ret + content;
              });
              return ret;
          };
      return f(this.first());
  };
}(jQuery));
