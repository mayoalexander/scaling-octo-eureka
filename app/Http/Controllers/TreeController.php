<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TreeController extends Controller
{
    /**
     * Display a listing of all trees.
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Get all root nodes (nodes without parents) and their descendants
            $trees = Tree::roots()->get();
            
            $result = $trees->map(function ($tree) {
                return $tree->toNestedArray();
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve trees',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created tree node.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'label' => 'required|string|max:255',
                'parentId' => 'nullable|integer|exists:trees,id'
            ]);

            // Create the new tree node
            $tree = Tree::create([
                'label' => $validated['label'],
                'parent_id' => $validated['parentId'] ?? null,
            ]);

            return response()->json([
                'id' => $tree->id,
                'label' => $tree->label,
                'parent_id' => $tree->parent_id,
                'created_at' => $tree->created_at,
                'updated_at' => $tree->updated_at,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create tree node',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
