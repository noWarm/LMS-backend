<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $user = Auth::user();
        //IF YOU ARE A STUDENT, SHOW ALL COURSES NAME, EACH WITH REGISTERED ?
        if ($user->isStudent) {

            error_log('hi there student');
            $courses = Course::all();
            
            // error_log(gettype($courses)); it's an object
            
            $courses_with_status = $courses->map(function($item) use ($user) {
                error_log("hey i'm in map");
                error_log($item->learningUsers->contains($user));
                return [$item->id => $item->learningUsers->contains($user)];
            });

            return $courses_with_status;
        }   
         // IF YOU ARE A TEACHER, SHOW ALL YOUR TEACHING COURSES
        else {
            return $user->teachingCourses()->get();
        }
    }

    public function register(Request $request, Course $course) 
    {   
        $user = Auth::user();
        if ($user->isStudent == false) {
            return response('teacher are not allowed to register yo!', 403);
        }
        if ($course->learningUsers->contains($user)){
            return response('already registerd dude!', 403); // is a teacher or already registered, should be forbidden to POST to this link
        }
        $user->learningCourses()->attach($course);
        return $user->learningCourses()->get();
        // FRONTEND-TODO: redirect the user to the course page to start learning
    }

    public function drop(Request $request, Course $course) 
    {   
        $user = Auth::user();
        if ($user->isStudent == false) {
            return response('teacher are not allowed to drop yo!', 403);
        }
        if ($course->learningUsers->contains($user) == false){
            return response('has not registered yet dude!', 403); // is a teacher or already registered, should be forbidden to POST to this link
        }

        $user->learningCourses()->detach($course);
        return $user->learningCourses()->get();
        // FRONTEND-TODO: redirect the user to the his dashboard or market place
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // FRONTEND-TODO: student are also not allowed to access this link too
        return 'FRONTEND-TODO: i will give you a form here. For now just POST';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        // $this->authorize('create', Course::class);
        if ($request->user()->cannot('create', Course::class)) {
            // abort(403);
            return 'you are just a kid. you are not ALLOWED to make your own course !';
        }
        
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);
        
        error_log("in the store method");
        
        // add the course to the database
        $course =  Course::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'password' => Hash::make($request->password),
        ]);
        
        error_log("successfully created course");
        
        // add the course to that teacher
        $user = Auth::user();
        $user->teachingCourses()->attach($course);

        return response()->json([
            'message' => '',
            'data' => $user->teachingCourses,
        ], 200); // use this!!!!!!!!
        //format universally!!!!

        // return $user->teachingCourses()->get(); // note that we can't return a RELATIONSHIP

        // FRONTEND-TODO: redirect the teacher to his dashboard of courses
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        return $course;

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        $course->delete();

        // $user = Auth::user();

        // return $user->teachingCourses()->get();

        return redirect()->route('user');
        
    }
}
