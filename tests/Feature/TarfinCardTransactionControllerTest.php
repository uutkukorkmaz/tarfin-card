<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CurrencyType;
use App\Http\Resources\TarfinCardTransactionResource;
use App\Models\TarfinCard;
use App\Models\TarfinCardTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
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
        $customer = User::factory()->create();
        $anotherCustomer = User::factory()->create();
        $tarfinCard = $anotherCustomer->tarfinCards()
            ->save(TarfinCard::factory()->active()->make());

        $this->actingAs($customer)
            ->postJson(route('tarfin-cards.tarfin-card-transactions.store', $tarfinCard), [
                'amount' => 100,
                'currency_code' => CurrencyType::EUR,
            ])->assertForbidden();
    }

    /**
     * @test
     */
    public function a_customer_can_see_a_tarfin_card_transaction(): void
    {
        $user = User::factory()->create();
        $tarfinCard = $user->tarfinCards()
            ->save(TarfinCard::factory()->active()->make());
        $tarfinCardTransaction = $tarfinCard->transactions()
            ->save(TarfinCardTransaction::factory()->make());

        $this->actingAs($user)
            ->getJson(route('tarfin-card-transactions.show', [$tarfinCardTransaction]))
            ->assertOk();
    }

    /**
     * @test
     */
    public function a_customer_can_not_see_a_tarfin_card_transaction_for_a_tarfin_card_of_another_customer(): void
    {
        $customer = User::factory()->create();
        $anotherCustomer = User::factory()->create();
        $tarfinCard = $anotherCustomer->tarfinCards()
            ->save(TarfinCard::factory()->active()->make());
        $tarfinCardTransaction = $tarfinCard->transactions()->save(TarfinCardTransaction::factory()->make());

        $this->actingAs($customer)
            ->getJson(route('tarfin-card-transactions.show', [$tarfinCardTransaction]))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function a_customer_can_list_tarfin_card_transactions(): void
    {
        $user = User::factory()->create();
        $tarfinCard = $user->tarfinCards()
            ->save(TarfinCard::factory()->active()->make());
        $tarfinCardTransactions = $tarfinCard->transactions()
            ->saveMany(TarfinCardTransaction::factory()->times(3)->make());
        $collection = TarfinCardTransactionResource::collection($tarfinCardTransactions);
        $request = Request::create(route('tarfin-cards.tarfin-card-transactions.index', $tarfinCard));

        $response = $this->actingAs($user)
            ->getJson(route('tarfin-cards.tarfin-card-transactions.index', $tarfinCard));

        $response->assertOk();
        $response->assertExactJson(['data' => $collection->toArray($request)]);
    }

    /**
     * @test
     */
    public function a_customer_can_not_list_tarfin_card_transactions_for_a_tarfin_card_of_another_customer(): void
    {
        $customer = User::factory()->create();
        $anotherCustomer = User::factory()->create();
        $tarfinCard = $anotherCustomer->tarfinCards()
            ->save(TarfinCard::factory()->active()->make());
        $tarfinCard->transactions()
            ->saveMany(TarfinCardTransaction::factory()->times(3)->make());

        $this->actingAs($customer)
            ->get(route('tarfin-cards.tarfin-card-transactions.index', $tarfinCard))
            ->assertForbidden();
    }
}
