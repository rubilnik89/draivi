<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PriceController extends Controller
{
    public function index()
    {
        $prices = \App\Models\Price::paginate(10);

        return view('prices.index', compact('prices'));
    }

    public function loadTableData(Request $request)
    {
        $prices = \App\Models\Price::paginate(10);

        if ($request->ajax()) {
            $tableData = view('prices.partials.table-rows', compact('prices'))->render();

            $pagination = view('prices.partials.pagination-links', compact('prices'))->render();

            return response()->json(['tableData' => $tableData, 'pagination' => $pagination]);
        }
    }

    public function updateOrderAmount(Request $request)
    {
        $price = Price::where('number', $request->number)->first();

        if ($request->action == 'add') {
            $price->orderamount += 1;
        } elseif ($request->action == 'clear') {
            $price->orderamount = 0;
        }

        $price->save();

        return response()->json([
            'newAmount' => $price->orderamount,
            'price' => number_format($price->price * ($price->orderamount ?: 1), 2),
            'priceGBP' => number_format($price->priceGBP * ($price->orderamount ?: 1), 2),
        ]);
    }

    public function delete()
    {
        Price::truncate();

        return back();
    }

    public function fetch()
    {
        Artisan::call('app:fetch-prices');

        return back();
    }
}
