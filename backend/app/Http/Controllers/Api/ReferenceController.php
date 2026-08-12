<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reference;

class ReferenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Reference::query();

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        $perPage = $request->integer('per_page', 10);

        $sortField = $request->query('sort_field', 'name');
        $sortOrder = $request->query('sort_order', 'asc');

        $references = $query->orderBy($sortField, $sortOrder)->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Успешно получили данные!',
            'data' => $references
        ], 200);
    }


    public function store(Request $request){
        $validated = $request->validate([
            'type'        => 'required|string|max:255',
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50',
            'short_name'  => 'nullable|string|max:50',
            'parent_id'   => 'nullable|integer',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);
        $reference = Reference::create($validated);

        return response()->json([
            'message' => 'Запись успешно добавлена',
            'data' => $reference,
        ], 201);
    }


    public function update(Request $request, string $id){
        $validated = $request->validate([
            'type'        => 'required|string|max:255',
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50',
            'short_name'  => 'nullable|string|max:50',
            'parent_id'   => 'nullable|integer',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

    $reference = Reference::findOrFail($id);
    $reference->update($validated);

    return response()->json([
        'message' => 'Запись успешно изменена',
        'data' => $reference,
    ]);
        
    }


    public function destroy($id){
        $reference = Reference::findOrFail($id);
        $reference->delete();

        return response()->json([
            'message' => 'Запись успешно удалена'
        ]);

    }

    public function destroySelected(Request $request){
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:references,id'],
        ]);

        $deletedCount = Reference::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'message' => "Удалено записей: {$deletedCount}",
            'deleted_count' => $deletedCount
        ], 200);
    }
}