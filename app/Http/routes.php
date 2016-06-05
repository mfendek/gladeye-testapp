<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\Exception;
use App\User;

// login is our welcome screen
Route::any('/', function() {
    return Redirect::to('login');
});

// login screen
Route::get('login', ['middleware' => 'logged.in', function () {
    return View::make('login-screen', [
        'login_url' => URL::to('login'),
        'register_url' => URL::to('register'),
        'fb_login_url' => URL::to('login/fb'),
    ]);
}]);

// login action
Route::post('login', ['middleware' => 'logged.in', function () {
    // validate mandatory inputs
    foreach (['email', 'password'] as $field) {
        if (!Input::has($field)) {
            throw new Exception('mandatory field '.$field.' is missing');
        }
    }

    // case 1: valid user
    if (Auth::attempt(['email' => Input::get('email'), 'password' => Input::get('password'), 'active' => 1])) {
        return Redirect::to('user/list');
    }
    // case 2: login failed
    else {
        throw new Exception('failed to log in user');
    }

    return Redirect::to('login');
}]);

// register screen
Route::get('register', ['middleware' => 'logged.in', function () {
    return View::make('user-details', [
        'data' => User::defaultData(),
        'title' => 'Register new user',
        'label' => 'Register user',
        'force_passwords' => true,
        'back_url' => URL::to('login'),
        'create_url' => URL::to('register'),
    ]);
}]);

// register action
Route::post('register', ['middleware' => 'logged.in', function () {
    User::validateUserData(Input::all(), false);

    // check if email is unique
    $emailCount = DB::table('users')->where('email', Input::get('email'))->count();
    if ($emailCount > 0) {
        throw new Exception('specified email already belongs to registered user');
    }

    // create new user and assign data
    $user = new User;
    foreach (['first_name', 'last_name', 'email', 'group'] as $field) {
        $user->$field = Input::get($field);
    }

    // newly registered users are active and in user group
    $user->active = 1;
    $user->group = 'user';
    $user->password = Hash::make(Input::get('password'));

    $user->save();

    // send registration email
    // TODO mail settings need to be configured first
//    $data = [
//        'subject' => 'Welcome to testapp',
//        'from' => 'testapp@test.org',
//        'to' => $user->email,
//    ];
//
//    $message = 'Welcome '.$user->first_name.'! Your account is now ready for use.';
//    Mail::raw($message, function ($m) use ($data)  {
//        $m->from($data['from']);
//        $m->to($data['to']);
//        $m->subject($data['subject']);
//    });

    return Redirect::to('login');
}]);

// facebook login action
Route::get('login/fb', ['middleware' => 'logged.in', function () {
    // validate mandatory inputs
    foreach (['id', 'first_name', 'last_name'] as $field) {
        if (!Input::has($field)) {
            throw new Exception('mandatory field '.$field.' is missing');
        }
    }

    // attempt to find user by fb id
    $user = User::where('fb_id', Input::get('id'))->first();

    // create new user if necessary
    if (empty($user)) {
        $user = new User;

        $user->fb_id = Input::get('id');
        $user->first_name = Input::get('first_name');
        $user->last_name = Input::get('last_name');
        $user->active = 1;
        $user->group = 'user';

        $user->save();
    }

    Auth::login($user);

    return Redirect::to('user/list');
}]);

// logout action
Route::get('logout', ['middleware' => 'auth', function () {
    Auth::logout();

    return Redirect::to('login');
}]);

// user list screen
Route::get('user/list', ['middleware' => 'auth', function() {
    // list all users
    $users = DB::table('users')->orderBy('first_name', 'asc')->get(['id', 'first_name', 'last_name', 'email', 'active', 'fb_id', 'created_at']);

    // extract and format relevant data
    $data = array();
    foreach ($users as $user) {
        $data[] = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'active' => ($user->active) ? 'yes' : 'no',
            'fb_id' => $user->fb_id,
            'created_at' => date('d.M.Y', strtotime($user->created_at)),
        ];
    }

    return View::make('user-list', [
        'users' => $data,
        'edit_url' => URL::to('user/edit'),
        'delete_url' => URL::to('user/delete'),
        'add_url' => URL::to('user/add'),
        'logout_url' => URL::to('logout'),
        'advanced_access' => (Auth::user()->group == 'admin'),
    ]);
}]);

// user add screen
Route::get('user/add', ['middleware' => ['auth', 'admin'], function() {
    return View::make('user-details', [
        'data' => User::defaultData(),
        'title' => 'Add new user',
        'label' => 'Create user',
        'force_passwords' => true,
        'show_admin_data' => true,
        'back_url' => URL::to('user/list'),
        'create_url' => URL::to('user/create'),
    ]);
}]);

// user add action
Route::post('user/create', ['middleware' => ['auth', 'admin'], function() {
    User::validateUserData(Input::all());

    // check if email is unique
    $emailCount = DB::table('users')->where('email', Input::get('email'))->count();
    if ($emailCount > 0) {
        throw new Exception('specified email already belongs to registered user');
    }

    // create new user and assign data
    $user = new User;
    foreach (['first_name', 'last_name', 'email', 'group'] as $field) {
        $user->$field = Input::get($field);
    }

    $user->active = Input::has('active');
    $user->password = Hash::make(Input::get('password'));

    $user->save();

    return Redirect::to('user/edit/'.$user->id);
}]);

// user edit screen
Route::get('user/edit/{id}', ['middleware' => ['auth', 'admin'], function($id) {
    $user = User::findOrFail($id);

    return View::make('user-details', [
        'data' => [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'active' => $user->active,
            'group' => $user->group,
            'fb_id' => $user->fb_id,
        ],
        'display_fb_id' => true,
        'title' => 'Edit user',
        'label' => 'Update user',
        'show_admin_data' => true,
        'back_url' => URL::to('user/list'),
        'create_url' => URL::to('user/update').'/'.$user->id,
    ]);
}]);

// user edit action
Route::post('user/update/{id}', ['middleware' => ['auth', 'admin'], function($id) {
    $user = User::findOrFail($id);

    User::validateUserData(Input::all());

    // check if email is unique (omit current user)
    $emailCount = DB::table('users')->where([['email', Input::get('email')], ['id', '<>', $id]])->count();
    if ($emailCount > 0) {
        throw new Exception('specified email already belongs to registered user');
    }

    // update user data
    foreach (['first_name', 'last_name', 'email', 'group'] as $field) {
        $user->$field = Input::get($field);
    }

    $user->active = Input::has('active');

    // store new password if necessary
    if (Input::has('password')) {
        $user->password = Hash::make(Input::get('password'));
    }

    $user->save();

    return Redirect::to('user/edit/'.$id);
}]);

// user delete action
Route::get('user/delete/{id}', ['middleware' => ['auth', 'admin'], function($id) {
    $user = User::findOrFail($id);

    $user->delete();

    return Redirect::to('user/list');
}]);

// API login
Route::post('api/login', function() {
    // case 1: valid user
    if (Auth::attempt(['email' => Input::get('email'), 'password' => Input::get('password'), 'group' => 'admin'])) {
        // start new session and generate new API token
        $user = Auth::user();
        $user->api_token = str_random(60);
        $user->save();

        $data = [
            'token' => $user->api_token,
            'success' => true,
            'message' => 'user logged in',
        ];
    }
    // case 2: login failed
    else {
        $data = [
            'token' => '',
            'success' => false,
            'message' => 'failed to log in user',
        ];
    }

    return response(json_encode($data, JSON_PRETTY_PRINT))->header('Content-Type', 'application/json');
});

// API list users
Route::get('api/users', ['middleware' => 'auth:api', function() {
    // list all users
    $users = DB::table('users')->orderBy('id', 'asc')->get(['id', 'first_name', 'last_name', 'email', 'group', 'active', 'fb_id', 'created_at', 'updated_at']);

    // extract and format relevant data
    $data = array();
    foreach ($users as $user) {
        $data[] = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'active' => ($user->active) ? true : false,
            'group' => $user->group,
            'fb_id' => (!empty($user->fb_id)) ? $user->fb_id : '',
            'created_at' => date('Y-m-d', strtotime($user->created_at)),
            'updated_at' => date('Y-m-d', strtotime($user->updated_at)),
        ];
    }

    return response(json_encode($data, JSON_PRETTY_PRINT))->header('Content-Type', 'application/json');
}]);