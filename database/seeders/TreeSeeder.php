<?php

namespace Database\Seeders;

use App\Models\Tree;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create root node
        $root = Tree::create([
            'label' => 'root',
            'parent_id' => null,
        ]);

        // Create bear node (child of root)
        $bear = Tree::create([
            'label' => 'bear',
            'parent_id' => $root->id,
        ]);

        // Create cat node (child of bear)
        $cat = Tree::create([
            'label' => 'cat',
            'parent_id' => $bear->id,
        ]);

        // Create frog node (child of root)
        $frog = Tree::create([
            'label' => 'frog',
            'parent_id' => $root->id,
        ]);
    }
}
