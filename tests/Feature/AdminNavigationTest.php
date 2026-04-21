<?php

namespace Tests\Feature;

use App\Providers\Filament\AdminPanelProvider;
use Filament\Panel;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    public function test_admin_panel_orders_transaction_group_before_master_data_group(): void
    {
        $provider = new AdminPanelProvider($this->app);
        $panel = $provider->panel(Panel::make());

        $labels = array_map(static fn ($group) => $group->getLabel(), $panel->getNavigationGroups());

        $this->assertSame([
            'Transaksi',
            'Master Data',
        ], $labels);
    }
}
