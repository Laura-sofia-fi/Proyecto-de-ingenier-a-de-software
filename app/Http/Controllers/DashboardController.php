<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now();
        $periods = [
            'today' => [
                'label' => 'Hoy',
                'from' => $today->copy()->startOfDay(),
                'to' => $today->copy()->endOfDay(),
            ],
            'week' => [
                'label' => 'Esta semana',
                'from' => $today->copy()->startOfWeek(),
                'to' => $today->copy()->endOfWeek(),
            ],
            'month' => [
                'label' => 'Este mes',
                'from' => $today->copy()->startOfMonth(),
                'to' => $today->copy()->endOfMonth(),
            ],
        ];

        $periodSummaries = collect($periods)->map(function (array $period) {
            $sales = Invoice::query()
                ->where('status', 'activa')
                ->whereBetween('issue_date', [$period['from'], $period['to']])
                ->get();

            $payments = Payment::query()
                ->whereBetween('payment_date', [$period['from'], $period['to']])
                ->sum('amount');

            $orders = Order::query()
                ->whereBetween('order_date', [$period['from'], $period['to']])
                ->count();

            return [
                'label' => $period['label'],
                'sales_total' => (float) $sales->sum('total'),
                'payments_total' => (float) $payments,
                'orders_count' => $orders,
                'avg_ticket' => $sales->count() > 0 ? (float) $sales->avg('total') : 0,
            ];
        });

        return view('dashboard', [
            'salesToday' => Invoice::query()->whereDate('issue_date', $today->toDateString())->where('status', 'activa')->sum('total'),
            'pendingBalance' => Invoice::query()->where('status', 'activa')->sum('balance_due'),
            'ordersToday' => Order::query()->whereDate('order_date', $today->toDateString())->count(),
            'periodSummaries' => $periodSummaries,
            'recentOrders' => Order::query()->with(['client', 'invoice'])->latest()->take(5)->get(),
            'recentPayments' => Invoice::query()->with('payments.recorder')->latest()->take(5)->get(),
        ]);
    }
}
