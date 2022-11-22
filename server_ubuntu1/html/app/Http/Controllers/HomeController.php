<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->phpGenerator();
        // $this->testUnset();
        // $this->runBackground();
    }

    /**
     * Run in background
     */
    private function runBackground() {
        Log::info('start');
        // exec("php /var/www/html/artisan SendEmails"); // no send in background

        exec("php /var/www/html/artisan SendEmails > /dev/null &"); // no $output will send in background

        Log::info('end');
    }

    function execInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r"));
        }
        else {
            exec($cmd . " > /dev/null &");
        }
    }

    /**
     * Tham chieu &
     */
    private function testUnset() {
        $a = 5;
        $b = &$a;
        unset($b);
        print_r($a . "<br>"); // result: $a = 5;

        $a = 5;
        $b = &$a;
        $b = null;
        print_r($a ? $a : 'null'); // result: $a = null;
    }

    /**
     * Test PHP Generator
     */
    private function noYield() {
        $a = [];

        for ($i = 0; $i < 10000; $i++) {
            $a[] = $i;
        }

        return $a;
    }

    private function withYield() {
        for ($i = 0; $i < 10000; $i++) {
            yield $i;
        }
    }

    public function phpGenerator() {
        $convert_to_mb = 1024 * 2024;

        echo memory_get_usage()/$convert_to_mb . " MB <br>"; // 0.58027498147233 MB (1tr rows noYield)

        // $a = $this->noYield();
        $a = $this->withYield();

        // foreach ($a as $value) {
        //     echo $value . "<br>";
        // }

        echo memory_get_usage()/$convert_to_mb . " MB <br>"; // 16.772013185524  MB (1tr rows noYield)

        // unset($a);
        $a = null;

        echo memory_get_usage()/$convert_to_mb . " MB <br>"; // 0.58027498147233 MB  (1tr rows noYield)
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
