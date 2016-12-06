/* global variable */
var api_user, api_code, api_chat, api_room, api_member, api_roomst,
    userId = -1,
    roomId = -1,
    roomLang = "c",
    userIdCN = "",
    userName = "",
    userColor = "#ffffff",
    langList = {
      "c"       : "C",
      "cpp"     : "C++",
      "csharp"  : "C#",
      "java"    : "Java",
      "js"      : "JavaScript",
      "php"     : "PHP",
      "perl"    : "Perl",
      "py"      : "Python",
      "rb"      : "Ruby"
    };


/* simplified log for debug */
function log(_){ if(
  // true
  false
){ console.log.apply(console, arguments); } }

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
  log(roomLang);
  return $("<pre class='brush:" + roomLang + " gutter:false'>" + _codeStr + "</pre>");
}



/* text color setting */
function calcMonoColor(_color) {
  if (_color.match(/^#[0-9A-Fa-f]{0,6}$/) == null) { return "#ffffff"; }
  var r = parseInt(_color.substr(1,2), 16),
      g = parseInt(_color.substr(3,2), 16),
      b = parseInt(_color.substr(5,2), 16);
  if (r*0.3 + g*0.6 + b*0.1 > 127.0) { return "#333333"; }
  else { return "#ffffff"; }
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
  .find(".user_ico").css({"background": _ctext['user_color'], "color": calcMonoColor(_ctext['user_color'])}).text(decodeSpecialChara(_ctext['user_name']))
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

function postTexts(_api, _postData, _successCB, _failCB, _completeCB){
  post({"url": _api, "type": "post", "query": _postData,
    "success": function(_data){
      log("success post "+_api, _data);
      _successCB(_data);
    },
    "fail": function(_data){
      log("fail post "+_api, _data);
      _failCB(_data);
    },
    "complete": function(_data){
      log("complete post "+_api, _data);
      _completeCB(_data);
    }
  });
}
/* end code and chat */




/* Code Row Creator */
function createCodeRowCreator($_viewField, $_view, _lang){
  if(_lang == "php"){ _lang = "php html-script: true"; }
  var $viewFieldTmp = $_viewField.clone().attr("class", "brush:"+_lang).remove(),
      $parentView = $_view,
      codesRowList = [];

  this['updateCR'] = function(_code){
    var lineNum = parseInt(_code["line_num"]);
    if(lineNum == void 0 || lineNum == null || lineNum == ""){ return; }

    if(lineNum >= 1 && lineNum <= codesRowList.length){ codesRowList[lineNum-1] = decodeSpecialChara(_code["code"]); }
    else if (lineNum == codesRowList.length+1) { codesRowList.push(decodeSpecialChara(_code["code"])); }
    else { alert("挿入Errorが発生したので再読み込みをします。"); location.reload(); }

    var $viewField = $viewFieldTmp.clone();
    $parentView.find("#code_view_field").remove();
    $viewField.text(codesRowList.join("\n") + " \n").appendTo($parentView);
  };
}
/* end Code Row Creator */




/* member */
function createMember(_member, _tui, $_memberRowTmp){
  var $memberRow = $_memberRowTmp.clone();
  $memberRow.css({"background": _member['color'], "color": calcMonoColor(_member['color'])}).text(_member['name'])
  .addClass("uid"+_member['id']).attr("data-uid", _member['id']);
  if(_member['id'] == _tui){ $memberRow.addClass("turn"); }
  return $memberRow;
}
/* end member */




/* room */
var roomVerCN = "";
function getRoomVersion(){
  var rVer = $.cookie(roomVerCN);
  if(rVer === void 0 || rVer === null){ rVer = "0000-00-00 00:00:00"; }
  return rVer;
}
function storeRoomVersion(_ver){ $.cookie(roomVerCN, _ver, { "path": "/", "expires": 1 }); }

function getRoomInfo(_api, _rId, _successCB){
  log(_api, _rId);
  post({"url": _api+_rId, "type": "get", "query": {},
    "success": function(_data){
      log("success get room info", _data);
      setRoomInfoCommon(_data['data']);
      _successCB(_data['data']);
    },
    "fail": function(_data){
      log("fail get "+getUrl, _data);
    },
    "complete": function(_data){ /* nothing to do. */ }
  });
}

function setRoomInfoCommon(_data) {
  roomLang = _data['room_lang'];
  $("#room_issue > .text").text("[" + langList[roomLang] + "] " + _data["room_issue"]);
}
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
  // log(postData);
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
