<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CurrencyType;
use App\Models\TarfinCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TarfinCardTransactionControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        Http::fake([
            'http://you-should-mock-this-job' => Http::response([], 200),
        ]);
    }

    /**
     * @test
     */
    public function a_customer_can_create_a_tarfin_card_transaction(): void
    {
        $user = User::factory()->create();
        $tarfinCard = $user->tarfinCards()
            ->save(TarfinCard::factory()->active()->make());

        $this->actingAs($user)
            ->postJson(route('tarfin-cards.tarfin-card-transactions.store', $tarfinCard), [
                'amount' => 100,
                'currency_code' => CurrencyType::EUR,
            ])->assertCreated();

        $this->assertDatabaseHas('tarfin_card_transactions', [
            'tarfin_card_id' => $tarfinCard->id,
            'amount' => 100,
            'currency_code' => CurrencyType::EUR,
        ]);
    }

    /**
     * @test
     */
    public function a_customer_can_not_create_a_tarfin_card_transaction_for_a_tarfin_card_of_another_customer(): void
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
    public function a_customer_can_see_a_tarfin_card_transaction(): void
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
    public function a_customer_can_not_see_a_tarfin_card_transaction_for_a_tarfin_card_of_another_customer(): void
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
    public function a_customer_can_list_tarfin_card_transactions(): void
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
    public function a_customer_can_not_list_tarfin_card_transactions_for_a_tarfin_card_of_another_customer(): void
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
