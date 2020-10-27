<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'Api'], function () 
{
    Route::group(['prefix' => 'auth'], function () {
        //Route::post('/signup', 'apiController@signup'); 
        Route::post('/login', 'AuthController@login'); 
        Route::post('/forgotpassword','AuthController@forgotpassword');
        Route::post('/change-device-id', 'AuthController@ChangeDeviceId'); 
        Route::post('/user-setting', 'AuthController@UserSetting'); 
        // Route::post('/change-password', 'AuthController@changepasswordpost'); 
    });
    Route::group(['prefix' => 'operator'], function () 
    {
        Route::post('/add-vessels', 'OperatorController@AddVessels'); 
        Route::post('/vessels-list', 'OperatorController@VesselsList'); 
        Route::post('/edit-vessels', 'OperatorController@EditVessels'); 
        Route::post('/addshipfavourite', 'OperatorController@addshipfavourite');
        Route::post('/add-agents', 'OperatorController@AddAgents'); 
        Route::post('/agents-list', 'OperatorController@AgentsList'); 
        Route::post('/edit-agents', 'OperatorController@EditAgents'); 
        Route::post('/add-operators', 'OperatorController@AddOperators'); 
        Route::post('/operators-list', 'OperatorController@OperatorsList'); 
        Route::post('/edit-operators', 'OperatorController@EditOperators'); 
        Route::post('/delete-operators', 'OperatorController@DeleteOperators'); 

        Route::post('/resend-operators', 'OperatorController@ResendAddOperators'); 

        // Route::post('/add-surveyor', 'OperatorController@AddSurveyor'); 
        // Route::post('/surveyor-list', 'OperatorController@SurveyorList'); 
        // Route::post('/edit-surveyor', 'OperatorController@EditSurveyor'); 
        // Route::post('/delete-surveyor', 'OperatorController@DeleteSurveyor'); 

        Route::post('/deleted-surveyor', 'OperatorController@DeletedSurveyor'); 


        Route::post('/survey-category', 'OperatorController@SurveyCategory'); 
		Route::post('/survey-user-list', 'OperatorController@SurveyUsers');
        Route::post('/request-survey', 'OperatorController@requestSurvey');
        Route::post('/user-detail', 'OperatorController@userdetail');

        Route::post('/survey-accept-reject', 'OperatorController@SurveyAcceptReject');

        Route::post('/custom-request-survey', 'OperatorController@CustomerequestSurvey');
        Route::post('/custom-survey-accept-reject', 'OperatorController@CustomeSurveyAcceptReject');

        Route::post('/cancel-survey', 'OperatorController@CancelSurvey');


        Route::post('/operator-custom-survey-accept', 'OperatorController@operatorCustomeSurveyAccept');
        Route::post('/custom-survey-user-list','OperatorController@CustomSurveyUsersList');


        Route::post('/edit-agent-insurvey', 'OperatorController@editagentinsurvey');

    
        Route::post('/survey-list', 'OperatorController@surveyListUpcomming');

        Route::post('/survey-all', 'OperatorController@surveyall');


        Route::post('/survey-list-past', 'OperatorController@surveyListPast');
        Route::post('/survey-details', 'OperatorController@surveyDetails');
        
        Route::post('/assign-surveyor-list', 'OperatorController@AssigntosurveyorList');
        Route::post('/assign-to-surveyor', 'OperatorController@AssignToSurveyor');
        Route::post('/change-start-date', 'OperatorController@ChangeStartDate');
        Route::post('/assign-to-operator', 'OperatorController@AssignToop');
        
        Route::post('/edit_profile', 'OperatorController@OperatorEditProfile');
        Route::post('/my-profile', 'OperatorController@OperatorProfile');

		Route::post('/notification_list','OperatorController@notificationList');	
		Route::post('/notification_delete','OperatorController@notificationDelete');	
        Route::post('/notification_read','OperatorController@notificationReadStatus');
        
        Route::post('/country-list','OperatorController@CountryList');
        Route::post('/user-survey-type','OperatorController@UserSurveyType');
        Route::post('/user-survey-port','OperatorController@UserSurveyPort');

	    Route::post('/user-calender-avail','OperatorController@addSurveyoravail');

        Route::post('/event-load','OperatorController@eventsload');
        Route::post('/report-submit','OperatorController@reportsubmit');
        Route::post('/report-accept','OperatorController@reportaccept');

        Route::post('/survey-filter-user','OperatorController@SurveyFilterUser');
        Route::post('/addrating','OperatorController@addrating');
        Route::post('/finance','OperatorController@Finance');
        Route::post('/report-survey-list','OperatorController@reportissuesurvey');
        Route::post('/report-issue-submit','OperatorController@reportissuepost');

        Route::post('/test','OperatorController@TestF');

        Route::post('/logout','OperatorController@logout');

        Route::post('/chat-email','OperatorController@chatEmail');

        Route::post('/chat-emailw','OperatorController@chatEmailw');

        
    });
});