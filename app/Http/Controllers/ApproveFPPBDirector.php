<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Mail;

class ApproveFPPBDirector extends Controller
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
        $data = DB::select("SELECT * from vw_listapprovedir 
                                where approvaltype = 2
                                and employee_id_bias = '".$username."'
                                and issend = 1
                                and current_timestamp between dtfrom and dtthru
                                order by notrx desc");
        return view('fppb.listapprovedirector',['data'=>$data]);
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
                            'dtmodified'    => $datenow,
                            'notedir'       => $request->notedir
                        ]);

                    // update flaging isreviewed tabel fppb detail
                    DB::table('tr_fppb_detail')
                        ->where('notrx','=',$request->nofppb)
                        ->update([
                            'isreviewed'    => 1,
                            'dtreviewed'    => $datenow
                        ]);

                    // update current status approval
                    DB::table('approvalstatus')
                            ->where('notrx','=',$request->nofppb)
                            ->where('dtfrom','<=',$datenow)
                            ->where('dtthru','>=',$datenow)
                            ->update([
                                'dtthru'     => $datenow
                             ]);

                    $cekdirektorat  = DB::table('vw_master_direktur')
                                        ->select('*')
                                        ->where('employee_id_bias','=',$username)
                                        ->first();

                    $jabatan = $cekdirektorat->employee_jabatan;
                    if ($jabatan == 'Plant Director') {
                        // insert new status approval
                        DB::table('approvalstatus')->insert([
                            'idstatus'      => Uuid::uuid4()->getHex(),
                            'notrx'         => $request->nofppb,
                            'nik'           => $username,
                            'approvaltype'  => 3,
                            'statustype'    => 'Approve',
                            'dtfrom'        => $datenow,
                            'dtthru'        => $dtthru
                        ]);
                    } elseif ($jabatan == 'Commercial Director') {
                        // insert new status approval
                        DB::table('approvalstatus')->insert([
                            'idstatus'      => Uuid::uuid4()->getHex(),
                            'notrx'         => $request->nofppb,
                            'nik'           => $username,
                            'approvaltype'  => 4,
                            'statustype'    => 'Approve',
                            'dtfrom'        => $datenow,
                            'dtthru'        => $dtthru
                        ]);
                    } else {
                        // insert new status approval
                        DB::table('approvalstatus')->insert([
                            'idstatus'      => Uuid::uuid4()->getHex(),
                            'notrx'         => $request->nofppb,
                            'nik'           => $username,
                            'approvaltype'  => 5,
                            'statustype'    => 'Approve',
                            'dtfrom'        => $datenow,
                            'dtthru'        => $dtthru
                        ]);
                    }
             
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
                    Mail::send('email.email_ict', [
                            'nofppb'    => $request->nofppb,
                            'datafetch' => $detail
                        ], function ($message) use ($request, $emailict, $detail) {
                            $message->from('info@djabesmen.net', 'Info');
                            $message->to($emailict)->subject('Request for review '.$request->nofppb);
                        });
                    
                    DB::commit();
                    return redirect()->route('approvedirector.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppb.' Berhasil di Approve ');
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
                $cekdirektorat  = DB::table('vw_master_direktur')
                            ->select('*')
                            ->where('employee_id_bias','=',$username)
                            ->first();

                $jabatan = $cekdirektorat->employee_jabatan;
                if ($jabatan == 'Plant Director') {
                    // insert new status approval
                    DB::table('approvalstatus')->insert([
                        'idstatus'      => Uuid::uuid4()->getHex(),
                        'notrx'         => $request->nofppb,
                        'nik'           => $username,
                        'approvaltype'  => 3,
                        'statustype'    => 'Reject',
                        'reason'        => $request->reason,
                        'dtfrom'        => $datenow,
                        'dtthru'        => $dtthru
                    ]);
                } elseif ($jabatan == 'Commercial Director') {
                    // insert new status approval
                    DB::table('approvalstatus')->insert([
                        'idstatus'      => Uuid::uuid4()->getHex(),
                        'notrx'         => $request->nofppb,
                        'nik'           => $username,
                        'approvaltype'  => 4,
                        'statustype'    => 'Reject',
                        'reason'        => $request->reason,
                        'dtfrom'        => $datenow,
                        'dtthru'        => $dtthru
                    ]);
                } else {
                    // insert new status approval
                    DB::table('approvalstatus')->insert([
                        'idstatus'      => Uuid::uuid4()->getHex(),
                        'notrx'         => $request->nofppb,
                        'nik'           => $username,
                        'approvaltype'  => 5,
                        'statustype'    => 'Reject',
                        'reason'        => $request->reason,
                        'dtfrom'        => $datenow,
                        'dtthru'        => $dtthru
                    ]);
                }
        
                    // cek requester
                    $getrequester = DB::table('vw_header')
                                    ->select('*')
                                    ->where('notrx','=',$request->nofppb)
                                    ->first();
                    $emailrequester = $getrequester->employee_email;

                     // case jika email di dbmastercontroll kosong ambil dari tabel user
                    if(empty($emailrequester) || is_null($emailrequester)) {
                        $user = DB::table('users')
                                ->select('*')
                                ->where('username','=',$requester)
                                ->first();
                        $emailrequester = $user->email;
                    }

                    // cek data appraiser dari master_employee
                    $getappraiser = DB::table('vw_master_employee')
                                    ->select('*')
                                    ->where('employee_id_bias','=',$username)
                                    ->first();
                    $appraiser = $getappraiser->employee_name;

                    // cek data history approver
                    $getlog = DB::table('vw_log')
                                ->select('*')
                                ->where('notrx','=',$request->nofppb)
                                ->where('statustype','=','Approve')
                                ->get();

                    $emails = array();
                    foreach ($getlog as $key => $value) {
                        array_push($emails, $getlog[$key]->employee_email);
                    }

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
                        ], function ($message) use ($request, $emailrequester, $detail, $appraiser, $emails) {
                            $message->subject('Notifikasi request FPPB nomor'.$request->nofppb);
                            $message->from('info@djabesmen.net', 'Info');
                            $message->to($emailrequester);
                            $message->cc($emails);
                        });
                    DB::commit();
                    return redirect()->route('approvedirector.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppb.' telah di reject. Notifikasi email akan disampaikan ke requester.');
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

        return view('fppb.detailfppbdir',[
                                    'datafetch' => $data,
                                    'header'    => $dataheader,
                                    'datalog'   => $datalog
                                    ]);
    }

}
