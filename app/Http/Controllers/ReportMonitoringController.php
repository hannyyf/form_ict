<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class ReportMonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $username = Auth::user()->username;
        $getdiv = DB::table('vw_profile_karyawan')
                        ->select('*')
                        ->where('employee_id_bias','=',$username)
                        ->first();
        $divuser = $getdiv->div_id_bias;

        $datas  = DB::table('vw_listreport')
                    ->select('*')
                    ->where('divcode','=',$divuser)
                    ->orderBy('notrx','asc')
                    ->get();

        return view('fppb.listreport',['datas' => $datas]);
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
    public function edit($notrx)
    {
        $data       = DB::table('vw_transaksi')
                     ->select('*')
                     ->where('notrx','=',$notrx)
                     ->get();

        $dataheader = DB::table('vw_listrequest')
                     ->select('*')
                     ->where('notrx','=',$notrx)
                     ->first();

        $datalog    = DB::table('vw_log')
                     ->select('*')
                     ->where('notrx','=',$notrx)
                     ->orderBy('dtfrom','asc')
                     ->get();

       $kodeitems  = DB::table('generate_pr')
                        ->select('*')
                        ->where('notrx','=',$notrx)
                        ->get();


        return view('fppb.detailreport',[
                                    'datafetch' => $data,
                                    'header'    => $dataheader,
                                    'datalog'   => $datalog,
                                    'kodeitems' => $kodeitems
                                    ]);
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

    public function filter(Request $request) {
        $nofppb     = $request->get('nofppb');
        $datas      = DB::table('vw_listreport')
                        ->select('*')
                        ->where('notrx','=',$nofppb)
                        ->orderBy('notrx','asc')
                        ->get();
        return view('fppb.listreport',['datas' => $datas]);
    }
}
