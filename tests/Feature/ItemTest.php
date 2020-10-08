<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Location;

class ItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetAllItem()
    {
        $response = $this->get('/api/items/all');

        $response->assertStatus(200);
    }

    public function testGetSingleItemPass()
    {
        $item = Item::factory()->create();
        $response = $this->get('/api/item/' . $item->id);
        $response->assertStatus(200);
    }

    public function testGetSingleItemFail()
    {
        $response = $this->get('/api/item/' . 'x');
        $response->assertStatus(422);
    }

    public function testCreateNewItemPass()
    {
        $item = Item::factory()->make()->toArray();
        $item['location'] = Location::factory()->make()->toArray();
        $response = $this->post('/api/item/create', $item);
        $response->assertStatus(201);
    }

    public function testCreateNewItemFail()
    {
        $response = $this->post('/api/item/create', []);
        $response->assertStatus(302);
    }

    public function testUpdateAnItemPass()
    {
        $item = Item::factory()->create()->toArray();
        $item['location'] = Location::factory()->create([
            'item_id' => $item['id'],
        ]);
        $response = $this->put('/api/item/update/' . $item['id'], $item);
        $response->assertStatus(200);
    }

    public function testUpdateAnItemFail()
    {
        $response = $this->put('/api/item/update/' . 'x', []);
        $response->assertStatus(302);
    }

    public function testDeleteAnItemPass()
    {
        $item = Item::factory()->create();
        $response = $this->delete('/api/item/delete/' . $item->id);
        $response->assertStatus(200);
    }

    public function testDeleteAnItemFail()
    {
        $response = $this->delete('/api/item/delete/' . 'x');
        $response->assertStatus(422);
    }

    public function testBookingPass()
    {
        $item = Item::factory()->create();
        $response = $this->patch('/api/item/book/' . $item->id);
        $response->assertStatus(200);
    }

    public function testBookingFail()
    {
        $response = $this->patch('/api/item/book/' . 'x');
        $response->assertStatus(422);
    }
}
