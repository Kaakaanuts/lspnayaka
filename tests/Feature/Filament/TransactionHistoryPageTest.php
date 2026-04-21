<?php

namespace Tests\Feature\Filament;

use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionHistoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_history_data_is_available_from_relations(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Minuman',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'name' => 'Es Teh',
            'price' => 5000,
            'stock' => 50,
        ]);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'date' => now(),
            'total' => 10000,
            'pay_total' => 10000,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'item_id' => $item->id,
            'qty' => 2,
            'subtotal' => 10000,
        ]);

        $transaction->load('details.item.category');

        $this->assertSame('Es Teh', $transaction->details->first()?->item?->name);
        $this->assertSame('Minuman', $transaction->details->first()?->item?->category?->name);
        $this->assertSame(2, $transaction->details->sum('qty'));
        $this->assertSame(10000, $transaction->total);
        $this->assertNotNull($transaction->date);
    }
}
