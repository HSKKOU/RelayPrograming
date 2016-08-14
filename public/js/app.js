/* simplified log for debug */
function log(_){ if(true){ console.log.apply(console, arguments); } }

/* ajax post method
 * - url[string]        : post url
 * - type[string]       : post restful method
 * - query[string]      : post parameters composed by JSON
 * - success[function]  : success callback function(data)
 * - fail[function]     : failed callback function(data)
 * - complete[function] : complete callback function(data)
 */
function post(_opt){
  var url = _opt["url"],
      type = _opt["type"],
      query = _opt["query"],
      successCB = _opt["success"],
      failCB = _opt["fail"],
      completeCB = _opt["complete"];
  $.ajax({
    "type": type,
    "url": url,
    "data": JSON.stringify(query),
    "contentType": "application/json",
    "dataType": "json",
    "success": function(_data){ successCB(_data); },
    "error": function(_data){ failCB({"statusCode": _data["status"], "response": _data["responseText"]}); },
    "complete": function(_data){ completeCB(_data); }
  });
}

/* escape special character */
function replaceSpecialChara(_str){
  return _str.replace(/&/g,"&amp;")
             .replace(/ /g,"&nbsp;")
             .replace(/"/g,"&quot;")
             .replace(/'/g,"&#039;")
             .replace(/</g,"&lt;")
             .replace(/>/g,"&gt;");
}

/*  */
function createPrettyCodeHtml(_codeStr){
  return $("<code class='prettyprint'>" + _codeStr + "</code>");
}



/* Cookie */
function createRoomCookieName(_room_id, _column){
  return "RPRoom" + roomId + _column;
}
function cookieNameUserId(_room_id){ return createRoomCookieName(_room_id, "Uid"); }
/* end Cookie */
