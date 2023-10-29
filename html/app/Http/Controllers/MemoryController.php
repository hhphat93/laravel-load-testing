<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use PDO;

class MemoryController extends Controller
{
    function getProducts() {
        foreach (range(1, 2097150) as $i) {
            yield [
                'id' => $i,
                'name' => "Product #{$i}",
                'price' => rand(9, 99),
            ];
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // echo convert(memory_get_usage()) . '<br>';
        // $a = $this->getProducts();
        // foreach ($this->getProducts() as $product) {
        //     $b =  $product['id'];
        // }

        // echo convert(memory_get_usage()) . '<br>';

        // dd('done');




        // dd(request()->getClientIp(), request()->ip(), request()->header('X-Forwarded-For'));
        // phpinfo();
        // html/vendor/laravel/framework/src/Illuminate/Database/Connection.php
        // public function cursor($query, $bindings = [], $useReadPdo = true)
        echo convert(memory_get_usage()) . '<br>';

        // DB::connection('employees')->getPdo()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $employees = DB::connection('employees')->table('employees')->cursor();
        // $employees = DB::connection('employees')->table('salaries')->cursor();
        // $employees = DB::connection('employees')->table('salaries')->cursor();

        // dd(DB::connection('employees')->getPdo()->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY));
        echo convert(memory_get_usage()) . '<br>';
        // dd($employees);
        // $data = [];
        foreach ($employees as $key => $employee) {
            // dd($employee);
            // if ($key > 2000000) {
            //     echo 'done' . '<br>';
            //     break;
            // } else {
            //     // $data[] = $employee;

            //     // $a = $employee;
            //     // dd($employee);
            //     // echo $key . '->' . $employee->emp_no  . '<br>';
            // }
        }

        echo convert(memory_get_usage()) . '<br>';

        // unset($data);
        $data = null;

        echo convert(memory_get_usage()) . '<br>';
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
