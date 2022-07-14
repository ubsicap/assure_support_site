$ (document).ready (function () {
  $('#mailing_enabled').click(function() {
    if($('#account_claim_mailing_enabled').is(':checked')) { alert("it's checked"); }
 });
 $('input:checkbox').click(function() {
  $('input:checkbox').not(this).prop('checked', false);
});
$("#domalingstart").mousedown(function () {
  window.alert("im changing id to ac");
  $("#domalingstart").attr("name", "accountclaimstart");
  $("#domalingstart").attr("id", "accountclaimstart");

  $("#accountclaimstart").unbind("click").click(function () {
      window.alert("do sth...");
      // $("#btn2").val("hey hey");
      // $("#btn2").attr("id", "btn1");    
  });
});
});
