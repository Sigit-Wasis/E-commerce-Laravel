<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Product;
use App\Category;
use GuzzleHttp\Client;

class MarketplaceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //ADA DUA PARAMETER YANG DITERIMA OLEH CLASS INI
    protected $username; //PERTAMA ADALAH USERNAME
    protected $take; //DAN BAGIAN KEDUA ADALAH JUMLAH PRODUK YANG AKAN DIUNDUH
    public function __construct($username, $take)
    {
        $this->username = $username;
        $this->take = $take;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = 'https://ruangapi.com/api/v1/shopee';
        $client = new Client();
        //HIT API YANG SUDAH DISEDIAKAN OLEH RUANGAPI
        $response = $client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'MASUKKAN API KEY ANDA DISINI'
            ],
            //DENGAN MENGIRIMKAN PARAMETER YANG DIMINTA
            'form_params' => [
                'username' => $this->username,
                'take' => $this->take
            ]
        ]);

        $body = json_decode($response->getBody(), true);
        //LOOPING YANG DITERIMA
        foreach ($body['data']['results'] as $row) {
            //UNDUH FILE GAMBAR DARI URL YANG TERSEDIA KE DALAM LOCAL STORAGENYA KITA
            $filename = Str::slug($row['title']) . '-' . time() . '.png';
            //MENGUGNAKAN FUNGSI FILE_PUT_CONTENTS
            file_put_contents(storage_path('app/public/products/' . $filename), file_get_contents($row['images'][0]));

            //JADI PASTIKAN ADA KATEGORI PADA TABLE CATEGORIES KARENA DATA PERTAMA AKAN KITA GUNAKAN SEBAGAI DEFAULT KATEGORI
            $category = Category::first();
            //JIKA DI API TERSEDIA DATA KATEGORINYA
            if (count($row['categories']) > 0) {
                //MAKA KITA TAMBAHKAN KE TABLE CATEGORIES
                $category = Category::firstOrCreate([
                    'name' => $row['categories'][0],
                    'slug' => Str::slug($row['categories'][0])
                ]);
            }
          
            //KEMUDIAN SIMPAN DATA PRODUK KE DALAM TABLE PRODUCTS
            //KITA MENGGUNAKAN FIRST OR CREATE UNTUK MENGHINDARI DATA YANG SAMA
            Product::firstOrCreate([
                'name' => $row['title'],
            ],
            [
                'slug' => Str::slug($row['title']),
                'category_id' => $category->id,
                'description' => $row['description'],
                'image' => $filename,
                'price' => $row['price'],
                'weight' => 600,
                'status' => 1
            ]);
        }

        //JIKA HASIL NYA MASIH LEBIH BESAR DARI 0
        //MAKA KEMUNGKINAN MASIH ADA DATA PADA PAGE SELANJUTNYA
        if (count($body['data']['results']) > 0) {
            //JADI KITA LOAD LAGI DATA BERIKUTNYA DENGAN MENAMBAHKAN NILAI 10 PADA TAKE SEBELUMNYA
          //JADI MISAL TAKE SEBELUMNYA ADALAH 10, MAKA PADA TAKE KEDUA ADALAH 20 DAN TAKE KETIGA ADALAH 30. BEGITU SETERUSNYA
            MarketplaceJob::dispatch($this->username, $this->take + 10)->delay(now()->addMinutes(5));
        }
    }
}