<?php

/* ------------------------------------------------------------------------- */
// This file routes all requests sent to the public index.php                //
/* ------------------------------------------------------------------------- */



App::missing(function($exception)
{
    $title = 'Oops!';
    return Response::view('missing', array('title' => $title), 404);
});

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
App::error(function(MethodNotAllowedHttpException $exception)
{
    $title = 'Oops!';
    return Response::view('missing', array('title' => $title), 404);
});


/* ------------------------------------------------------------------------- */
///////                        General routing                          ///////
/* ------------------------------------------------------------------------- */

// Run a check to make sure that it is a GTP user asking for this info
Route::filter('isGTP', function($route, $request, $perms)
{
    $user = Teacher::find(Session::get('user'));
    if (empty($user)) $user = new Teacher;
    
    if ($user->permissions($perms)) {
        // return nothing so the default action occurs
    } else {
        Session::flash('message', 'You don\'t have sufficient permissions to access those tools.');
        return Redirect::to('T/login/');
    }
});

// Always check if it is a GTP user when dealing with workshop and feedback things
Route::when('FB/*', 'isGTP:fbinfo');
Route::when('R/*', 'isGTP:reports');

// Default routing
Route::get('/', function()
{
    if (Session::has('user')) {
        $u = Session::get('user');
        
        // If the user isn't 'gtp,' who has an id of 1
        if ($u != 1) {
            return Redirect::to('T/info/'.Session::get('user'));
        } else {
            return Redirect::to('WS/list');
        }
    } else {
        return Redirect::to('T/login');
    }
});



/* ------------------------------------------------------------------------- */
///////                        Teacher routing                          ///////
/* ------------------------------------------------------------------------- */

// Basic login page
Route::get('T/login', 'TeacherController@login');

// Set up session for new login, route to start page
Route::post('T/login', 'TeacherController@loginPost');

// Forget the session variables, and log out
Route::get('T/logout', 'TeacherController@logout');

// Pull up info on a teacher (with identikey), requires GTP auth
Route::get('T/info/{id?}', function($id = '')
{
    return App::make('TeacherController')->getTeacher($id);
});

// Post updates to a teacher from the Teacher Tool
Route::any('T/update', 'TeacherController@updateDB');

Route::get('T/find', array('before' => 'isGTP:tinfo', 'uses' => 'TeacherController@findTeacher'));

// Returns a JSON list of names for AJAX <datalist> updating
Route::post('T/names', 'TeacherController@searchNames');

Route::get('T/notes/{id}/{VTC}', function($id, $VTC)
{
    return App::make('TeacherController')->getNotes($id, $VTC);
});

// List feedback created for workshop $ws_id for presenter to view
Route::get('T/fblist/{ws_id}', function($ws_id)
{
    Session::forget('message');
    return App::make('FeedbackController')->feedbackList($ws_id, true);
});

// Pull info for a feedback for a presenter to view
Route::get('T/fbinfo/{id}', function($id)
{
    Session::forget('message');
    return App::make('FeedbackController')->getFeedback($id, true);
});



/* ------------------------------------------------------------------------- */
///////                        Workshop routing                        ////////
/* ------------------------------------------------------------------------- */

// Pull up a list of workshops (default GTP user view)
Route::match(array('GET', 'POST'), 'WS/list', array('before' => 'isGTP:any', 'uses' => 'WorkshopController@workshopList'));

// Pull info for a workshop (or create a new one if $id == 0)
Route::get('WS/info/{id?}', array('before' => 'isGTP:wsinfo', function($id = 0)
{
    return App::make('WorkshopController')->getWorkshop($id);
}));

// Update a workshop in the database
Route::post('WS/update/', 'WorkshopController@updateDB');

// Start attendance taking
Route::post('WS/attend/', array('before' => 'isGTP:wssignin', 'uses' => 'WorkshopController@attend'));

// Stop attendance taking
Route::get('WS/stopAttend/', array('before' => 'isGTP:wssignin', 'uses' => 'WorkshopController@stopAttend'));

// Delete an attendance record
Route::post('WS/delAtt/', array('before' => 'isGTP:attendance', 'uses' => 'WorkshopController@remAttendance'));

// Store WS_id in session, and direct to teacher search for adding to the WS attendance
Route::get('WS/addAtt/{id}', array('before' => 'isGTP:attendance', function($id)
{
    return App::make('WorkshopController')->addAttendance($id);
}));

// Direct to the Teacher Controller for DB update with new attendance record
Route::post('WS/insAtt/', array('before' => 'isGTP:attendance', 'uses' => 'TeacherController@addAttendance'));

Route::post('WS/presAdd/', array('before' => 'isGTP:wsinfo', 'uses' => 'WorkshopController@addPresenter'));

Route::post('WS/attCred/', array('before' => 'isGTP:attendance', 'uses' => 'WorkshopController@editAttendance'));

// Deactivate a workshop
Route::post('WS/switchWSStatus/', array('before' => 'isGTP:wsinfo', 'uses' => 'WorkshopController@switchWorkshopStatus'));

//Switch to active/inactive workshops
Route::get('WS/switchWSList', array('before' => 'isGTP:wsinfo', 'uses' => 'WorkshopController@switchWorkshopList'));



/* ------------------------------------------------------------------------- */
///////                        Feedback routing                         ///////
/* ------------------------------------------------------------------------- */

// List feedback created for workshop $ws_id
Route::get('FB/list/{ws_id}', function($ws_id)
{
    return App::make('FeedbackController')->feedbackList($ws_id);
});

// If there isn't a ws_id supplied, redirect to the WS list
Route::get('FB/list/', function()
{
    Session::flash('message', 'Please choose the workshop you would like to enter feedback for.');
    return Redirect::to('WS/list');
});

// Pull info for a feedback (or start a new one if $id == 0)
Route::get('FB/info/{id?}', function($id = 0)
{
    return App::make('FeedbackController')->getFeedback($id);
});

// Post updates to the database
Route::post('FB/update', 'FeedbackController@updateDB');

// Pull up the FB Tool if we know which workshop we're working with, if not, let FB/list deal with it
Route::match(array('GET', 'POST'), 'FB/edit/{fb_id?}', function($fb_id = 0)
{
    if (Session::has('wsfb_id')) {
        Session::flash('editFB', 'true');
        return Redirect::to('FB/info/'.$fb_id);
    } else {
        return Redirect::to('FB/list/');
    }
});

// Delete a FB
Route::post('FB/rem/', 'FeedbackController@deleteFB');



/* ------------------------------------------------------------------------- */
///////                         Reports routing                         ///////
/* ------------------------------------------------------------------------- */

// Get a list of the available reports
Route::get('R/list/', 'ReportsController@reportList');

// Edit the list of departments
Route::get('R/dept/edit/', 'ReportsController@deptEdit');
Route::post('R/dept/add/', 'ReportsController@deptAdd');
Route::post('R/dept/rem/', 'ReportsController@deptRem');

// Pull up info on a department (list of teachers)
Route::match(array('GET', 'POST'), 'R/dept/{d_id}', function($d_id)
{
    return App::make('ReportsController')->deptInfo($d_id);
});

// Pull up a list of departments with links to deptInfo
Route::get('R/dept/', 'ReportsController@deptList');

// Will eventually give metrics on certifications, when we know what they should be
Route::match(array('GET', 'POST'), 'R/cert/', 'ReportsController@certList');

Route::get('R/series/edit/', 'ReportsController@seriesEdit');
Route::post('R/series/add/', 'ReportsController@seriesAdd');
Route::post('R/series/rem/', 'ReportsController@seriesRem');
Route::match(array('GET', 'POST'), 'R/series/{s_id}', function($s_id)
{
    return App::make('ReportsController')->seriesInfo($s_id);
});
Route::match(array('GET', 'POST'), 'R/series/', 'ReportsController@seriesList');

Route::match(array('GET', 'POST'), 'R/feedback/', 'ReportsController@fbReports');

Route::match(array('GET', 'POST'), 'R/leads/', 'ReportsController@leadsList');
Route::match(array('GET', 'POST'), 'R/VTCs/', 'ReportsController@VTCList');

Route::match(array('GET', 'POST'), 'R/log/', 'ReportsController@actionList');

Route::match(array('GET', 'POST'), 'R/suggestions/', 'ReportsController@suggestionList');



/* ------------------------------------------------------------------------- */
///////                           Testing Page                          ///////
/* ------------------------------------------------------------------------- */

// I use this to test php code in the closure.  Will be commented out in production.
Route::match(array('GET', 'POST'), 'test', function()
{
    $ws = Teacher::find(2);
    
    return var_dump($ws);
});
