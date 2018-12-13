var AWS = require('aws-sdk');
var path = require('path');
var esDomain = {
region: 'xx-xxxxxxxx-1',
endpoint: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxx.ap-xxxxxxxx-1.es.amazonaws.com'
};
var endpoint = new AWS.Endpoint(esDomain.endpoint);
var creds = new AWS.EnvironmentCredentials('AWS');

/ Lambda "main": Execution begins here /
exports.handler = function(event, context) {
console.log("--- EVENT: ");
console.log(JSON.stringify(event));

var req = new AWS.HttpRequest(endpoint);
req.region = esDomain.region; 

var index = event.index;
var doctype = event.type;

if(event.id != undefined){
var iid = event.id;
req.path = path.join('/', index, doctype, iid);
req.method = 'GET';
}else{
req.path = path.join('/', index, doctype);
req.body = JSON.stringify(event.post_data);
req.method = 'POST';
req.path = req.path+"/_search";
}

req.headers['presigned-expires'] = false;
req.headers['Host'] = endpoint.host;
req.headers['Content-Type'] = 'application/json';

var signer = new AWS.Signers.V4(req , 'es'); // es: service code
signer.addAuthorization(creds, new Date());

var send = new AWS.NodeHttpClient();
send.handleRequest(req, null, function(httpResp) {
var respBody = '';
httpResp.on('data', function (chunk) {
respBody += chunk;
});
httpResp.on('end', function (chunk) {
console.log(JSON.stringify(respBody));
context.succeed({"status": 1, "message": respBody});
});
}, function(err) {
	var data = {"status": 1, "message": JSON.stringify(err)};
	context.fail(data);
});
}