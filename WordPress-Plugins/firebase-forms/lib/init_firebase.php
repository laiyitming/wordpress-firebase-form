<?php
// Firebase app key
$apikey = "XXXXXXXXXXXXXXXXXXXXXXXXXXX";
$authdomain = "XXX-XX-XXXXX.firebaseapp.com";
$dburl = "https://XXX-XX-XXXXX.firebaseio.com";
$projecturl = "XXX-XX-XXXXX";
$bucket = "XXX-XX-XXXXX.appspot.com";
$senderid = "XXXXXXXXXXXX";

?>
<script>

//Initialize Firebase
var config = {
        apiKey: "<?php echo $apikey?>",
        authDomain: "<?php echo $authdomain?>",
        databaseURL: "<?php echo $dburl?>",
        projectId: "<?php echo $projecturl?>",
        storageBucket: "<?php echo $bucket?>",
        messagingSenderId: "<?php echo $senderid?>"
};
if (!firebase.apps.length) {
firebase.initializeApp(config);
}
var messaging = firebase.messaging();
var pluginUrl = '<?php echo plugins_url();?>';

</script>
<?php

define("DEFAULT_URL",$dburl);

//Firebase Database Token 
define("DEFAULT_TOKEN", "XXXXXXXXXXXXXXXXXXXXXXXXXXX");


//API path using whihc we are going to fetch participants data from Elastic Serach thrugh AWS API
if (!defined('FIREBASE_ES_API')) define( 'FIREBASE_ES_API', 'https://xxxxxxxxxx.execute-api.ap-southeast-1.amazonaws.com/prod/getes/');

//This key will be used to authenticate with AWS API gateway 
if (!defined('FIREBASE_AWS_API_SECRET')) define( 'FIREBASE_AWS_API_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXXXXX');
?>