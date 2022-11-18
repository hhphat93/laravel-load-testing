<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $a = 5;
        $b = &$a;
        $b = null; // just say $b should not point to any variable
        print $a; // 5
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
