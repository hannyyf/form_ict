<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Mail;

class ApproveDICController extends Controller
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
        $data = DB::table('vw_listapprovedic')
                             ->select('*')
                             ->where('isfullapproved','=','0')
                             ->where('approvaltype','=',6)
                             ->where('nikdic','=',$username)
                             ->where('dtfrom','<=',$datenow)
                             ->where('dtthru','>=',$datenow)
                             ->orderBy('notrx','desc')
                             ->get();

        return view('fppb.listapprovedic',['data'=>$data]);
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
        
        switch ($request->input('action')) {
            case 'approve':
                DB::beginTransaction();
                try {
                    // update dtmodified tabel fppb header
                    DB::table('tr_fppb_header')
                        ->where('notrx','=',$request->nofppb)
                        ->update([
                            'dtmodified'        => $datenow,
                            'isfullapproved'    => 1
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
                        'approvaltype'  => 7,
                        'statustype'    => 'Approve',
                        'dtfrom'        => $datenow,
                        'dtthru'        => $dtthru
                    ]);

                    // cek data detail
                    $detail = DB::table('tr_fppb_detail')
                                 ->select('*')
                                 ->where('notrx','=',$request->nofppb)
                                 ->get();

                     // cek data kategori
                    $header = DB::table('tr_fppb_header')
                                 ->select('*')
                                 ->where('notrx','=',$request->nofppb)
                                 ->first();
                                             
                    // cek email ict first layer
                    $ict = DB::table('masterkategori')
                            ->select('*')
                            ->where('idkategori','=',$header->kategorifppb)
                            ->first();
                    $emailict = $ict->email;

                    // fungsi kirim email notifikasi ke ict first layer
                    Mail::send('email.email_ict_mapping', [
                            'nofppb'    => $request->nofppb,
                            'datafetch' => $detail
                        ], function ($message) use ($request, $emailict, $detail) {
                            $message->from('info@djabesmen.net', 'Info');
                            // $message->to($emailict)->subject('Notifikasi request FPPB nomor '.$request->nofppb);
                            $message->to('hannyfauzia2@gmail.com')->subject('Notifikasi request FPPB nomor '.$request->nofppb);
                        });
                    DB::commit();
                    return redirect()->route('approvedic.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppb.' Berhasil di Approve ');
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                    dd($e);
                }                
                break;
            
            case 'reject':
                DB::beginTransaction();
                try {
                    // update ke tabel fppb header
                    DB::table('tr_fppb_header')
                            ->where('notrx','=',$request->nofppb)
                            ->update([
                                'issend'        => 0,
                                'dtmodified'    => $datenow
                             ]);

                    // update current approval
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
                        'approvaltype'  => 7,
                        'statustype'    => 'Reject',
                        'reason'        => $request->reason,
                        'dtfrom'        => $datenow,
                        'dtthru'        => $dtthru
                    ]);

                     // cek requester
                    $getrequester = DB::table('tr_fppb_header')
                                    ->select('*')
                                    ->where('notrx','=',$request->nofppb)
                                    ->first();
                    $requester = $getrequester->requestedby;

                    // cek email user dari master employee
                    $getemail = DB::table('vw_master_employee')
                                ->select('*')
                                ->where('employee_id_bias','=',$requester)
                                ->first();
                    $emailrequester = $getemail->employee_email;

                    // cek data appraiser dari master_employee
                    $getappraiser = DB::table('vw_master_employee')
                                    ->select('*')
                                    ->where('employee_id_bias','=',$username)
                                    ->first();
                    $appraiser = $getappraiser->employee_name;

                    // cek data div head
                    $getdivhead = DB::table('approvalstatus')
                                    ->select('*')
                                    ->where('notrx','=',$request->nofppb)
                                    ->where('approvaltype','=',2)
                                    ->where('statustype','=','Approve')
                                    ->first();
                    $divhead = $getdivhead->nik;
                    
                    // cek email div head
                    $getemaildivhead = DB::table('vw_master_employee')
                                        ->select('*')
                                        ->where('employee_id_bias','=',$divhead)
                                        ->first();
                    $emaildivhead = $getemaildivhead->employee_email;

                    $detail = DB::table('tr_fppb_detail')
                                     ->select('*')
                                     ->where('notrx','=',$request->nofppb)
                                     ->get();

                    // fungsi kirim email notifikasi reject ke user
                    Mail::send('email.email_reject', [
                            'divisi'    => $request->divisi, 
                            'nofppb'    => $request->nofppb,
                            'reason'    => $request->reason,
                            'appraiser' => $appraiser,
                            'datafetch' => $detail
                        ], function ($message) use ($request, $emailrequester, $detail, $appraiser, $emaildivhead) {
                            $message->subject('Notifikasi request FPPB nomor'.$request->nofppb);
                            $message->from('info@djabesmen.net', 'Info');
                            // $message->to($emailrequester);
                            // $message->cc($emaildivhead);
                            $message->to('hannyfauzia2@gmail.com');
                            $message->cc('hannyfauzia2@gmail.com');
                        });
                    DB::commit();
                    return redirect()->route('approvedic.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppb.' telah di reject. Notifikasi email akan disampaikan ke requester.');
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                    dd($e);
                }    
                break;
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

        return view('fppb.detailfppbdic',[
                                    'datafetch' => $data,
                                    'header'    => $dataheader,
                                    'datalog'   => $datalog
                                    ]);
    }
    
}
