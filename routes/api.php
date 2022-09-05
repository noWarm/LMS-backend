<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseMaterialController;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

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


// gotta add it here to let front talk to back
// header('Access-Control-Allow-Origin: http://localhost:3000');

Route::get('/', function (Request $request) {
    return response()->json([
        'message' => 'sup dude',
        'data' => 'some boobs for ya'
    ]);
});


Route::prefix('auth')->group(function () {
    Route::post('register', function (Request $request) {

        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'isStudent' => 'required',
        ]);


        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'isStudent' => $validatedData['isStudent'],
        ]);

        return response()->json('registered succesfully');
        // FRONTEND-TODO: i'd want to redirect them to login page actually
    })->name('register');

    // on this login route we don't need any middleware yet right ?
    Route::post('login', function (Request $request) {

        error_log('into the login function');
        $validatedData = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        error_log('passed the validation');

        $user = User::where('email', $validatedData['email'])->firstOrFail();
        // BACK-TODO: if fail, tell them the email is not registered, not wrong password

        if (Hash::check($validatedData['password'], $user->password)) {

            $token = $user->createToken('accessToken')->accessToken;
            return response()->json([
                'accessToken' => $token,
                'isStudent' => $user->isStudent
            ]);
        }
        return response()->json('invalid password');
        // FRONTEND-TODO: i'd actually want to redirect them back to GET / login page
    });

    Route::get('login', function (Request $request) {
        return 'log in first !!';
    })->name('login');
});


Route::middleware('auth:api')->group(function () {
    Route::prefix('user')->group(function () {

        Route::get('/', function (Request $request) {
            $user = $request->user();

            if ($user->isStudent) {
                return response()->json([
                    'user' => $user,
                    'learning_courses' => $user->teachingCourses()->get(),
                ]);
            } else {
                return response()->json([
                    'user' => $user,
                    'teaching_courses' => $user->teachingCourses()->get(),
                ]);
            }
        })->name('user');

        Route::get('/profile', function (Request $request) {
            return 'this will be the user profile';
        })->name('profile');
    });

    Route::get('/logout', function (Request $request) {
        $request->user()->token()->revoke();
        return "you are logged out. Thanks";
        // FRONTEND-TODO: i'd actually want to redirect them back to GET / login page
    });

    Route::post('/courses/{course}/register', [CourseController::class, 'register']);
    Route::post('/courses/{course}/drop', [CourseController::class, 'drop']);

    Route::resource('/courses', CourseController::class);
    Route::resource('courses.materials', CourseMaterialController::class);
});
