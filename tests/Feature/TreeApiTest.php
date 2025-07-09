<?php

namespace Tests\Feature;

use App\Models\Tree;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TreeApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test GET /api/tree returns all trees in nested format.
     */
    public function test_get_tree_returns_nested_structure(): void
    {
        // Create test data
        $root = Tree::create(['label' => 'root', 'parent_id' => null]);
        $bear = Tree::create(['label' => 'bear', 'parent_id' => $root->id]);
        $cat = Tree::create(['label' => 'cat', 'parent_id' => $bear->id]);
        $frog = Tree::create(['label' => 'frog', 'parent_id' => $root->id]);

        $response = $this->get('/api/tree');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    [
                        'id',
                        'label',
                        'children' => [
                            [
                                'id',
                                'label',
                                'children' => [
                                    [
                                        'id',
                                        'label',
                                        'children'
                                    ]
                                ]
                            ],
                            [
                                'id',
                                'label',
                                'children'
                            ]
                        ]
                    ]
                ]);

        $data = $response->json();
        $this->assertEquals('root', $data[0]['label']);
        $this->assertEquals('bear', $data[0]['children'][0]['label']);
        $this->assertEquals('cat', $data[0]['children'][0]['children'][0]['label']);
        $this->assertEquals('frog', $data[0]['children'][1]['label']);
    }

    /**
     * Test POST /api/tree creates a new node.
     */
    public function test_post_tree_creates_new_node(): void
    {
        $root = Tree::create(['label' => 'root', 'parent_id' => null]);
        $bear = Tree::create(['label' => 'bear', 'parent_id' => $root->id]);
        $cat = Tree::create(['label' => 'cat', 'parent_id' => $bear->id]);

        $response = $this->post('/api/tree', [
            'label' => "cat's child",
            'parentId' => $cat->id
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id',
                    'label',
                    'parent_id',
                    'created_at',
                    'updated_at'
                ]);

        $data = $response->json();
        $this->assertEquals("cat's child", $data['label']);
        $this->assertEquals($cat->id, $data['parent_id']);

        // Verify the node was created in the database
        $this->assertDatabaseHas('trees', [
            'label' => "cat's child",
            'parent_id' => $cat->id
        ]);
    }

    /**
     * Test POST /api/tree creates a root node when no parentId is provided.
     */
    public function test_post_tree_creates_root_node_without_parent(): void
    {
        $response = $this->post('/api/tree', [
            'label' => 'new root'
        ]);

        $response->assertStatus(201);
        $data = $response->json();
        $this->assertEquals('new root', $data['label']);
        $this->assertNull($data['parent_id']);
    }

    /**
     * Test POST /api/tree validation errors.
     */
    public function test_post_tree_validation_errors(): void
    {
        // Test missing label
        $response = $this->post('/api/tree', []);
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'error',
                    'messages' => [
                        'label'
                    ]
                ]);

        // Test invalid parent ID
        $response = $this->post('/api/tree', [
            'label' => 'test',
            'parentId' => 999
        ]);
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'error',
                    'messages' => [
                        'parentId'
                    ]
                ]);
    }

    /**
     * Test GET /api/tree returns empty array when no trees exist.
     */
    public function test_get_tree_returns_empty_array_when_no_trees(): void
    {
        $response = $this->get('/api/tree');
        $response->assertStatus(200)
                ->assertJson([]);
    }

    /**
     * Test multiple root nodes are returned correctly.
     */
    public function test_get_tree_returns_multiple_root_nodes(): void
    {
        $root1 = Tree::create(['label' => 'root1', 'parent_id' => null]);
        $root2 = Tree::create(['label' => 'root2', 'parent_id' => null]);
        
        $child1 = Tree::create(['label' => 'child1', 'parent_id' => $root1->id]);
        $child2 = Tree::create(['label' => 'child2', 'parent_id' => $root2->id]);

        $response = $this->get('/api/tree');
        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertCount(2, $data);
        $this->assertEquals('root1', $data[0]['label']);
        $this->assertEquals('root2', $data[1]['label']);
    }
}
