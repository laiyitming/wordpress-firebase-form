/ == Imports == /
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

var index = event.index;
var doctype = event.type;
var iid = event.id;
var data = JSON.stringify({"status": 0, "message": "There is some error in function"});

req.body = JSON.stringify(event.post_data);
req.method = 'POST';
req.path = path.join('/', index, doctype, iid);
req.region = esDomain.region;

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
console.log('Response: ' + respBody);
context.succeed({"status": 1, "message": respBody});
});
}, function(err) {
console.log('Error: ' + err);
var data = {"status": 0, "message": err};
context.fail(data);
});
}