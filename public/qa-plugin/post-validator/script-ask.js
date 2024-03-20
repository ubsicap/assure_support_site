$(document).ready(function() {
  // Check sensitive info in title
  $('#title').change(function() {
    var warningMessage = checkField(this.value); // Validate the text field
    var errorRegion = $('#title').parent(); // Area for the warning message
    displayWarning(warningMessage, errorRegion);
  });

  // Check sensitive info in body
  $.getScript('/qa-plugin/pupi-dm/ckeditor/ckeditor.js?2.0.0')
    .done(function(script, textStatus) {
      var checkIframeBody = function() {
        var iframeBody = $('iframe').contents().find('body');
        if (iframeBody.length) {
          var observer = new MutationObserver(function(mutations) {
            onCKEditor("ask");
          });
          observer.observe(iframeBody[0], { childList: true, subtree: true });
        } else {
          setTimeout(checkIframeBody, 100);
        }
      };
      checkIframeBody();
    })
    .fail(function(jqxhr, settings, exception) {
      console.log('Failed to get editor!');
    });

  // Check sensitive info in tagbox, either add or remove warning
  var tagboxObserver = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      var listItems = $('.tagbox > ul li');
      var tagText = ""; // Text of the tags
      listItems.each(function(idx, li) {
        if (idx === listItems.length - 1) return; // Does not check for the input
        tagText += " " + $(li).children(':first').text(); // Append the text of tag
      });
      var warningMessage = checkField(tagText); // Validate the text field
      var errorRegion = $('.tagbox').parent(); // Area for the warning message
      displayWarning(warningMessage, errorRegion);
    });
  });

  var tagbox = document.querySelector('.tagbox > ul');
  if (tagbox) {
    tagboxObserver.observe(tagbox, { childList: true, subtree: true });
  }
});
