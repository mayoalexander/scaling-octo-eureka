<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    protected $fillable = [
        'label',
        'parent_id',
    ];

    protected $casts = [
        'parent_id' => 'integer',
    ];

    /**
     * Get the parent node.
     */
    public function parent()
    {
        return $this->belongsTo(Tree::class, 'parent_id');
    }

    /**
     * Get all child nodes.
     */
    public function children()
    {
        return $this->hasMany(Tree::class, 'parent_id');
    }

    /**
     * Get all descendants recursively.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Scope query to get only root nodes (nodes without parents).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Convert tree to nested array format for API response.
     */
    public function toNestedArray()
    {
        $children = $this->children()->get();
        
        return [
            'id' => $this->id,
            'label' => $this->label,
            'children' => $children->map(function ($child) {
                return $child->toNestedArray();
            })->toArray(),
        ];
    }
}
