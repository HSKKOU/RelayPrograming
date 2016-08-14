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
