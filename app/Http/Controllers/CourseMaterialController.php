<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Course $course)
    {
        return $course->materials;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Course $course)
    {
        error_log('in CourseMaterial Store ja');
        
        
        // if there's no directory for all COURSES, create a courses folder
        if (in_array("public/courses", Storage::directories('public')) == false) {
            error_log('creating the courses directory');
            Storage::makeDirectory("public/courses");
        }
        
        // if there's no directory for this specific course, create it
        if (in_array("public/courses/" . strval($course->id), Storage::directories('public/courses')) == false) {
            error_log('creating the course directory for course ' . strval($course->id));
            Storage::makeDirectory("public/courses/" . strval($course->id));
        }

        $path = $request->file('materialFile')->store("public/courses/" . strval($course->id));

        error_log($path);

        $course->materials()->create([
            'name' => $request->name,
            'description' => $request->description,
            'filepath' => $path,
        ]);

        return $course->materials;
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course, Material $material)
    {
        return $material;

        // FRONTEND-TODO: set up a download button and make a 
        // a hlink to the $material->filepath 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function edit(Material $material)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Material $material)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function destroy(Material $material)
    {
        //
    }
}
