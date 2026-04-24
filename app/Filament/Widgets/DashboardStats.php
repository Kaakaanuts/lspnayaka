<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Kategori', Category::count()),

            Stat::make('Total Item', Item::count()),

            Stat::make('Total Transaksi', Transaction::count()),
        ];
    }
}
