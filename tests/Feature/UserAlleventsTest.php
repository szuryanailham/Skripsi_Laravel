<?php

namespace Tests\Feature;

use App\Models\Events;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserAllEventsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_all_events_successfully(): void
    {
        Events::factory()->count(5)->create();

      $response = $this->getJson('/api/user/events');


        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                 ])
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         '*' => ['id', 'title', 'slug', 'description', 'price', 'location', 'date', 'time', 'created_at', 'updated_at']
                     ]
                 ]);
    }



}
