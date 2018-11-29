<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Mail;

class ReviewICTController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');
        $datenow = date('Y-m-d H:i:s');
        $username = Auth::user()->username;
        $data = DB::table('vw_listreview')
                             ->select('*')
                             ->where('nik','=',$username) // nik di master kategori 
                             ->whereIn('approvaltype',[3,4,5])
                             ->where('issend','=','1')
                             ->where('dtfrom','<=',$datenow)
                             ->where('dtthru','>=',$datenow)
                             ->orderBy('notrx','desc')
                             ->groupBy('notrx','nik','dtfppb','div_nama', 'approvaltype','dtfrom', 'dtthru','issend')
                             ->get();

        return view('fppb.listreviewict',['data'=>$data]);
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
        date_default_timezone_set('Asia/Jakarta');
        $datenow = date('Y-m-d H:i:s');
        $username = Auth::user()->username;
        $dtthru = '2079-06-06 23:59:00'; // nilai maksimum untuk type smalldatetime

        DB::beginTransaction();
        try {
            // update dtmodified tabel fppb header
            DB::table('tr_fppb_header')
                ->where('notrx','=',$request->nofppb)
                ->update([
                    'noteict'       => $request->noteict,
                    'dtmodified'    => $datenow
                ]);

            // update current status approval
            DB::table('approvalstatus')
                    ->where('notrx','=',$request->nofppb)
                    ->where('dtfrom','<=',$datenow)
                    ->where('dtthru','>=',$datenow)
                    ->update([
                        'dtthru'     => $datenow
                     ]);

            // insert new status approval
            DB::table('approvalstatus')->insert([
                'idstatus'      => Uuid::uuid4()->getHex(),
                'notrx'         => $request->nofppb,
                'nik'           => $username,
                'approvaltype'  => 8,
                'statustype'    => 'Review',
                'dtfrom'        => $datenow,
                'dtthru'        => $dtthru
            ]);


            // update perkiraan budget per item
            foreach ($request->no as $key => $value) {
                $budget = intval(str_replace(',','',$request->budget[$key]));
                DB::table('tr_fppb_detail')
                    ->where('notrx','=',$request->nofppb)
                    ->where('seqid','=',$request->no[$key])
                    ->update(
                        ['perkiraanbudget'  => $budget
                     ]);
                }

            $getdivfrommaster = DB::table('vw_master_division')
                                    ->select('*')
                                    ->where('div_nama','=',$request->divisi)
                                    ->where('div_id','like','djabesmen%')
                                    ->first();
            $divcode = $getdivfrommaster->div_id_bias;

            // cari data ict manager from master
            $getdataictmgr     = DB::table('master_app_ictmanager')
                                    ->select('*')
                                    ->first();

            // cek email ict manager dari master employee
            $getemail = DB::table('vw_master_employee')
                            ->select('*')
                            ->where('employee_id_bias','=',$getdataictmgr->nikictmgr)
                            ->first();
            $emailictmgr = $getemail->employee_email;

            $detail = DB::table('tr_fppb_detail')
                             ->select('*')
                             ->where('notrx','=',$request->nofppb)
                             ->get();

            // fungsi kirim email notifikasi ke ict manager
            Mail::send('email.email_ictmgr', [
                    'divisi'    => $request->divisi, 
                    'nofppb'    => $request->nofppb,
                    'datafetch' => $detail
                ], function ($message) use ($request, $emailictmgr, $detail) {
                    $message->from('info@djabesmen.net', 'Info');
                    $message->to('hannyfauzia2@gmail.com')->subject('Request for approval '.$request->nofppb);
                });
            DB::commit();
            return redirect()->route('reviewict.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppb.' Berhasil di Update ');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            dd($e);
        }      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($notrx)
    {
        $data = DB::table('vw_transaksi')
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

        return view('fppb.detailfppbict',[
                                    'datafetch' => $data,
                                    'header'    => $dataheader,
                                    'datalog'   => $datalog
                                    ]);
    }
}
