<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use PDO;

class PHPGeneratorController extends Controller
{
    private function range()
    {
        for ($i = 0; $i < 10000; $i++) {
            $value = yield $i;

            if ($value === 'stop') {
                return;
            }
        }
    }

    private function rangeKeyValue()
    {
        for ($i = 0; $i < 10000; $i++) {
           yield "key $i" => "value {$i}";
        }
    }

    private function rangeNoYield()
    {
        $a = [];

        for ($i = 0; $i < PHP_INT_MAX; $i++) {
            $a[] = $i;
        }

        return $a;
    }

    function myGeneratorFunction()
    {
        echo 'One' . "<br>";
        yield 'first return value' . "<br>";

        echo 'Two' . "<br>";
        yield 'second return value' . "<br>";

        return 'my return value';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pdo = DB::connection()->getPdo();
        dd($pdo->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY), PDO::MYSQL_ATTR_USE_BUFFERED_QUERY);

        // $iterator = $this->myGeneratorFunction();

        // $firstValue = $iterator->current();
        // echo $firstValue;

        // $iterator->next();
        // $secondValue = $iterator->current();
        // echo $secondValue;

        // $iterator->next();
        // echo $iterator->getReturn();

        // dd(PHP_INT_MAX);
        // $generator = $this->range();

        // foreach ($generator as $value) {
        //     if ($value === 5) {
        //         $generator->send('stop');
        //     }

        //     echo "current value is {$value} <br>";
        // }

        // LazyCollection::make(function () {
        //     foreach (range(1, 12) as $i) {
        //         yield [
        //             'id' => $i,
        //             'name' => "Product #{$i}",
        //             'price' => rand(9, 99),
        //         ];
        //     }
        // })->chunk(4)->map(function ($lines) {
        //     // dd($lines);
        //     return $lines;
        // })->each(function (LazyCollection $product) {
        //     dd($product);
        // });
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
