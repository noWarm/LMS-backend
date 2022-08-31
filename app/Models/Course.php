<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function learningUsers() {
        return $this->belongsToMany(User::class,'user_learning_intermediate');
    }
    
    public function teachingUsers() {
        return $this->belongsToMany(User::class,'user_teaching_intermediate');
    }

    public function materials() {
        return $this->hasMany(Material::class);
    }
}
