<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\TarfinCard;
use Illuminate\Support\Facades\Request;
use App\Http\Resources\TarfinCardResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarfinCardControllerTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @test
     */
    public function a_customer_can_create_a_tarfin_card(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('tarfin-cards.store'), [
                'type' => 'American Express',
            ])->assertCreated();

        $this->assertDatabaseHas('tarfin_cards', [
            'user_id' => $user->id,
            'type' => 'American Express',
        ]);
    }

    /**
     * @test
     */
    public function a_customer_can_not_create_an_invalid_tarfin_card(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('tarfin-cards.store'), [
                'type' => 'INVALID CARD TYPE FOR TEST PURPOSES',
            ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function a_customer_can_see_a_tarfin_card(): void
    {
        $user = User::factory()->create();
        $tarfinCard = $user->tarfinCards()->save(
            TarfinCard::factory()->make([
                'type' => 'American Express',
            ])
        );
        $resource = new TarfinCardResource($tarfinCard);
        $request = Request::create(route('tarfin-cards.show', $tarfinCard));

        $response = $this->actingAs($user)
            ->getJson(route('tarfin-cards.show', $tarfinCard));

        $response->assertOk();
        $this->assertSameSize($resource->toArray($request), $response->json('data'));
    }

    /**
     * @test
     */
    public function a_customer_can_not_see_a_tarfin_card_of_another_customer(): void
    {
        $customer = User::factory()->create();
        $anotherCustomer = User::factory()->create();
        $tarfinCard = $anotherCustomer->tarfinCards()->save(
            TarfinCard::factory()->make([
                'type' => 'American Express',
            ])
        );

        $this->actingAs($customer)
            ->getJson(route('tarfin-cards.show', $tarfinCard))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function a_customer_can_list_tarfin_cards(): void
    {
        // 1. Arrange 🏗
        // TODO:

        // 2. Act 🏋🏻‍
        // TODO:

        // 3. Assert ✅
        // TODO:
    }

    /**
     * @test
     */
    public function a_customer_can_activate_the_tarfin_card(): void
    {
        // 1. Arrange 🏗
        // TODO:

        // 2. Act 🏋🏻‍
        // TODO:

        // 3. Assert ✅
        // TODO:
    }

    /**
     * @test
     */
    public function a_customer_can_deactivate_the_tarfin_card(): void
    {
        // 1. Arrange 🏗
        // TODO:

        // 2. Act 🏋🏻‍
        // TODO:

        // 3. Assert ✅
        // TODO:
    }

    /**
     * @test
     */
    public function a_customer_can_delete_a_tarfin_card(): void
    {
        // 1. Arrange 🏗
        // TODO:

        // 2. Act 🏋🏻‍
        // TODO:

        // 3. Assert ✅
        // TODO:
    }

    // THE MORE TESTS THE MORE POINTS 🏆
}
