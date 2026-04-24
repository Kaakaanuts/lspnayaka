<x-filament-panels::page>
    <div>

        <!-- Daftar Produk -->
        <div style="margin-bottom: 1.5rem;">
            <x-filament::section heading="Daftar Produk">
                <div class="flex flex-col gap-3">
                    @forelse(\App\Models\Item::all() as $item)
                        <div style="display: flex; align-items: center; justify-content: space-between;"
                             class="border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3">
                            <div>
                                <div class="font-bold text-sm">{{ $item->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">Stok: {{ $item->stock }} &nbsp;·&nbsp; Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                <x-filament::button size="xs" color="danger" wire:click="decreaseQty({{ $item->id }})">−</x-filament::button>
                                <span class="text-sm font-semibold" style="min-width: 20px; text-align: center;">
                                    {{ isset($cart[$item->id]) ? $cart[$item->id]['qty'] : 0 }}
                                </span>
                                <x-filament::button size="xs" color="success" wire:click="addToCart({{ $item->id }})">+</x-filament::button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-500">
                            Belum ada produk. Silahkan tambah produk di menu <b>Items</b> terlebih dahulu.
                        </div>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        <!-- Keranjang -->
        <div>
            <x-filament::section heading="Keranjang (Cart)">
                <div class="flex flex-col gap-1">
                    @forelse($cart as $id => $detail)
                        <div style="display: flex; justify-content: space-between; align-items: center;"
                             class="py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div>
                                <div class="font-medium text-sm">{{ $detail['name'] }}</div>
                                <div class="text-xs text-gray-500">Rp {{ number_format($detail['price'], 0, ',', '.') }} x {{ $detail['qty'] }}</div>
                            </div>
                            <div class="font-bold text-sm">Rp {{ number_format($detail['subtotal'], 0, ',', '.') }}</div>
                        </div>
                    @empty
                        <div class="text-gray-500 text-center py-3">Keranjang masih kosong.</div>
                    @endforelse
                </div>

                @if(count($cart) > 0)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-col gap-4">
                        <div style="display: flex; justify-content: space-between;" class="text-lg font-bold">
                            <span>Total:</span>
                            <span>Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Uang Bayar (Rp):</label>
                            <input type="number" wire:model.live="pay_amount"
                                   class="w-full bg-white dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm text-gray-950 dark:text-white px-3 py-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="0">
                        </div>
                        <x-filament::button wire:click="checkout" color="primary" size="lg" class="w-full">
                            <span class="w-full text-center">Proses Pembayaran</span>
                        </x-filament::button>
                    </div>
                @endif
            </x-filament::section>
        </div>

    </div>

    <!-- Modal Struk -->
    @if($showReceipt && $receiptData)
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <!-- Backdrop -->
            <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);" wire:click="closeReceipt"></div>

            <!-- Modal Box -->
            <div style="position: relative; z-index: 51; background: white; border-radius: 1rem; width: 100%; max-width: 400px; padding: 2rem; color: #111;">
                
                <!-- Header Struk -->
                <div style="text-align: center; margin-bottom: 1.25rem; border-bottom: 1px dashed #ccc; padding-bottom: 1rem;">
                    <div style="font-size: 1.25rem; font-weight: 700;">Struk Pembelian</div>
                    <div style="font-size: 0.75rem; color: #666; margin-top: 4px;">No. Transaksi: #{{ str_pad($receiptData['id'], 5, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size: 0.75rem; color: #666;">{{ \Carbon\Carbon::parse($receiptData['date'])->format('d/m/Y H:i') }}</div>
                    <div style="font-size: 0.75rem; color: #666;">Kasir: {{ $receiptData['kasir'] }}</div>
                </div>

                <!-- Item List -->
                <div style="margin-bottom: 1rem; border-bottom: 1px dashed #ccc; padding-bottom: 1rem;">
                    @foreach($receiptData['items'] as $item)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.875rem;">
                            <div>
                                <div style="font-weight: 500;">{{ $item['name'] }}</div>
                                <div style="color: #888; font-size: 0.75rem;">{{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                            </div>
                            <div style="font-weight: 600;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- Total -->
                <div style="font-size: 0.875rem; margin-bottom: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span>Total</span>
                        <span style="font-weight: 600;">Rp {{ number_format($receiptData['total'], 0, ',', '.') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span>Bayar</span>
                        <span>Rp {{ number_format($receiptData['pay_total'], 0, ',', '.') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1rem; border-top: 1px solid #eee; padding-top: 8px; margin-top: 8px;">
                        <span>Kembalian</span>
                        <span>Rp {{ number_format($receiptData['kembalian'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Footer -->
                <div style="text-align: center; font-size: 0.75rem; color: #888; margin-top: 1rem; margin-bottom: 1.25rem;">
                    Terima kasih atas pembelian Anda!
                </div>

                <!-- Tombol -->
                <div style="display: flex; gap: 8px;">
                    <button onclick="window.print()"
                            style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #d1d5db; background: white; font-weight: 600; cursor: pointer; font-size: 0.875rem;">
                        🖨️ Print
                    </button>
                    <button wire:click="closeReceipt"
                            style="flex: 1; padding: 10px; border-radius: 8px; border: none; background: #f97316; color: white; font-weight: 600; cursor: pointer; font-size: 0.875rem;">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif

</x-filament-panels::page>