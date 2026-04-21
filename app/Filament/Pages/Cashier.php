<?php

namespace App\Filament\Pages;

use App\Models\Item;
use App\Models\Transaction;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use UnitEnum;

class Cashier extends Page
{
    protected string $view = 'filament.pages.cashier';

    protected static ?string $navigationLabel = 'Kasir';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected ?string $heading = 'Kasir';

    public array $cart = [];
    public int $pay_amount = 0;

    // Untuk modal struk
    public bool $showReceipt = false;
    public ?array $receiptData = null;

    #[Computed]
    public function grandTotal(): int
    {
        return collect($this->cart)->sum('subtotal');
    }

    #[Computed]
    public function items(): Collection
    {
        return Item::query()->orderBy('name')->get();
    }

    #[Computed]
    public function totalQuantity(): int
    {
        return collect($this->cart)->sum('qty');
    }

    public function addToCart(int $itemId): void
    {
        $item = Item::find($itemId);

        if (! $item || $item->stock < 1) {
            Notification::make()->title('Stok habis!')->danger()->send();
            return;
        }

        if (isset($this->cart[$itemId])) {
            if ($this->cart[$itemId]['qty'] >= $item->stock) {
                Notification::make()->title('Stok tidak mencukupi!')->danger()->send();
                return;
            }
            $this->cart[$itemId]['qty']++;
            $this->cart[$itemId]['subtotal'] = $this->cart[$itemId]['qty'] * $item->price;
        } else {
            $this->cart[$itemId] = [
                'name' => $item->name,
                'price' => $item->price,
                'qty' => 1,
                'subtotal' => $item->price,
            ];
        }
    }

    public function decreaseQty(int $itemId): void
    {
        if (isset($this->cart[$itemId])) {
            if ($this->cart[$itemId]['qty'] > 1) {
                $this->cart[$itemId]['qty']--;
                $this->cart[$itemId]['subtotal'] = $this->cart[$itemId]['qty'] * $this->cart[$itemId]['price'];
            } else {
                unset($this->cart[$itemId]);
            }
        }
    }

    public function checkout(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Keranjang masih kosong!')->warning()->send();
            return;
        }

        $grandTotal = $this->grandTotal;

        if ($this->pay_amount < $grandTotal) {
            Notification::make()->title('Uang pembayaran kurang!')->danger()->send();
            return;
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'date'     => now(),
                'total'    => $grandTotal,
                'pay_total' => $this->pay_amount,
            ]);

            foreach ($this->cart as $itemId => $detail) {
                $transaction->details()->create([
                    'item_id'  => $itemId,
                    'qty'      => $detail['qty'],
                    'subtotal' => $detail['subtotal'],
                ]);
                Item::where('id', $itemId)->decrement('stock', $detail['qty']);
            }

            DB::commit();

            $kembalian = $this->pay_amount - $grandTotal;

            // Simpan data struk sebelum reset cart
            $this->receiptData = [
                'id'        => $transaction->id,
                'date'      => $transaction->date,
                'items'     => $this->cart,
                'total'     => $grandTotal,
                'pay_total' => $this->pay_amount,
                'kembalian' => $kembalian,
                'kasir'     => auth()->user()->name,
            ];

            $this->reset(['cart', 'pay_amount']);
            $this->showReceipt = true;

        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->title('Terjadi kesalahan: ' . $e->getMessage())->danger()->send();
        }
    }

    public function closeReceipt(): void
    {
        $this->showReceipt = false;
        $this->receiptData = null;
    }
}