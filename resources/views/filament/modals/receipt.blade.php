<div class="text-gray-900 dark:text-gray-100">

    {{-- Header --}}
    <div style="text-align: center; margin-bottom: 1.25rem; padding-bottom: 1rem;"
         class="border-b border-dashed border-gray-300 dark:border-gray-600">
        <div class="text-lg font-bold">STRUK PEMBELIAN</div>
        <div class="text-xs mt-1 text-gray-500 dark:text-gray-400">No. Transaksi: #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->date->format('d/m/Y H:i') }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">Kasir: {{ $transaction->user->name }}</div>
    </div>

    {{-- Item List --}}
    <div style="margin-bottom: 1rem; padding-bottom: 1rem;"
         class="border-b border-dashed border-gray-300 dark:border-gray-600">
        @foreach($transaction->details as $detail)
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;" class="text-sm">
                <div>
                    <div class="font-medium">{{ $detail->item->name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $detail->qty }} x Rp {{ number_format($detail->item->price, 0, ',', '.') }}</div>
                </div>
                <div class="font-semibold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</div>
            </div>
        @endforeach
    </div>

    {{-- Total --}}
    <div class="text-sm" style="margin-bottom: 1rem;">
        <div style="display: flex; justify-content: space-between;" class="mb-1">
            <span class="text-gray-600 dark:text-gray-400">Total</span>
            <span class="font-semibold">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between;" class="mb-1">
            <span class="text-gray-600 dark:text-gray-400">Bayar</span>
            <span>Rp {{ number_format($transaction->pay_total, 0, ',', '.') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; padding-top: 8px; margin-top: 8px;"
             class="font-bold text-base border-t border-gray-200 dark:border-gray-700">
            <span>Kembalian</span>
            <span>Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="text-center text-xs text-gray-400 dark:text-gray-500" style="margin-bottom: 1rem;">
        Terima kasih atas pembelian Anda!
    </div>

    {{-- Print Button --}}
    <div style="text-align: center;">
        <button onclick="window.print()"
                class="px-6 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 font-semibold text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
            🖨️ Print Struk
        </button>
    </div>

</div>

{{-- Print styles: force white background for print only --}}
<style>
    @media print {
        body * { visibility: hidden; }
        .fi-modal-content, .fi-modal-content * { visibility: visible; }
        .fi-modal-content { position: fixed; left: 0; top: 0; width: 100%; }
        button { display: none !important; }
    }
</style>