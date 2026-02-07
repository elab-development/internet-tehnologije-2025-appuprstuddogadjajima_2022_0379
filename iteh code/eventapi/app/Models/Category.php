<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';


    // The primary key for the categories table is 'idCategory', which is an auto-incrementing integer. The $fillable property specifies that the 'name' and 'description' fields can be mass assigned when creating or updating a category. The eventovi() method defines a one-to-many relationship between the Category model and the Event model, indicating that each category can have multiple associated events.
    // nismo stavili u drugim kolonama, ali nije hteo destroy() da radi bez ovih postavki
    protected $primaryKey = 'idCategory';
    public $incrementing = true;
    protected $keyType = 'int';

     /** @use HasFactory<\Database\Factories\CategoryFactory> */
    protected $fillable = [
        "name",
        "opis",
    ];

    protected $casts = [
        //
    ];

      public function eventovi()
    {
        return $this->hasMany(Event::class, 'idCategory');
    }
}
