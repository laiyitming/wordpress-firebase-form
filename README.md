# wordpress-firebase-form
Wordpress plugins that enables you to create web form that  stores information in Firebase RealTime Database after user login thru FirebaseUI. It will then trigger Firebase Functions, to submit information to AWS Elastic Search thru AWS API Gateway and AWS Lambda.

# Description
  - Create realtime contest with wirdpress plugin, Google firebase[realtime database, Firebase functions, firebase storage], AWS [Iam, API gateway, LAMBDA, Elastic Search]
  - To use this plugin you will have to open account on Google to use google products and on AWS to use amazon services as mentioned above.
  - Also you will have to install firebase CLI to deploy firebase function on firebase.
  - On Google firebase side will need to have premium account as to call third party API's it is must.
  - For firebase functions can refer to: https://firebase.google.com/docs/functions/get-started
  
# Configuration need to configure in init_firebase.php file
- Please change or initiate firebase application by configuring below mentioned parameters in \firebase-forms\lib\init_firebase.php
> $apikey = "XXXXXXXXXXXXXXXXXXXXXXXXXXX";
> $authdomain = "XXX-XX-XXXXX.firebaseapp.com";
> $dburl = "https://XXX-XX-XXXXX.firebaseio.com";
> $projecturl = "XXX-XX-XXXXX";
> $bucket = "XXX-XX-XXXXX.appspot.com";
> $senderid = "XXXXXXXXXXXX";

- DEFAULT_TOKEN:  You can get it from firebase console > project > project settings > Service accounts > Database secrets
- FIREBASE_ES_API: This constant will hold URL of aws API which will post data to ES server with aws LAMDA function
- FIREBASE_AWS_API_SECRET: This constant will hold secret key to authenticate call with aws API
- Refer to https://firebase.google.com/docs/web/setup for more information about setup application on google firebase.

### Installation & Setup for PHP REST API
- You can use PHP Firebase API to connect Firebase database in PHP using API suggested by Firebase https://firebase.google.com/docs/database/rest/start
- We have used PHP API developed by Tamas Kalman
- Go to https://github.com/ktamas77/firebase-php/, install or download below three files
1. firebaseInterface.php
2. firebaseLib.php
3. firebaseStub.php
- Copy them in /firebase-forms/lib folder
- We are using this API to approve and reject contest entries from admin side.
- To deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software please go through Licence section of API first. 

### Used Technologies
1. Google Firebase: Realtime database, firebase functions, Firebase storage
2. AWS: IAM role, API Gateway, LAMBDA, Elastic Search

# Create Contest Page
  - After activating plugin add below code in page content, you can modify it if you want:
```sh
<div style="width: 100%;">
<div style="width: 30%; float: left; margin-right: 10px;">[bootstrap_sidebar_navigation]</div>
<div class="container" style="width: 60%; float: left;">[firebase_form_shortcode]</div>
<div style="clear: both;"></div>
</div>
```
  `[bootstrap_sidebar_navigation]` : Shortcode for sidebar navigation 
  `[firebase_form_shortcode]` : Shortcode for contest forms
  `[firebase_contest_shortcode contest="-LHITRFgQimv9k6UzbYn"]` : Shortcode to show participated users, where contest is ID of contest whihc you can get from admin
  
### Required Plugin
  - We have created another plugin "bootstrap-sidebar-navigation" to show sidebar with links to diffrent pages of site
  - To add or modify existing navigation you need to do changes in /view/navigation.php
  - Note: We used javascript function to call another page which is boot_loadPage() eq.
	javascript:boot_loadPage(3, 2)  - where 3 is page id and 2 header id which we have added in firebase forms plugin
  - Also need to keep ID of "<a>" element in specific format: "nav_lnk{pageid}{headerid}" eq: <a id="nav_lnk32" href="javascript:boot_loadPage(3, 2)">Fathers Day Contest</a>

### Customize Plugin Firbase Forms plugin
  - If you want to add new form or page you have to add two files
    	1. form-{id}.php eq. form-2.php in folder /view/
	2. form-{id}.js  eq. form-2.js in folder /js/
	3. Similar to form you can add new header with file name headerlinks-{id}.php, eq. headerlinks-2.php 
	4. For your referance we have added few forms whihc you can refer to add new
  - On admin side, we have added page using whihc you can add new contest and see all participated users.
  - For added sample contests we have considered 
  - If you want to customize <select> dropdown using select2 lib to implement multiple select drop down you need to follow  two points below with some changes in form.js and form html
  	1. Go to https://github.com/select2/select2/blob/master/LICENSE.md, find and download select2.min.js file then paste it in WordPress-Plugins/firebase-forms/js folder
  	2. Go to https://github.com/select2/select2/blob/master/LICENSE.md, find and download select2.min.css file then paste it in WordPress-Plugins/firebase-forms/css folder
	
## Workflow
  1. Add contest from admin, once you add contest you will get contest id whihc you need to configure in js file of form 
  2. When user submit form data will get stored to firebase node.
  3. Once data write on firebase node firebase function will get trigger which will post same data to API gateway which will post it to Elastic Search
  4. After post data to Elastic search firebase function will copy data to main node and delete it from temporary node
  5. On frontend side, shortcode [firebase_contest_shortcode contest="-LHITRFgQimv9k6UzbYn"] will fetch all participant data from elastic search and show on site
  6. Note. If you want to change fields need to make changes in form javascript file, Firebase function code and LAMBDA function code
 
## Firebase Database Structure
Node: 
	- wp_fb_admin : Add admin users 
	- wp_fb_contests : This node will include contest data like createdate and name
	- wp_fb_images	: This node will include image info like description, status, createdate and URL
	- wp_fb_tokens	: This node will include user device token which will be used as user login token
	- wp_fb_users	: This node will include user related data like address, name and createdate
	
# License

```
The MIT License (MIT)

Copyright (c) 2018 Christian Lai Yit Ming

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```
