$(document).ready(function() {
  function setupEditorObservation(buttonSelector) {
    $(buttonSelector).children().last().click(function() {
      $.getScript('/qa-plugin/pupi-dm/ckeditor/ckeditor.js?1.8.6')
        .done(function() {
          var checkInterval = setInterval(function() {
            var iframeBody = $('iframe').contents().find('body');
            if (iframeBody.length) {
              clearInterval(checkInterval);
              var observer = new MutationObserver(function() {
                onCKEditor("ask");
              });
              observer.observe(iframeBody[0], { childList: true, subtree: true });
            }
          }, 100);
        })
        .fail(function() {
          console.log('Failed to get editor!');
        });
    });
  }

  // Setup observation for different button groups
  setupEditorObservation('.qa-q-view-buttons');
  setupEditorObservation('.qa-a-item-buttons');
  setupEditorObservation('.qa-c-item-buttons');
});
