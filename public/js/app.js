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

/* syntax highlight */
function createPrettyCodeHtml(_codeStr){
  return $("<code class='prettyprint'>" + _codeStr + "</code>");
}



/* Cookie */
function createRoomCookieName(_room_id, _column){
  return "RPRoom" + roomId + _column;
}
function cookieNameUserId(_room_id){ return createRoomCookieName(_room_id, "Uid"); }
function cookieNameRoomVer(_room_id){ return createRoomCookieName(_room_id, "Ver"); }
/* end Cookie */



/* code and chat */
function createCodeRow(_code, $_tmp){ return $_tmp.append(createPrettyCodeHtml(_code['code'])); }
function createChatRow(_ctext, $_tmp){
  $_tmp.find(".user_ico").css({"background": _ctext['user_color']}).text(_ctext['user_name']).next(".text").text(_ctext['text']);
  return $_tmp;
}

function isCodeOrChatText(_text){ return _text.indexOf(":") == 0; }

function getTextsInRoom(_api, _roomId, _successCB){
  var getUrl = _api+"?room_id="+_roomId;
  post({"url": getUrl, "type": "get", "query": {},
    "success": function(_data){
      log("success get "+getUrl, _data);
      _successCB(_data['data']);
    },
    "fail": function(_data){
      log("fail get "+getUrl, _data);
    },
    "complete": function(_data){ /* nothing to do. */ }
  });
}

function postTexts(_api, _postData, _successCB){
  post({"url": _api, "type": "post", "query": _postData,
    "success": function(_data){
      log("success post "+_api, _data);
      _successCB(_data['data']);
    },
    "fail": function(_data){
      log("fail post "+_api, _data);
    },
    "complete": function(_data){ /* nothing to do. */ }
  });
}
/* end code and chat */




/* 非同期処理の待機 */
function Waiter(_callback, _count){
  var callback = _callback, count = _count, success = true;
  this["resolve"] = function(){
    if(--count == 0){callback(success);};
  };
  this["reject"] = function(){
    success = false;
    if(--count == 0){callback(false);}
  };
  this["finish"] = function(){
    if(count > 0){callback(success);}
    count = 0;
  };
}
/* end 非同期処理の待機 */
