<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Total revenue from confirmed, processing, shipped, and completed orders
        $totalRevenue = Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'completed'])
            ->sum('total_amount');

        // Orders count
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $confirmedOrders = Order::where('status', 'confirmed')->count();

        // Products count
        $totalProducts = Product::count();
        $lowStockProducts = Product::lowStock()->count();
        $outOfStockProducts = Product::outOfStock()->count();

        return [
            Stat::make('Total Revenue', 'Rp ' . Number::format($totalRevenue, locale: 'id'))
                ->description('From confirmed orders')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart($this->getRevenueChart()),

            Stat::make('Total Orders', $totalOrders)
                ->description("{$pendingOrders} pending, {$confirmedOrders} confirmed")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart($this->getOrdersChart()),

            Stat::make('Products', $totalProducts)
                ->description("{$lowStockProducts} low stock, {$outOfStockProducts} out of stock")
                ->descriptionIcon('heroicon-m-camera')
                ->color($lowStockProducts > 0 ? 'warning' : 'success'),

            Stat::make('Pending Orders', $pendingOrders)
                ->description('Awaiting confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),
        ];
    }

    protected function getRevenueChart(): array
    {
        // Get last 7 days revenue
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenue = Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'completed'])
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            $data[] = (int) ($revenue / 1000000); // In millions for chart readability
        }
        return $data;
    }

    protected function getOrdersChart(): array
    {
        // Get last 7 days orders count
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = Order::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }
}
