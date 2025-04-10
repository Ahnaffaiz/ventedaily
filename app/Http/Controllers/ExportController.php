<?php

namespace App\Http\Controllers;

use App\Enums\DiscountType;
use App\Enums\PaymentType;
use App\Models\Expense;
use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\Printer;

class ExportController extends Controller
{
    public function stockHistory($productStockId, $startDate, $endDate)
    {
        $start_date = Carbon::parse($startDate)->startOfDay();
        $end_date = Carbon::parse($endDate)->endOfDay();
        $productStock = ProductStock::where('id', $productStockId)->first();
        $stockHistories = ProductStockHistory::with('productStock')->whereBetween('created_at', [$start_date, $end_date])
            ->where('product_stock_id', $productStockId)
            ->orderBy('id', 'desc')
            ->get();
        $setting = Setting::first();

        return view('print.stock-history', compact('stockHistories', 'setting', 'productStock', 'startDate', 'endDate'));
    }

    public function monthlyReport($month)
    {
        $setting = Setting::first();
        $monthYear = $month;
        [$year, $month] = explode('-', $month);

        $report['omzet'] = Sale::whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->sum('total_price');
        $report['net_profit'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->select(DB::raw('SUM((sale_items.price - product_stocks.purchase_price) * sale_items.total_items) as total_profit'))
                            ->value('total_profit');
        $report['total_sales'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales');
        $report['total_sales_reseller'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->join('groups', 'customers.group_id', '=', 'groups.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('groups.name', 'reseller')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_reseller');
        $report['total_sales_shopee'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('marketplaces', 'sales.marketplace_id', '=', 'marketplaces.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('marketplaces.name', 'shopee')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_shopee');
        $report['total_sales_tiktok'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('marketplaces', 'sales.marketplace_id', '=', 'marketplaces.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('marketplaces.name', 'tiktok')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_tiktok');
        $report['total_sales_website'] = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('marketplaces', 'sales.marketplace_id', '=', 'marketplaces.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->where('marketplaces.name', 'website')
                            ->select(DB::raw('SUM(sale_items.total_items) as total_sales'))
                            ->value('total_sales_website');
        $report['monthly_cost'] = Expense::whereMonth('created_at', $month)
                            ->whereYear('created_at', $year)
                            ->sum('total_amount');
        $salesCategories = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                            ->join('products', 'product_stocks.product_id', '=', 'products.id')
                            ->join('categories', 'products.category_id', '=', 'categories.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->select(
                                'categories.id',
                                'categories.name',
                                DB::raw('SUM(sale_items.total_items) as total_items')
                            )
                            ->groupBy('categories.id', 'categories.name')
                            ->get();
            $salesProducts = DB::table('sale_items')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->join('product_stocks', 'sale_items.product_stock_id', '=', 'product_stocks.id')
                            ->join('products', 'product_stocks.product_id', '=', 'products.id')
                            ->whereMonth('sales.created_at', $month)
                            ->whereYear('sales.created_at', $year)
                            ->select(
                                'products.id',
                                'products.name',
                                DB::raw('SUM(sale_items.total_items) as total_items')
                            )
                            ->groupBy('products.id', 'products.name')
                            ->get();
        return view('print.monthly-report', compact('monthYear', 'report', 'salesCategories', 'salesProducts','setting'));
    }

    public function printInvoice($saleId)
    {
        // Ambil data utama penjualan
        $sale = Sale::where('id', $saleId)->first();

        // Ambil pengaturan perusahaan
        $setting = Setting::first();
        $discount = 0;
        $shipping = 0;
        if(strtolower($sale->discount_type) === strtolower(DiscountType::PERSEN)) {
            $discount = round($sale->sub_total* (int) $sale->discount/100);
        } elseif(strtolower($sale->discount_type) === strtolower(DiscountType::RUPIAH)) {
            $discount = $sale->discount;
        }

        $ship = $sale->ship ? $sale->ship : 0;
        $sub_total = $sale->sub_total - ($discount + $ship);

        $tax = 0;
        if($sale->tax) {
            $tax = round($sub_total * (int) $sale->tax/100);
        }

        // Konfigurasi printer
        $connector = new DummyPrintConnector();
        $profile = CapabilityProfile::load("simple");
        $printer = new Printer($connector, $profile);

        // Fungsi pembantu untuk mencetak 3 kolom
        $buatBaris4Kolom = function ($kolom1, $kolom2, $kolom3) {
            $lebar_kolom_1 = 8;
            $lebar_kolom_2 = 10;
            $lebar_kolom_3 = 10;

            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
            $kolom3 = wordwrap($kolom3, $lebar_kolom_3, "\n", true);

            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);
            $kolom3Array = explode("\n", $kolom3);

            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array), count($kolom3Array));
            $hasilBaris = [];

            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {
                $hasilKolom1 = str_pad($kolom1Array[$i] ?? "", $lebar_kolom_1, " ");
                $hasilKolom2 = str_pad($kolom2Array[$i] ?? "", $lebar_kolom_2, " ", STR_PAD_LEFT);
                $hasilKolom3 = str_pad($kolom3Array[$i] ?? "", $lebar_kolom_3, " ", STR_PAD_LEFT);
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
            }

            return implode("\n", $hasilBaris) . "\n";
        };

        // Fungsi pembantu 1 kolom
        $buatBaris1 = fn($kolom1) => wordwrap($kolom1, 33, "\n", true);

        // HEADER
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text($setting->name . "\n");
        $printer->selectPrintMode();
        $printer->text("Beteng Trade Center \n");
        $printer->text("Lantai 1 Blok A4  no 14. \n");
        $printer->text("Phone : 0813-9302-4717 \n");
        $printer->text("--------------------------------\n");

        // Info nota
        $printer->initialize();
        $printer->text("NO    : INV-" . Carbon::parse($sale->created_at)->format('d-F-Y') . "-" . $sale->no_sale . "\n");
        $printer->text("Time  : " . Carbon::parse($sale->created_at)->format('d-F-Y') . "\n");
        $printer->text("--------------------------------\n");

        // Customer
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("*" . $sale->customer->name . "*\n");
        $printer->text("Nomor Keep : " . $sale->Keep?->no_keep . "*\n");
        $printer->feed();

        // Detail barang
        $printer->initialize();
        foreach ($sale->saleItems as $saleItem) {
            $printer->text($buatBaris1( ucwords($saleItem->productStock->product->name) . " " . ucwords($saleItem->productStock->color->name) . " " . ucwords($saleItem->productStock->size->name) . "\n"));
            $printer->text($buatBaris4Kolom($saleItem->total_items . "x", number_format($saleItem->price), number_format($saleItem->total_price)));
        }

        // Ringkasan pembayaran
        $printer->text("--------------------------------\n");
        $printer->text($buatBaris4Kolom('', "SubTotal", number_format($sale->sub_total)));
        $printer->text($buatBaris4Kolom('', "Diskon", number_format($discount)));
        $printer->text($buatBaris4Kolom('', "Pengiriman", number_format($ship)));
        $printer->text($buatBaris4Kolom('', "Pajak", number_format($tax)));
        $printer->text("--------------------------------\n");
        $printer->text($buatBaris4Kolom('', "TOTAL :", number_format($sale->total_price)));
        $printer->text($buatBaris4Kolom('', "Total pcs :", number_format($sale->total_items)));
        $printer->text($buatBaris4Kolom('', "TUNAI :", number_format($sale->salePayment?->cash_received)));
        $printer->text($buatBaris4Kolom('', "Kembali :", number_format($sale->salePayment?->cash_change * -1)));

        // Info tambahan
        $printer->feed();
        $printer->initialize();
        $printer->text("Pembayaran :" . strtolower($sale->salePayment->payment_type) == strtolower(PaymentType::TRANSFER) ? ucwords($sale->salePayment->bank->name) : 'Cash'  . "\n");
        $printer->text("Kasir : Admin Vente\n");

        // Footer
        $printer->feed();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("*Terimakasih*\n");
        $printer->initialize();
        $printer->selectPrintMode(Printer::MODE_UNDERLINE);
        $printer->text("Periksa Barang Anda Terlebih Dahulu, Penukaran Barang Max 3x24 Jam*\n\n");
        $printer->text("Dapatkan cashback Rp. 5.000,- untuk setiap pembelian kelipatan 10pcs. Batas penukaran nota maksimal 1 bulan dari tanggal transaksi.* \n");
        $printer->cut();

        // Output hasil cetakan ke browser
        $data = $connector->getData();
        $print = base64_encode($data);
        $btraw = 'rawbt:base64,' . $print;

        header('Content-type: application/octet-stream');
        header('Content-Length: ' . strlen($btraw));
        echo $btraw;

        $printer->pulse();
        $printer->close();
    }
}
