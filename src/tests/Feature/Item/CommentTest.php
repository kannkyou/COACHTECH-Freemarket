<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class CommentTest extends TestCase

{
    use RefreshDatabase;

    public function test_logged_in_user_can_comment()
    {
        $user = User::factory()->create([
            'postal_code'=>'123',
            'address'=>'Tokyo',
            'email_verified_at'=>now()
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user)
            ->post("/item/{$item->id}/comments",[
                'comment'=>'hello'
            ]);

        $this->assertDatabaseHas('comments',[
            'comment'=>'hello'
        ]);
    }

    public function test_guest_cannot_comment()
    {
        $item = Item::factory()->create();

        $this->post("/item/{$item->id}/comments",[
            'comment'=>'hello'
        ])
        ->assertRedirect('/login');
    }

    public function test_comment_validation()
    {
        $user = User::factory()->create([
            'postal_code'=>'123',
            'address'=>'Tokyo',
            'email_verified_at'=>now()
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user)
            ->post("/item/{$item->id}/comments",[
                'comment'=>str_repeat('a',256)
            ])
            ->assertSessionHasErrors();
    }
}
