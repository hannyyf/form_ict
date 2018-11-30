
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Mail;

class ApproveFPPBDivHeadController extends Controller
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
         $data = DB::select("SELECT * from vw_listapprove 
                    where approvaltype = 1
                    and employee_id_bias = '".$username."'
                    and issend = 1
                    and current_timestamp between dtfrom and dtthru
                    order by notrx desc");
        return view('fppb.listapprovedivhead',['data'=>$data]);
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
                // DB::beginTransaction();
                // try {
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
                        'approvaltype'  => 2,
                        'statustype'    => 'Approve',
                        'dtfrom'        => $datenow,
                        'dtthru'        => $dtthru
                    ]);

                    // update dtmodified tabel fppb header
                    DB::table('tr_fppb_header')
                        ->where('notrx','=',$request->nofppb)
                        ->update([
                            'dtmodified'    => $datenow
                    ]);


                    $getdivfrommaster = DB::table('vw_master_division')
                                        ->select('*')
                                        ->where('div_nama','=',$request->divisi)
                                        ->where('div_id','like','djabesmen%')
                                        ->first();
                    $divcode = $getdivfrommaster->div_id_bias;

                    // cari mappingan divisi dan direktur
                    $getmappingdir     = DB::table('vw_master_direktur')
                                        ->select('*')
                                        ->where('div_id_bias','=',$divcode)
                                        ->first();

                    // // cek email direktur dari master employee
                    // $getemail = DB::table('vw_master_employee')
                    //             ->select('*')
                    //             ->where('employee_id_bias','=',$getmappingdir->nikdirektur)
                    //             ->first();
                    $emaildir = $getmappingdir->employee_email;

                    $detail = DB::table('tr_fppb_detail')
                                     ->select('*')
                                     ->where('notrx','=',$request->nofppb)
                                     ->get();

                    // fungsi kirim email notifikasi ke direktur
                    Mail::send('email.email_direktur', [
                            'divisi'    => $request->divisi, 
                            'nofppb'    => $request->nofppb,
                            'datafetch' => $detail
                        ], function ($message) use ($request, $emaildir, $detail) {
                            $message->from('info@djabesmen.net', 'Info');
                            $message->to($emaildir)->subject('Request for approval '.$request->nofppb);
                        });
                    // DB::commit();
                    return redirect()->route('approvedivhead.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppb.' Berhasil di Approve ');
                // } catch (\Exception $e) {
                //     DB::rollback();
                //     throw $e;
                //     dd($e);
                // }                
                break;
            
            case 'reject':
                // DB::beginTransaction();
                // try {
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
                        'approvaltype'  => 2,
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

                    // cek data appraiser dari master employee
                    $getappraiser = DB::table('vw_master_employee')
                                ->select('*')
                                ->where('employee_id_bias','=',$username)
                                ->first();
                    $appraiser = $getappraiser->employee_name;

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
                        ], function ($message) use ($request, $emailrequester, $detail, $appraiser) {
                            $message->from('info@djabesmen.net', 'Info');
                            $message->to($emailrequester)->subject('Notifikasi request FPPB nomor'.$request->nofppb);
                        });
                    // DB::commit();
                    return redirect()->route('approvedivhead.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppb.' telah di reject. Notifikasi email akan disampaikan ke requester.');
                // } catch (\Exception $e) {
                //     DB::rollback();
                //     throw $e;
                //     dd($e);
                // }    
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

        return view('fppb.detailfppb',[
                                    'datafetch' => $data,
                                    'header'    => $dataheader,
                                    'datalog'   => $datalog
                                    ]);
    }

}
