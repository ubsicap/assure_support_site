function checkField(text) 
{
  var warnings = '';
  emailMatches = checkEmail(text);
  //add all the warnings (if there are any
  warnings += addWarningText(checkNames(text), "Names");
  warnings += addWarningText(checkEmail(text), "Email");
  warnings += addWarningText(checkPhone(text), "Phone Number");
  warnings += addWarningText(checkRegistrationCode(text), "Registration Key");
  warnings += addWarningText(checkIP(text), "IP");
  warnings += addWarningText(checkMAC(text), "MAC Address");
  
  if (warnings.length == 0)
      return null; //no warning needed
    console.log(warnings)
  return createWarning (warnings); //format with warning message
}

function createSimpleWarning (text) //create html warning without reference to page
{
    return "<div class=\"post-validator-error\">" + text + "</div>";
}
function insertInWarning(warning, text) //warning should be in format of createWarning return
{
  //insert right before "Please refer to:"
  var insertIndex = warning.indexOf("Please refer to: <a href");
  return warning.slice(0,insertIndex) + text + "<br>" + warning.slice(insertIndex);
}

function createWarning (entries) //create html warning message
{
    var warning =
      '<div class="post-validator-error">Sensitive information detected: ' +
      entries +
      '<br>Please refer to: <a href="./best-practices" target="_blank" rel="noopener noreferrer">our best practice page.</a></div>';
    return warning;
}

function addWarningText(warnings, type) //get the warning text formatted, don't add anything there are no warnings
{
  if(warnings == null) //no warning
    return "";
  var warningText = "<br>&nbsp;&nbsp;&nbsp;&nbsp;" + type + ": "
  for(var match of warnings)
    warningText += match + ", ";
  warningText = warningText.slice(0, -2); //remove the last space and comma

  var maxWarningLength = 200; //max string length before cutting off
  if(warningText.length > maxWarningLength) //chop off the end of the warning if it is too long
    warningText = warningText.substring(0,maxWarningLength-3) + "...";
  return warningText;
}


//regex functions, return null (no match) or an array of all the matches
function checkEmail(text)
{
  if(!enabled_email)
    return null;
  //emails are in the format of begin@mid.end (each has different allowed characters)
  var valRegex = /\b[A-Za-z\.0-9!#$%&'*+/=?^_`{|}~-]+@[A-Za-z1-9-]+\.[A-Za-z0-9-]+\b/g;
  return text.match (valRegex);
}
function checkPhone(text) //phone number check, difficult as phone numbers vary in length often
{
  if(!enabled_phone)
    return null;
  //phone numbers may be difficult as international phone numbers vary significantly, here we limit between 7 and 15 digits
  var valRegex = /\b(\d[\s-()]*){7,15}\b/g;
  return text.match(valRegex);
}
function checkIP(text) //ip address address search
{
  if(!enabled_ip)
    return null;
  //ip's in the format of num.num.num.num where number [0-255]
  //regex checks for num.(3 times)num (where num is [0-999]), we validate after to make sure this is valid
  var valRegex = /\b(\d{1,3}\s*\.\s*){3}\d{1,3}\b/g;
  var matches = text.match(valRegex);
  if(matches == null)
    return null;
  var finalMatches = []; //matches that are true ip addresses
  for(var entry of matches) //go through each match  and check it is a true ip
  {
    var trueIp = true;
    for(var seg of entry.split(".")) //each portion of the ip should be <= 255
      if(parseInt(seg) > 255) //invalid, should be in range [0-255]
        trueIp = false;
    if(trueIp)
      finalMatches.push(entry); //must be a falid ip
  }
  if(finalMatches.length == 0) //return null if no matches
    return null;
  return finalMatches;
}
function checkMAC(text) //mac address 
{
  if(!enabled_mac)
    return null;
  //mac address in the form byte:byte:byte:byte:byte:byte (6 secionts, each a byte in hex)
  //regex like (byte:) 5 times then (byte), allow spaces in between sections
  var valRegex = /\b([a-fA-f0-9]{2}\s*:\s*){5}[a-fA-f0-9]{2}\b/g;
  return text.match (valRegex);
}
function checkRegistrationCode(text) //paratext registration code check i.e. AB1234-CDE23F-... (5 sections seperated by hyphens)
{
  if(!enabled_registration)
    return null;
  //regex like: section-(5 times)section (spaces around hyphen allowed)
  var valRegex = /\b([\da-zA-Z]{6}\s*-\s*){4}[\da-zA-Z]{6}\b/g;
  return text.match (valRegex);
}
function checkImage(text) //check if an image is in the post (only returns true/false, not an array!)
{
  if(!enabled_image)
    return false;
  var valRegex = /<img[^>]*>/;
  return valRegex.test(text); //true if an image element is in the text
}
function checkNames(text) //names may be added in the future, but currently no validation exists
{
  if(!enabled_name)
    return null;
  //nothing here currently
  return null;
}
//end regex functions

function displayWarning(warning, region) //add message to proper place in the html
{
  region.find('.post-validator-error').remove(); //remove previous warning if there was one
  if (warning != null) //there is a warning, add it
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
