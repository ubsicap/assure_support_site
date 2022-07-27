$ (document).ready (function () {

  //check sensitive info in title
  $ ('#title').change (function () {
    console.log ('title change');
    var warningMessage = checkField(this.value); //validate the text field

    this.parent().find('.post-validator-error').remove(); //remove previous warning if there was one
    if(warningMessage != null) //there is a warning, add it
      this.parent().append(warningMessage);
  });

  //check sensitive info in content
  $ ('#content_ckeditor_ok').change (function () {
    console.log('change content');
    if ($ ('#content_ckeditor_ok').parent ().find ('.post-validator-error').length === 0) {
      var warning = createWarning ('email');
      $ ('#content_ckeditor_ok').parent ().append (warning);
    }
  });

  //check sensitive info in tagbox, either add or remove warning
  $ ('.tagbox > ul').bind ('DOMSubtreeModified', function () {
    var listItems = $ ('.tagbox > ul li');
    var notFound = true;
    listItems.each (function (idx, li) {
      if (idx === listItems.length - 1) return false; //does not check for the input
      var product = $ (li);
      var VAL = product.children (':first').text (); //get the text of tag
      var email = new RegExp (
        "\\b[A-Za-z\\.0-9!#$%&'*+/=?^_`{|}~-]+@[A-Za-z1-9-]+\\.[A-Za-z0-9-]+\\b"
      );
      if (email.test (VAL)) {
        notFound = false;
        if (
          $ ('.tagbox').parent ().find ('.post-validator-error').length === 0
        ) {
          var warning = createWarning ('email');
          $ ('.tagbox').parent ().append (warning);
          return false;
        }
      }
    });
    if (notFound) {
      $ ('.tagbox').parent ().find ('.post-validator-error').remove ();
    }
  });

  //check sensitive info in comments
  $ ('.qa-form-light-button-comment').click (function () {
    if ($ (' .qa-c-form ').css ('display') != 'none') {
      $("textarea").on('input',function () {
        var VAL = this.value;
        var email = new RegExp (
          "\\b[A-Za-z\\.0-9!#$%&'*+/=?^_`{|}~-]+@[A-Za-z1-9-]+\\.[A-Za-z0-9-]+\\b"
        );
        if (email.test (VAL)) {
          if ($("textarea").parent ().find ('.post-validator-error').length === 0) {
            var warning = createWarning ('email');
            $("textarea").parent ().append (warning);
          }
        } else {
          $("textarea").parent ().find ('.post-validator-error').remove ();
        }
      });
    }
  });


});

function checkField(text)
{
  var warnings = "";
  emailMatches = checkEmail(text);
  if(emailMatches != null) //add all the warnings (if there are any)
    for(var match of emailMatches)
      warnings = warnings + "<br>" + match;

  if(warnings.length == 0)
    return null; //no warning needed
  else
    return createWarning(warnings); //format with warning message
}

function checkEmail(text) //return null (no match) or an array of all the matches 
{
  var matches;
  var emailRegex = "\\b[A-Za-z\\.0-9!#$%&'*+/=?^_`{|}~-]+@[A-Za-z1-9-]+\\.[A-Za-z0-9-]+\\b";
  return text.match(emailRegex);
}

function createWarning (entries) {
  var warning =
    '<div class="post-validator-error">Sensitive information detected: ' +
    entries +
    '<br>Please refer to: <a href="./best-practices" target="_blank" rel="noopener noreferrer">our best practice page.</a></div>';
  return warning;
}
