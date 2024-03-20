$(document).ready(function() {
  // On edit answer/comment
  $.getScript('/qa-plugin/pupi-dm/ckeditor/ckeditor.js?2.0.0')
    .done(function(script, textStatus) {
      var setupObserver = function() {
        var iframeBody = $('iframe').contents().find('body');
        if (iframeBody.length) {
          // Successfully found the iframe's body, clear the interval
          clearInterval(checkInterval);

          // Create a MutationObserver instance to observe changes
          var observer = new MutationObserver(function(mutations) {
            onCKEditor("question-edit");
          });

          // Configuration for the observer
          observer.observe(iframeBody[0], { childList: true, subtree: true });

          // Optionally, you can disconnect the observer later if needed
          // observer.disconnect();
        }
      };

      // Check for the iframe's body availability every 100ms
      var checkInterval = setInterval(setupObserver, 100);
    })
    .fail(function(jqxhr, settings, exception) {
      console.log('Failed to get editor!');
    });
});
