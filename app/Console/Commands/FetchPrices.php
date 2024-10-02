<?php

namespace App\Console\Commands;

use App\Models\Price;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FetchPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to fetch and update alko prices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');

        try {
            $currencyRateResponse = Http::get(config('services.currency_rate.link') . config('services.currency_rate.api_key'));
            $currencyRate = $currencyRateResponse->json()['quotes']['EURGBP'];
        } catch (\Exception  $e) {
            $this->error("Couldn't fetch EURGBP currency rate");
            $this->error($e->getMessage());
        }

        try {
            $response = Http::get(config('services.alko.prices_file_link'));
            $filePath = storage_path('app/downloads/prices.xlsx');
            file_put_contents($filePath, $response->body());
        } catch (\Exception $e) {
            $this->error('Problem with downloading prices file');
            $this->error($e->getMessage());
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $toCreate = [];
        $toUpdate = [];

        $prices = Price::pluck('price', 'number')->toArray();
        $timestamp = now();

        foreach ($sheet->getRowIterator() as $row) {

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];

            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            if ($number = intval($data[0])) {

                if (in_array($number, array_keys($prices))) {
                    if ($prices[$number] != floatval($data[4])) {
                        $toUpdate[] = $this->getPrice($data, $currencyRate, $timestamp);
                    } // else means the price is the same and we don't need to update it
                } else {
                    $toCreate[] = $this->getPrice($data, $currencyRate, $timestamp);;
                }
            }
        }

        foreach (array_chunk($toCreate, 1000) as $chunk) {
            Price::insert($chunk);
        }

        if (count($toUpdate)) {
            $numbers = collect($toUpdate)->pluck('number')->toArray();

            $priceSql = "CASE ";
            $priceGBPSql = "CASE ";

            foreach ($toUpdate as $number) {
                $priceSql .= "WHEN number = {$number['number']} THEN {$number['price']} ";
                $priceGBPSql .= "WHEN number = {$number['number']} THEN '{$number['priceGBP']}' ";
            }

            $priceSql .= "END";
            $priceGBPSql .= "END";

            DB::statement("UPDATE prices SET priceGBP = $priceGBPSql, price = $priceSql WHERE number IN (" . implode(',', $numbers) . ")");
        }

        $this->info('Prices updated successfully');
    }

    private function getPrice($data, $currencyRate, $timestamp)
    {
        $bottleSize = (float)str_replace(',', '.', substr($data[3], 0,-2)) * 1000;

        return [
            'number' => intval($data[0]),
            'name' => $data[1],
            'bottlesize' => $bottleSize,
            'price' => floatval($data[4]),
            'priceGBP' => floatval($data[4]) * $currencyRate,
            'timestamp' => $timestamp,
        ];
    }
}
