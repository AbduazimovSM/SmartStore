<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reference;

class ReferenceController extends Controller
{

public function index()
{
    $references = Reference::all();

    return response()->json([
        'success' => true,
        'message' => 'Успешно получили данные!',
        'data' => $references
    ], 200);
}


    public function store(Request $request)
    {
        
    }


    public function show(string $id)
    {
        
    }


    public function update(Request $request, string $id)
    {
        
    }


    public function destroy(string $id)
    {
        
    }
}
