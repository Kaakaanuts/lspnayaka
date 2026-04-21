<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('detail_items')
                    ->label('Nama Item')
                    ->state(fn (Transaction $record): string => $record->details
                        ->pluck('item.name')
                        ->filter()
                        ->unique()
                        ->join(', ') ?: '-'),
                TextColumn::make('detail_categories')
                    ->label('Kategori')
                    ->state(fn (Transaction $record): string => $record->details
                        ->pluck('item.category.name')
                        ->filter()
                        ->unique()
                        ->join(', ') ?: '-'),
                TextColumn::make('detail_qty')
                    ->label('Jumlah Item')
                    ->alignCenter()
                    ->state(fn (Transaction $record): int => (int) $record->details->sum('qty')),
                TextColumn::make('total')
                    ->label('Jumlah Harga')
                    ->alignEnd()
                    ->formatStateUsing(fn (?int $state): string => 'Rp '.number_format((int) $state, 0, ',', '.')),
                TextColumn::make('date')
                    ->label('Tanggal Pembelian')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([])
            ->recordActions([
                Action::make('lihat_struk')
                    ->label('Lihat Struk')
                    ->icon('heroicon-o-receipt-percent')
                    ->modalHeading(fn (Transaction $record) => 'Struk #' . str_pad($record->id, 5, '0', STR_PAD_LEFT))
                    ->modalContent(fn (Transaction $record) => view(
                        'filament.modals.receipt',
                        ['transaction' => $record->load('details.item', 'user')]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->toolbarActions([]);
    }
}