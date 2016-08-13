/* simplified log for debug */
function log(_){ console.log.apply(console, arguments); }

/* ajax post method
 * - url[string]        : post url
 * - type[string]       : post restful method
 * - query[string]      : post parameters composed by JSON
 * - callback[function] : callback function(result, data)
 */
function post(_opt){
  var url = _opt["url"],
      type = _opt["type"],
      query = _opt["query"],
      callback = _opt["callback"];
  $.ajax({
    "type": type,
    "url": url,
    "data": JSON.stringify(query),
    "contentType": "application/json",
    "dataType": "json",
    "success": function(_data){ callback(true, _data); },
    "error": function(_data){ callback(false, {"statusCode": _data["status"], "response": _data["responseText"]}); }
  });
}
