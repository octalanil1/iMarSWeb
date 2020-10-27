<?php Route::get('/', function () { return view('pages.home'); });

Route::get('/signin', 'UsersController@signin');
Route::post('/signinpost', 'UsersController@signinpost');

Route::get('/logout', 'UsersController@logout');
Route::get('/exportdb', 'Controller@exportdb');

Route::get('/chat', 'UsersController@chat');

Route::get('/signup', 'UsersController@signup');
Route::get('/operator-signup', 'UsersController@operatorsignup');
Route::post('/operatorsignuppost', 'UsersController@operatorsignuppost');

Route::get('/individual-operator-signup/{id}', 'UsersController@individualoperatorsignup');
Route::post('/individualoperatorsignuppost', 'UsersController@individualoperatorsignuppost');

Route::get('/bycompany-surveyor-signup/{id}', 'UsersController@bycompanysurveyorsignup');
Route::post('/bycompanysurveyorsignuppost', 'UsersController@bycompanysurveyorsignuppost');

Route::get('/surveyor-signup', 'UsersController@surveyorsignup');

Route::get('/individual-surveyor-signup', 'UsersController@individualsurveyorsignup');
Route::post('/individualsurveyorsignuppost', 'UsersController@individualsurveyorsignuppost');

Route::get('/company-surveyor-signup', 'UsersController@companysurveyorsignup');
Route::post('/companysurveyorsignuppost', 'UsersController@companysurveyorsignuppost');
Route::post('/getcountrycode', 'UsersController@getcountrycode');


Route::post('/forgotpost', 'UsersController@forgotpost');
Route::get('/create-password/{uniqurl}', 'UsersController@createpass');
Route::post('/create-password-post', 'UsersController@createpasspost');




Route::post('/contactpost', 'homeController@contactpost');
Route::post('/enquirepost', 'homeController@enquirepost');

Route::get('/thanks', function () { return view('pages.thanks'); });
Route::get('/about-us', 'homeController@aboutus');
Route::get('/contact-us', 'homeController@contactus');
Route::post('/contactuspost', 'homeController@contactuspost');

Route::get('/page/{type}/{slug}', 'homeController@staticpages');
Route::post('/email-verify', 'homeController@emailVerify');
Route::get('/verify-email/{id}', 'homeController@emailconfirm');

Route::get('/mobile-verify/{mobile}', 'homeController@mobileVerify');
Route::post('/verify-mobile', 'homeController@mobileconfirm');


Route::group(['middleware' => 'Checkuser'], function () {


Route::get('/myaccount', 'UsersController@myaccount');
Route::post('/myaccount', 'UsersController@myprofile');



Route::post('/appoint-surveyor', 'UsersController@appointsurveyor');
Route::post('/appoint-surveyor-post', 'UsersController@appointsurveyorpost');

Route::post('/getsurveyor', 'UsersController@getsurveyor');



Route::post('/myprofile', 'UsersController@myprofile');

Route::post('/editprofilepost', 'UsersController@editprofilepost');

// Route::post('/mysurvey', 'UsersController@mysurvey');
Route::post('/mysurvey', 'UsersController@mysurveyLatest');



Route::get('/survey-detail/{id}', 'UsersController@surveydetail');
Route::get('/user-detail/{id}', 'UsersController@userdetail');

Route::get('/survey-detail-cal/{id}', 'UsersController@surveydetailcal');

Route::get('/chat-form/{survey_id}/{sender_id}/{receiver_id}', 'UsersController@chatForm');

Route::post('/addchat', 'UsersController@addchat');
Route::post('/updatechat', 'UsersController@updatechat');

Route::post('/getchat', 'UsersController@getchat');

Route::get('/event-detail/{id}', 'UsersController@eventdetail');

Route::post('/survey-accept-reject', 'UsersController@SurveyAcceptReject');
Route::post('/AssignTo', 'UsersController@AssignTo');
Route::post('/AssignToop', 'UsersController@AssignToop');
Route::post('/ChangeStartDate', 'UsersController@ChangeStartDate');


Route::post('/reportsubmit', 'UsersController@reportsubmit');
Route::post('/reportaccept', 'UsersController@reportaccept');
Route::get('/add-rating/{survey_id}/{operator_id}/{surveyor_id}', 'UsersController@addrating');
Route::post('/add-rating-post', 'UsersController@addratingpost');

Route::post('/CustomeSurveyAcceptReject', 'UsersController@CustomeSurveyAcceptReject');	
Route::post('/operatorCustomeSurveyAccept', 'UsersController@operatorCustomeSurveyAccept');	
Route::post('/CancelSurvey', 'UsersController@CancelSurvey');	




Route::post('/myport', 'UsersController@myport');
Route::post('/myportpost', 'UsersController@myportpost');
Route::post('/editportpost', 'UsersController@editportpost');
Route::post('/getport', 'UsersController@getport');
Route::post('/removeport', 'UsersController@removeport');



Route::post('/myoperator', 'UsersController@myoperator');
Route::post('/myoperatorpost', 'UsersController@myoperatorpost');
Route::post('/removeoperator', 'UsersController@removeoperator');

Route::post('/myfinance', 'UsersController@myfinance');

Route::post('/myearning', 'UsersController@myearning');

Route::post('/paymentrequest', 'UsersController@paymentrequest');


Route::post('/mysurveyor', 'UsersController@myoperator');
Route::post('/mysurveyorpost', 'UsersController@myoperatorpost');
Route::post('/removesurveyor', 'UsersController@removeoperator');

Route::post('/myagent', 'UsersController@myagent');
Route::post('/myagentpost', 'UsersController@myagentpost');
Route::post('/removeagent', 'UsersController@removeagent');
Route::post('/getdata', 'UsersController@getdata');


Route::post('/myship', 'UsersController@myship');
Route::post('/myshippost', 'UsersController@myshippost');
Route::post('/removevessel', 'UsersController@removevessel');


Route::post('/addshipfavourite', 'UsersController@addshipfavourite');

Route::post('/mycalendar', 'UsersController@mycalendar');
Route::get('/eventsload', 'UsersController@eventsload');

Route::post('/eventsadd', 'UsersController@eventsadd');
Route::post('/eventsupdate', 'UsersController@eventsupdate');
Route::post('/eventsdelete', 'UsersController@eventsdelete');



Route::post('/my-survey-types', 'UsersController@mysurveytypes');
Route::post('mysurveytypespost', 'UsersController@mysurveytypespost');
Route::post('/removesurveytype', 'UsersController@removesurveytype');
Route::post('/conductcustomsurvey', 'UsersController@conductcustomsurvey');
Route::post('/isavail', 'UsersController@isavail');




Route::post('/payment-detail', 'UsersController@PaymentDetail');
Route::post('/bank-detail-post', 'UsersController@PaymentDetailPost');

Route::post('/GetCalender', 'UsersController@GetCalender');




Route::post('/editagentprofilepost', 'homeController@editagentprofilepost');

Route::post('/change-password', 'UsersController@changepassword');
Route::post('/change-password-post', 'UsersController@changepasswordpost');	
Route::post('/uploaddocument', 'UsersController@uploaddocument');	

Route::post('/report-issue', 'UsersController@reportissue');
Route::post('/report-issue-post', 'UsersController@reportissuepost');	



});

/*-----------------------------------Cron controllers -----------------------------------------------------------*/

Route::get('/send-survey-request', 'CronController@SendSurveyRequest');
Route::get('/accept-report', 'CronController@AcceptReport');



/*-----------------------------------Admin controllers -----------------------------------------------------------*/
Route::get('/admin/login', 'Admin\AdminController@login');
Route::post('/admin/loginpost', 'Admin\AdminController@loginpost');
Route::get('/admin/forgot-password', 'Admin\ForgotPasswordController@index');
Route::post('/admin/post-mail', 'Admin\ForgotPasswordController@postmail');
Route::get('/admin/create-password/{uniqurl}', 'Admin\ForgotPasswordController@createpass');
Route::post('/admin/password-save', 'Admin\ForgotPasswordController@createpasspost');

Route::group(['prefix' => 'admin','middleware' => 'adminSecurity'], function () {
Route::get('/', 'Admin\AdminController@index');
Route::post('/dashboard', 'Admin\AdminController@index');

Route::get('/profile', 'Admin\AdminController@profile');
Route::get('/logout', 'Admin\AdminController@logout');
Route::get('/change-password', 'Admin\AdminController@changepassword');
Route::post('/edit-profile-post', 'Admin\AdminController@editprofile');
Route::post('/change-password-post', 'Admin\AdminController@changepasswordpost');
Route::post('/sendemail', 'Admin\AdminController@sendemail');
	
Route::get('/users', 'Admin\UsersController@index');
Route::post('/users', 'Admin\UsersController@index');
Route::get('/add-user', 'Admin\UsersController@adduser');
Route::post('/add-user-post', 'Admin\UsersController@adduserpost');
Route::get('/edit-user/{id}', 'Admin\UsersController@edituser');
Route::post('/edit-user-post', 'Admin\UsersController@edituserpost');
Route::get('/view-user/{id}', 'Admin\UsersController@viewuser');
Route::post('/user-status', 'Admin\UsersController@userstatus');

Route::get('/reset-password/{id}', 'Admin\UsersController@resetpassword');
Route::post('/reset-password-post', 'Admin\UsersController@resetpasswordpost');


Route::get('/survey-category', 'Admin\SurveyCategoryController@index');
Route::post('/survey-category', 'Admin\SurveyCategoryController@index');
Route::get('/add-survey-category', 'Admin\SurveyCategoryController@addsurveycategory');
Route::post('/add-survey-category-post', 'Admin\SurveyCategoryController@addsurveycategorypost');
Route::get('/edit-survey-category/{id}', 'Admin\SurveyCategoryController@editsurveycategory');
Route::post('/edit-survey-category-post', 'Admin\SurveyCategoryController@editsurveycategorypost');
Route::post('/category-remove', 'Admin\SurveyCategoryController@categoryremove');
Route::post('/survey-category-status', 'Admin\SurveyCategoryController@surveystatus');

Route::get('/survey-type', 'Admin\SurveyTypeController@index');
Route::post('/survey-type', 'Admin\SurveyTypeController@index');
Route::get('/add-survey-type', 'Admin\SurveyTypeController@addsurveycategory');
Route::post('/add-survey-type-post', 'Admin\SurveyTypeController@addsurveycategorypost');
Route::get('/edit-survey-type/{id}', 'Admin\SurveyTypeController@editsurveycategory');
Route::post('/edit-survey-type-post', 'Admin\SurveyTypeController@editsurveycategorypost');
Route::post('/type-remove', 'Admin\SurveyTypeController@categoryremove');
Route::post('/survey-type-status', 'Admin\SurveyTypeController@surveystatus');

Route::get('/users-port', 'Admin\PortController@usersport');
Route::post('/users-port', 'Admin\PortController@usersport');
Route::get('/edit-user-port/{id}', 'Admin\PortController@edituserport');
Route::post('/edit-user-port-post', 'Admin\PortController@edituserportpost');
Route::post('/user-port-remove', 'Admin\PortController@userportremove');

Route::get('/port', 'Admin\PortController@port');
Route::post('/port', 'Admin\PortController@port');


Route::get('/add-port', 'Admin\PortController@addport');
Route::post('/add-port-post', 'Admin\PortController@addportpost');
Route::get('/edit-port/{id}', 'Admin\PortController@editport');
Route::post('/edit-port-post', 'Admin\PortController@editportpost');

Route::get('/import-port', 'Admin\PortController@importport');
Route::post('/import-port-post', 'Admin\PortController@importportpost');


Route::get('/country', 'Admin\CountryController@index');
Route::post('/country', 'Admin\CountryController@index');
Route::get('/add-country', 'Admin\CountryController@addcountry');
Route::post('/add-country-post', 'Admin\CountryController@addcountrypost');
Route::get('/edit-country/{id}', 'Admin\CountryController@editcountry');
Route::post('/edit-country-post', 'Admin\CountryController@editcountrypost');
Route::post('/country-status', 'Admin\CountryController@countrystatus');

Route::get('/import-country', 'Admin\CountryController@importcountry');
Route::post('/import-country-post', 'Admin\CountryController@importcountrypost');


Route::get('/content', 'Admin\ContentController@index');
Route::post('/content', 'Admin\ContentController@index');
Route::get('/add-content', 'Admin\ContentController@addcontent');
Route::post('/add-content-post', 'Admin\ContentController@addcontentpost');
Route::get('/edit-content/{id}', 'Admin\ContentController@editcontent');
Route::post('/edit-content-post', 'Admin\ContentController@editcontentpost');
Route::post('/content-remove', 'Admin\ContentController@contentremove');
    
Route::get('/email-templates', 'Admin\EmailTemplatesController@index');
Route::post('/email-templates', 'Admin\EmailTemplatesController@index');
Route::get('/add-email-templates', 'Admin\EmailTemplatesController@addemailtemplates');
Route::post('/add-email-templates-post', 'Admin\EmailTemplatesController@addemailtemplatespost');

Route::get('/edit-email-templates/{id}', 'Admin\EmailTemplatesController@editemailtemplates');
Route::post('/edit-email-templates-post', 'Admin\EmailTemplatesController@editemailtemplatespost');

Route::get('/survey', 'Admin\SurveyController@index');
Route::post('/survey', 'Admin\SurveyController@index');
Route::get('/view-survey/{id}', 'Admin\SurveyController@viewsurvey');
Route::get('/view-company/{id}', 'Admin\SurveyController@viewcompany');
Route::get('/change-surveryor-status/{id}/{status}', 'Admin\SurveyController@changesurveryorstatus');
Route::post('/change-surveryor-status-post', 'Admin\SurveyController@changesurveryorstatuspost');
Route::get('/chat-form/{survey_id}/{sender_id}/{receiver_id}', 'Admin\SurveyController@chatForm');



Route::get('/dispute-request', 'Admin\RequestController@disputerequest');
Route::post('/dispute-request', 'Admin\RequestController@disputerequest');
Route::post('/dispute-request-action', 'Admin\RequestController@disputerequestaction');
Route::get('/view-dispute-request/{id}', 'Admin\RequestController@viewdisputerequest');
Route::get('/edit-dispute-request/{id}', 'Admin\RequestController@editdisputerequest');
Route::post('/edit-dispute-request-post', 'Admin\RequestController@editdisputerequestpost');


Route::get('/payment-request', 'Admin\RequestController@paymentrequest');
Route::post('/payment-request', 'Admin\RequestController@paymentrequest');
Route::post('/payment-request-action', 'Admin\RequestController@paymentrequestaction');
Route::get('/view-payment-request/{id}', 'Admin\RequestController@viewpaymentrequest');
Route::get('/edit-payment-request/{id}', 'Admin\RequestController@editpaymentrequest');
Route::post('/edit-payment-request-post', 'Admin\RequestController@editpaymentrequestpost');


Route::get('/notification', 'Admin\NotificationController@index');
Route::post('/notification', 'Admin\NotificationController@index');
Route::get('/view-notification/{id}', 'Admin\NotificationController@viewnotification');

Route::get('/add-notification', 'Admin\NotificationController@addnotification');
Route::post('/add-notification-post', 'Admin\NotificationController@addnotificationpost');

Route::get('/earning', 'Admin\EarningController@index');
Route::post('/earning', 'Admin\EarningController@index');
Route::get('/edit-earning/{id}', 'Admin\EarningController@editearning');
Route::post('/edit-earning-post', 'Admin\EarningController@editearningpost');



Route::get('/users-survey-price', 'Admin\UsersSurveyPriceController@index');
Route::post('/users-survey-price', 'Admin\UsersSurveyPriceController@index');

Route::get('/importcountry', 'Admin\PortController@importcountry');



});
