var allTags = [];
var hasHints = false;
var tagNumber = Math.max(0,$('.tagbox ul li').length - 1);
$ (document).ready (function () {
  var maxLength = $('#tags').attr("maxlength");
  if($('#tags').val().trim() != '') {
    splitTags($('#tags').val().trim());
    $('#tags').val('');
  }

  $('.tag-length').append('<p2>Length of current tag: <span id="tagLength">0/'+maxLength+'</span></p2><br><p2>Number of Tags: <span id="tagNumber">'+tagNumber+'/5</span></p2>');

  $('#tags').parent().parent().click(function(){
    $('#tags').focus();
  });

  $('#tags').keyup(function (e) { 
    //style the pushed tag
    if(e.keyCode === 32) {
      var tag = $('#tags').val().trim();
      if(makeLiTag(tag)) allTags.push(tag);
       $('#tags').val('');
    }
    //widen the input when text grows
    var length = $("#tags").val().length;
    $(this)[0].style.width = (length+1) + 'em';
    //style the tag length label
    if (length >= maxLength) {
      $('#tagLength').addClass('exceed');
    } else {
      $('#tagLength').removeClass('exceed');
    }
    //update the tag length label
    $('#tagLength').text(length+'/'+maxLength);
  });
  
  //triggered after mousedown or simply lose focus
  $('#tags').focusout(function() {
    var tag = $('#tags').val().trim();
    if(tag.length > 0) {
      if(makeLiTag(tag)) allTags.push(tag);
      $('#tags').val('');
      //hide the tag hints
      $("#tag_hints").parent().css({"display":"none"});
    }
  });

  $('.tagbox').keydown(function(e){
    if(e.keyCode === 8) {
      if($('#tags').val().trim() == ''){
        allTags.pop();
        $('.tagbox ul li').last().prev().remove();
        $('#tagNumber').text(Math.max(0, $('.tagbox ul li').length-1)+'/5');
        if ($('.tagbox ul li').length-1 < 5) {
          $('#tagNumber').removeClass('exceed');
        }
      }
    }
  })

  $('body').on('click', '.delete', function(e) {
    findAndRemove(allTags, $(this).prev().text().trim());
    $(this).parent().remove();
    $('#tagNumber').text(Math.max(0, $('.tagbox ul li').length-1)+'/5');
    if ($('.tagbox ul li').length-1 < 5) {
      $('#tagNumber').removeClass('exceed');
    }
  });

  $('#tag_hints').on('mousedown', '.qa-tag-link', function(e) {
    $('#tags').val('');
    $('#tagLength').text('0/'+maxLength);
  });

  $('#tag_hints').on('mouseup', '.qa-tag-link', function(e) {
    setTimeout(function() {
      var tag = $('#tags').val().trim();
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
  if(allTags.indexOf(tag) === -1 && tag != '' && $('.tagbox ul li').length-1 < 5) {
    var newTag = $('<li><span class="tag">'+tag+'</span>'+'<span class="delete">&#215;</span></li>');
    $(newTag).insertBefore('.tagbox li.new');
    tagNumber += 1;
    $('#tagNumber').text(Math.max(0, $('.tagbox ul li').length-1)+'/5');
  } else {
    return false;
    
  } 
  if ($('.tagbox ul li').length-1 >= 5) {
    $('#tagNumber').addClass('exceed');
  } else {
    $('#tagNumber').removeClass('exceed');
  }
  return true;
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