$ (document).ready (function () {
  //check sensitive info in title
  $ ('#title').change (function () {
    console.log ('change');
    var VAL = this.value;
    var email = new RegExp (
      "\\b[A-Za-z\\.0-9!#$%&'*+/=?^_`{|}~-]+@[A-Za-z1-9-]+\\.[A-Za-z0-9-]+\\b"
    );
    if (email.test (VAL)) {
      if ($ ('#title').parent ().find ('.post-validator-error').length === 0) {
        var warning = createWarning ('email');
        $ ('#title').parent ().append (warning);
      }
    } else {
      $ ('#title').parent ().find ('.post-validator-error').remove ();
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
function createWarning (category) {
  var warning =
    '<div class="post-validator-error">Sensitive information detected: ' +
    category +
    '. Please refer to: <a href="https://supportsitetest.tk/best-practices" target="_blank" rel="noopener noreferrer">our best practice page.</a></div>';
  return warning;
}
