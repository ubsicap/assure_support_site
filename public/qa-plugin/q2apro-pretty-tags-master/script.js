var allTags = [];
$ (document).ready (function () {
  if($('#tags').val().trim() != '') {
    splitTags($('#tags').val().trim());
    $('#tags').val('');
  }
  $('#tags').keydown(function (e) { 
    if(e.keyCode === 32) {
      var tag = $('#tags').val().trim();
        if(makeLiTag(tag)) allTags.push(tag);
        $('#tags').val('');
    }
  });

  $('.tagbox').keydown(function(e){
    if(e.keyCode === 8) {
      if($('#tags').val().trim() == ''){
        allTags.pop();
        $('.tagbox ul li').last().prev().remove();
      }
    }
  })

  $('body').on('click', '.delete', function(e) {
    findAndRemove(allTags, $(this).prev().text().trim());
    $(this).parent().remove();
  });

  $('#tag_hints').on('mouseup', '.qa-tag-link', function(e) {
    setTimeout(function() {
      //var tag = $('#tags').val().trim();
      var tag = $(this).text();
      if(makeLiTag(tag)) allTags.push(tag);
      $('#tags').val('');
    }, 100);
  });

  $('form').on('submit',function(){
    var tag = $('#tags').val().trim();
    if(makeLiTag(tag)) allTags.push(tag);
    $('#tags').val(allTags.join(" "));
    $("#tags").css({"color":"transparent"});
    return true;
  });
});

function makeLiTag(tag) {
  if(allTags.indexOf(tag) === -1 && tag != '') {
    var newTag = $('<li><span class="tag">'+tag+'</span>'+'<span class="delete">&#215;</span></li>');
    $(newTag).insertBefore('.tagbox li.new');
    return true;
  }
  return false;
}

function splitTags(tags) {
  var tagsArray = tags.split(" ");
  var insertTag = '';
  for(let i=0;i<tagsArray.length;i++) {
    insertTag = tagsArray[i].trim();
    makeLiTag(insertTag);
    allTags.push(insertTag);
  }
}

function findAndRemove(tags, item) {
  const index = tags.indexOf(item);
  if(index > -1) {
    allTags.splice(index, 1);
  }
}