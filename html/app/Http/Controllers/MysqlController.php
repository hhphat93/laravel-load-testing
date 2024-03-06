<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MysqlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Select
        // $users = DB::connection('test_read_write')->table('users')->get(); //run on slave
        // dd($users);

        // Replace
        // $sql = '
        //     REPLACE INTO users
        //     SELECT name
        //     FROM posts;
        // ';

        // $result = DB::connection('test_read_write')->statement($sql); // run on master (return bool)
        // $result = DB::connection('test_read_write::read')->statement($sql); // run on slave (return bool)
        // dd($result);

        // Optimize
        // $result = DB::connection('test_read_write')->statement('OPTIMIZE TABLE users'); // run on master
        // DB::connection('test_read_write::read')->statement('OPTIMIZE TABLE users'); // run on slave

        // Delete
        // $result = DB::connection('test_read_write')->statement("DELETE FROM users WHERE name = 'e'"); // run on master  (return bool)
        // $result = DB::connection('test_read_write::read')->statement("DELETE FROM users WHERE name = 'e'"); // run on slave  (return bool)
        // dd($result);
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
