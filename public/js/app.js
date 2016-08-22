/* global variable */
var api_user, api_code, api_chat, api_room, api_member, api_roomst,
    userId = -1,
    roomId = -1,
    userIdCN = "",
    userName = "",
    userColor = "#ffffff";

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
function encodeSpecialChara(_str){
  return _str.replace(/&/g,"&amp;")
             .replace(/ /g,"&nbsp;")
             .replace(/"/g,"&quot;")
             .replace(/'/g,"&#039;")
             .replace(/</g,"&lt;")
             .replace(/>/g,"&gt;");
}
function decodeSpecialChara(_str){
  return _str.replace(/&amp;/g,"&")
             .replace(/&nbsp;/g," ")
             .replace(/&quot;/g,'"')
             .replace(/&#039;/g,"'")
             .replace(/&lt;/g,"<")
             .replace(/&gt;/g,">");
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
function resetCookiesInRoom(_room_id){
  $.removeCookie(cookieNameUserId(_room_id));
  $.removeCookie(cookieNameRoomVer(_room_id));
}
/* end Cookie */



/* code and chat */
function createChatRow(_ctext, $_tmp){
  $_tmp
  .find(".user_ico").css({"background": _ctext['user_color']}).text(decodeSpecialChara(_ctext['user_name']))
  .next(".text").text(decodeSpecialChara(_ctext['text']));
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




/* Code Row Creator */
function createCodeRowCreator($_tmp){
  var $codeRowTmp = $_tmp,
      $nextCodeRow = $_tmp.clone(),
      rowNumber = 0;

  this['createCR'] = function(_code){
    var $codeRow = createCodeRow(_code, $codeRowTmp.clone());
    rowNumber++;
    $codeRow.attr("data-num", rowNumber);
    $codeRow.find(".number").text(rowNumber);
    return $codeRow;
  };

  this['createNextRow'] = function(){
    $nextCodeRow.find(".code").append(createPrettyCodeHtml(""));
  }

  this['updateNextRow'] = function(_code){
  }

  function createCodeRow(_code, $_tmp){
    $_tmp.find(".code").append(createPrettyCodeHtml(_code['code']));
    return $_tmp;
  }
}
/* end Code Row Creator */




/* room */
var roomVerCN = "";
function getRoomVersion(){
  var rVer = $.cookie(roomVerCN);
  if(rVer === void 0 || rVer === null){ rVer = "0000-00-00 00:00:00"; }
  return rVer;
}
function storeRoomVersion(_ver){ $.cookie(roomVerCN, _ver, { "path": "/", "expires": 1 }); }
/* end room */




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




/* heart beat */
var heartBeatSPB = 1.0,
    canPostGHB = false, isActiveWindow = false,
    recievedCodes, recievedChatTexts, recievedMembers;
function startHeartBeat(){
  $(window).on("focus", function(){ isActiveWindow = true; })
  .on("blur", function(){ isActiveWindow = false; })
  .focus();
  isActiveWindow = true;
  canPostGHB = true;
  heartBeat();
}

function postHeartBeat(){
  if(!canPostGHB || !isActiveWindow){ return; }
  canPostGHB = false;
  postGetRoomStatus(api_roomst, succeedPostHeartBeat);
}

function postGetRoomStatus(_api, _callback){
  var postData = { "type": "hb", 'user_id': userId, 'room_id': roomId, "ver": getRoomVersion() };
  post({"url": _api, "type": "post", "query": postData,
    // "success": function(_data){ setTimeout(function(){_callback(_data['data']);}, 1400); },
    "success": function(_data){ log("rss", _data); _callback(_data['data']); },
    "fail": function(_data){ log("rs fail", _data);/* nothing to do. */ },
    "complete": function(_data){ /* nothing to do. */ }
  });
}

function succeedPostHeartBeat(_data){
  if(_data['updated']){
    log("rs updated", _data);
    storeRoomVersion(_data['room_ver']);
    recievedCodes(_data['codes']);
    recievedChatTexts(_data['chat']);
    recievedMembers(_data['members']);
  }
  canPostGHB = true;
}

function heartBeat(){
  setTimeout(function(){
    postHeartBeat();
    heartBeat();
  }, heartBeatSPB * 1000);
}
/* end heart beat */
