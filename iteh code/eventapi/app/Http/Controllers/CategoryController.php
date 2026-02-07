<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // This method will return a list of all categories in the database.
    // It uses the Category model to retrieve all records and returns them as a JSON response.
    // The response will have a status code of 200 (OK) and the data will be in the form of a JSON object with a "data" key containing the list of categories.
    // GET metoda za dohvatanje svih kategorija

    // vraca sve kategorije iz baze podataka i vraca ih kao JSON odgovor SVE!!!
    public function index()
    {
        //
       // return Category::all();
    return Category::all();

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // This method will handle the creation of a new category. It expects a Request object that contains the data for the new category.
    // The method will validate the incoming request data to ensure that it meets the required criteria (e.g., name is required and must be a string).
    // If the validation passes, it will create a new category record in the database using the Category model and return a JSON response with the
    // newly created category data and a status code of 201 (Created). If the validation fails, it will return a JSON response with the validation errors and a status code of 422 (Unprocessable Entity).
    // POST metoda za kreiranje nove kategorije
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'opis' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
            'errors' => $validator->errors()], 
            422);
        }


        $data = $validator->validated();
        $category = Category::create($data);
        return response()->json([
            $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */

    // This method will display a specific category based on the provided Category model instance. It will return a JSON response with the category data and a status code of 200 (OK).
    // GET metoda za dohvatanje jedne kategorije po ID-u
    // Ova metoda će prikazati određenu kategoriju na osnovu pružene instance modela Category. Vratit će JSON odgovor s podacima kategorije i statusnim kodom 200 (OK).
    // GET metoda za dohvatanje jedne kategorije po ID-u    ---------- SAMO JEDNE KATEGORIJE!!! ------
    public function show($id)
    {
   // return Category::where('idCategory', $id)->firstOrFail();
   return new CategoryResource(Category::where('idCategory', $id)->firstOrFail());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // This method will handle the updating of an existing category. It expects a Request object that contains the updated data for the category and a Category model instance that represents the category to be updated.
    // The method will validate the incoming request data to ensure that it meets the required criteria (
    // e.g., name is required and must be a string). If the validation passes, it will update the category record in the database using the Category model and return a JSON response with the updated category data and a status code of 200 (OK). If the validation fails, it will return a JSON response with the validation errors and a status code of 422 (Unprocessable Entity).
    // PUT metoda za ažuriranje postojeće kategorije
    // Ova metoda će se baviti ažuriranjem postojeće kategorije. Očekuje objekt Request koji sadrži ažurirane podatke za kategoriju i instancu modela Category koja predstavlja kategoriju koja će biti ažurirana. Metoda će validirati dolazne podatke zahtjeva kako bi osigurala da zadovoljavaju potrebne kriterije (npr., ime je obavezno i mora biti string). Ako validacija prođe, ažurirat će zapis kategorije u bazi podataka koristeći model Category i vratit će JSON odgovor s ažuriranim podacima kategorije i statusnim kodom 200 (OK). Ako validacija ne uspije, vratit će JSON odgovor s greškama validacije i statusnim kodom 422 (Unprocessable Entity).
    public function update(Request $request, $id)
    {
        //
        $category= Category::where('idCategory', $id)->firstOrFail();
        if(!$category){
            return response()->json([
                'message' => 'Kategorija nije pronađena'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'opis' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
            'errors' => $validator->errors()], 
            422);
        }
        
        $data = $validator->validated();
        $category->update($data);
        $category->refresh(); // da vrati sveže iz baze

        //$category->update($data);
        return response()->json([
            $category
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
        // This method will handle the deletion of a category. It expects a Category model instance that represents the category to be deleted.
    // The method will delete the category record from the database using the Category model and return a JSON response with a message indicating that the category has been deleted and a status code of 200 (OK). If the deletion fails (e.g., due to database constraints), it will return a JSON response with an error message and a status code of 500 (Internal Server Error).
    // DELETE metoda za brisanje kategorije
    // Ova metoda će se baviti brisanjem kategorije. Očekuje instancu modela Category koja predstavlja kategoriju koja će biti obrisana. Metoda će izbrisati zapis kategorije iz baze podataka koristeći model Category i vratit će JSON odgovor s
    public function destroy($id)
    {
        //
        $category = Category::where('idCategory', $id)->first();
        //$category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Kategorija nije pronađena'
            ], 404);
        }

        $category->delete();
        return response()->json([
            'message' => 'Kategorija je obrisana'
        ], 200);
    }
}
