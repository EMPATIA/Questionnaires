<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

/**
 * Route for the requests of Event Schedules methods
 */

Route::put('eventSchedule/{key}/updateDetails', 'EventSchedulesController@updateDetails');
Route::put('eventSchedule/{key}/updatePeriods', 'EventSchedulesController@updatePeriods');
Route::get('eventSchedule/list', 'EventSchedulesController@index');
Route::resource('eventSchedule', 'EventSchedulesController', ['only' => ['show', 'store', 'update', 'destroy']]);

/**
 * Route for the requests of Event Participants methods
 */

Route::get('esParticipant/list', 'EsParticipantsController@index');
Route::post('esParticipant/{key}', 'EsParticipantsController@store');
Route::post('esParticipant/anonymousStore/{key}', 'EsParticipantsController@anonymousStore');
Route::put('esParticipant/anonymousUpdate/{key}', 'EsParticipantsController@anonymousUpdate');
Route::delete('esParticipant/anonymousDelete/{key}/', 'EsParticipantsController@anonymousDestroy');
Route::resource('esParticipant', 'EsParticipantsController', ['only' => ['show', 'update', 'destroy']]);


/**
 * Route for the requests of Forms methods
 */

Route::get('form/list', 'FormsController@index');
Route::get('form/{formKey}/getQuestionnaire', 'FormsController@getQuestionnaire');
Route::get('form/{formKey}/getAnswers', 'FormsController@getAnswers');
Route::get('form/{formKey}/showByUser/{userKey}', 'FormsController@showByUser');
Route::get('form/{formKey}/construction', 'FormsController@formConstruction');
Route::post('form/{formKey}/statisticsByForm', 'FormsController@statisticsByForm');
Route::get('form/{formKey}/statisticsByFormReply/{form_reply_key}', 'FormsController@statisticsByFormReply');
Route::get('form/{formKey}/statistics', 'FormsController@statistics');
Route::get('form/{formKey}/completed', 'FormsController@completed');
Route::get('form/{formKey}/completedUserList', 'FormsController@completedUserList');
Route::get('form/{formKey}/answers', 'FormsController@formAnswers');
Route::get('form/{formKey}/questionGroups', 'FormsController@questionGroupList');
Route::resource('form', 'FormsController', ['only' => ['show', 'store', 'update', 'destroy']]);

route::post('form/getQuestionnaires', 'FormsController@getQuestionnaires');
/**
 * Route for the requests of Question Groups methods
 */

Route::get('questionGroup/list', 'QuestionGroupsController@index');
Route::get('questionGroup/dependencies/{questionGroupKey}', 'QuestionGroupsController@getQuestionDependencies');
Route::put('questionGroup/updatePositions', 'QuestionGroupsController@updatePosition');
Route::get('questionGroup/{questionGroupId}/questionsInfo', 'QuestionGroupsController@questionInfo');
Route::get('questionGroup/{questionGroupId}/question/list', 'QuestionGroupsController@questionList');
Route::resource('questionGroup', 'QuestionGroupsController', ['only' => ['show', 'store', 'update', 'destroy']]);

/**
 * Route for the requests of Question methods
 */

Route::get('question/list', 'QuestionsController@index');
Route::get('question/dependencies/{questionKey}', 'QuestionsController@getQuestionDependencies');
Route::put('question/updatePositions', 'QuestionsController@updatePosition');
Route::put('question/changeGroup', 'QuestionsController@changeGroup');
Route::get('question/{questionKey}/questionOption/list', 'QuestionsController@questionOptionList');
Route::get('question/{questionKey}/reuse', 'QuestionsController@reuse');
Route::get('question/getReuseOptions/{formKey}', 'QuestionsController@getReuseOptions');
Route::resource('question', 'QuestionsController', ['only' => ['show', 'store', 'update', 'destroy']]);

/**
 * Route for the requests of Question Option methods
 */

Route::get('questionOption/list', 'QuestionOptionsController@index');
Route::put('questionOption/updatePositions', 'QuestionOptionsController@updatePosition');
Route::post('questionOption/duplicateReuseOptions', 'QuestionOptionsController@duplicateReuseOptions');
Route::resource('questionOption', 'QuestionOptionsController', ['only' => ['show', 'store', 'update', 'destroy']]);


/**
 * Route for the requests of Icons methods
 */

Route::get('icon/list', 'IconsController@index');
Route::resource('icon', 'IconsController', ['only' => ['show', 'store', 'update', 'destroy']]);

/**
 * Route for the requests of Question Type methods
 */

Route::get('questionType/list', 'QuestionTypesController@index');
Route::resource('questionType', 'QuestionTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);

/**
 * Route for the requests of Form Replies methods
 */

Route::get('formReply/list/{form_key}', 'FormRepliesController@index');
Route::get('formReply/verifyReply/{id}', 'FormRepliesController@verifyReply');
Route::resource('formReply', 'FormRepliesController', ['only' => ['show', 'store', 'update', 'destroy']]);

/**
 * Route for the requests of Form Reply Answers methods
 */

Route::get('formReplyAnswer/{question_id}/listAnswers', 'FormReplyAnswersController@listAnswers');
Route::get('formReplyAnswer/list', 'FormReplyAnswersController@index');
Route::resource('formReplyAnswer', 'FormReplyAnswersController', ['only' => ['show', 'store', 'update', 'destroy']]);

/*
|--------------------------------------------------------------------------
| Form Configurations Routes
|--------------------------------------------------------------------------
|
| This route group applies the «Form Configurations» group to every route
| it contains.
|
*/

Route::get('formConfigurations/list', 'FormConfigurationsController@index');
Route::get('formConfigurations/{formConfigurationKey}/edit', 'FormConfigurationsController@edit');
Route::resource('formConfigurations', 'FormConfigurationsController', ['only' => ['show', 'store', 'update', 'destroy']]);


Route::group(['middleware' => ['web']], function () {
    //
});
