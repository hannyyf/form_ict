<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Mail;

class RequestCloseController extends Controller
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
        $data = DB::table('vw_listreqclosed')
                             ->select('*')
                             ->where('isfullapproved','=','1')
                             ->whereIn('approvaltype',[9,10])
                             ->where(function ($query) {
                                $query->whereNull('isfullrequestclose')
                                      ->orWhere('isfullrequestclose','=',0);
                             })
                             ->where('nik','=',$username)
                             ->where('dtfrom','<=',$datenow)
                             ->where('dtthru','>=',$datenow)
                             ->orderBy('notrx','desc')
                             ->groupBy('notrx','nik','dtfppb','isfullapproved','div_nama', 'approvaltype','dtfrom', 'dtthru','isfullrequestclose')
                             ->get();

        return view('fppb.listserahterima',['data'=>$data]);
    }

      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRequested()
    {
        date_default_timezone_set('Asia/Jakarta');
        $datenow = date('Y-m-d H:i:s');
        $username = Auth::user()->username;
        $data = DB::table('vw_lisrequestclosed')
                             ->select('*')
                             ->where(function ($query) {
                                 $datenow = date('Y-m-d H:i:s');
                                 $username = Auth::user()->username;
                                 $query->where('approvaltype','=',9)
                                      ->where('isfullrequestclose','=',0)
                                      ->where('dtfrom','<=',$datenow)
                                      ->where('dtthru','>=',$datenow)
                                      ->where('requestedby','=',$username);
                             })
                             ->orWhere('approvaltype','=',10)
                             ->where('requestedby','=',$username)
                             ->where('dtfrom','<=',$datenow)
                             ->where('dtthru','>=',$datenow)
                             ->orderBy('notrx','desc')
                             ->groupBy('notrx','nik','dtfppb','isfullapproved','div_nama', 'approvaltype','dtfrom', 'dtthru','isfullrequestclose','requestedby')
                             ->get();

        return view('fppb.listrequestclose',['data'=>$data]);
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

        foreach ($request->selected as $key => $value) {
            $updatereqclosed = DB::table('tr_fppb_detail')
                                    ->where('notrx','=',$request->nofppb)
                                    ->where('seqid','=',$request->selected[$key])
                                    ->update([
                                        'isrequestclosed'  => 1,
                                        'dtrequestclosed'  => $datenow
                                    ]);
        }

        // cek data detail
        $detail = DB::table('tr_fppb_detail')
                     ->select('*')
                     ->where('notrx','=',$request->nofppb)
                     ->where('isrequestclosed','=',1)
                     ->get();

         // cek requester
        $getrequester = DB::table('approvalstatus')
                        ->select('*')
                        ->where('notrx','=',$request->nofppb)
                        ->where('approvaltype','=',1)
                        ->first();
        $requester = $getrequester->nik;

        // cek email user dari master employee
        $getemail = DB::table('vw_master_employee')
                    ->select('*')
                    ->where('employee_id_bias','=',$requester)
                    ->first();
        $emailrequester = $getemail->employee_email;

        // fungsi kirim email notifikasi reject ke user
        Mail::send('email.email_requestclosed', [
                'nofppb'    => $request->nofppb,
                'datafetch' => $detail
            ], function ($message) use ($request, $emailrequester, $detail) {
                $message->subject('Request closed FPPB nomor'.$request->nofppb);
                $message->from('info@djabesmen.net', 'Info');
                $message->to($emailrequester);
            });

        // cek masih ada yang belum di request closed atau engga
        $getdetail = DB::table('tr_fppb_detail')
                        ->select('*')
                        ->where('notrx','=',$request->nofppb)
                        ->where(function ($query) {
                                $query->whereNull('isrequestclosed')
                                      ->orWhere('isrequestclosed','=',0);
                             })
                        ->first();
         // jika detailnya sudah di request closed semua               
        if (is_null($getdetail)) {
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
                'approvaltype'  => 10,
                'statustype'    => '',
                'dtfrom'        => $datenow,
                'dtthru'        => $dtthru
            ]);

            // update dtmodified tabel fppb header
            DB::table('tr_fppb_header')
                ->where('notrx','=',$request->nofppb)
                ->update([
                    'dtmodified'            => $datenow,
                    'isfullrequestclose'    => 1
                ]);
        } else {
            // update dtmodified tabel fppb header
            DB::table('tr_fppb_header')
                ->where('notrx','=',$request->nofppb)
                ->update([
                    'dtmodified'            => $datenow,
                    'isfullrequestclose'    => 0
                ]);
        }

        return redirect()->route('serahterima.index')->with('alert-success','Permintaan penutupan request FPPB dengan nomor '.$request->nofppb.' Berhasil di Update ');
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
                     ->where(function ($query) {
                        $query->whereNull('isrequestclosed')
                              ->orWhere('isrequestclosed','=',0);
                     })
                     ->get();

        $kodeitems  = DB::table('generate_pr')
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

        return view('fppb.detailrequestclosed',[
                                    'datafetch' => $data,
                                    'header'    => $dataheader,
                                    'datalog'   => $datalog,
                                    'kodeitems' => $kodeitems
                                    ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editRequested($notrx)
    {
        $data = DB::table('vw_transaksi')
                     ->select('*')
                     ->where('notrx','=',$notrx)
                     ->where('isrequestclosed','=',1)
                     ->get();

        $dataheader = DB::table('vw_listrequest')
                     ->select('*')
                     ->where('notrx','=',$notrx)
                     ->first();

        $kodeitems  = DB::table('generate_pr')
                        ->select('*')
                        ->where('notrx','=',$notrx)
                        ->get();

        $datalog    = DB::table('vw_log')
                     ->select('*')
                     ->where('notrx','=',$notrx)
                     ->orderBy('dtfrom','asc')
                     ->get();

        return view('fppb.detailrequestedclosed',[
                                    'datafetch' => $data,
                                    'header'    => $dataheader,
                                    'datalog'   => $datalog,
                                    'kodeitems' => $kodeitems
                                    ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeEdit(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $datenow = date('Y-m-d H:i:s');
        $username = Auth::user()->username;
        $dtthru = '2079-06-06 23:59:00'; // nilai maksimum untuk type smalldatetime

        switch ($request->input('action')) {
            case 'closed':
            foreach ($request->selected as $key => $value) {
            DB::table('tr_fppb_detail')
                ->where('notrx','=',$request->nofppb)
                ->where('seqid','=',$request->selected[$key])
                ->update([
                    'isrequestclosed'   => 2,
                    'isitemclosed'      => 1,
                    'dtitemclosed'      => $datenow
                ]);

             $getdetailclosed =  DB::table('tr_fppb_detail')
                                    ->select('*')
                                    ->where('notrx','=',$request->nofppb)
                                    ->where('seqid','=',$request->selected[$key])
                                    ->get();
            }


            
            // cek masih ada yang belum di closed atau engga
            $getdetail = DB::table('tr_fppb_detail')
                            ->select('*')
                            ->where('notrx','=',$request->nofppb)
                            ->where(function ($query) {
                                $query->whereNull('isitemclosed')
                                      ->orWhere('isitemclosed','=',0);
                             })
                            ->first();

             // jika detailnya sudah di closed semua               
            if (is_null($getdetail)) {
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
                    'approvaltype'  => 11,
                    'statustype'    => '',
                    'dtfrom'        => $datenow,
                    'dtthru'        => $dtthru
                ]);

                // update dtmodified tabel fppb header
                DB::table('tr_fppb_header')
                    ->where('notrx','=',$request->nofppb)
                    ->update([
                        'dtmodified'    => $datenow,
                        'isclosed'      => 1,
                        'dtclosed'      => $datenow
                    ]);

            } else {
                // update dtmodified tabel fppb header
                DB::table('tr_fppb_header')
                    ->where('notrx','=',$request->nofppb)
                    ->update([
                        'dtmodified'    => $datenow,
                        'isclosed'      => 0,
                    ]);
            }

            // cek ke tabel header, masuk kategori mana
            $header = DB::table('tr_fppb_header')
                            ->select('*')
                            ->where('notrx','=',$request->nofppb)
                            ->first();
            $kategori = DB::table('masterkategori')
                            ->select('*')
                            ->where('idkategori','=',$header->kategorifppb)
                            ->first();
            $emailict = $kategori->email;

            // fungsi kirim email notifikasi close ke ict first layer
            Mail::send('email.email_close', [
                    'nofppb'    => $request->nofppb,
                    'datafetch' => $getdetailclosed
                ], function ($message) use ($request, $emailict, $getdetailclosed) {
                    $message->subject('Notifikasi Request close FPPB nomor '.$request->nofppb);
                    $message->from('info@djabesmen.net', 'Info');
                    $message->to($emailict);
                });

                return redirect()->route('serahterima.reindex')->with('alert-success','Request FPPB dengan nomor '.$request->nofppb.' Berhasil di Closed ');
            break;

            case 'reject':
            foreach ($request->selected as $key => $value) {
            $getdetailreject = DB::table('tr_fppb_detail')
                                    ->select('*')
                                    ->where('notrx','=',$request->nofppb)
                                    ->where('seqid','=',$request->selected[$key])
                                    ->get();

            // update isrequested closed jadi 0
            DB::table('tr_fppb_detail')
                            ->where('notrx','=',$request->nofppb)
                            ->where('seqid','=',$request->selected[$key])
                            ->update([
                                'isrequestclosed'    => 0
                             ]);
            }
            // update ke tabel fppb header
            DB::table('tr_fppb_header')
                    ->where('notrx','=',$request->nofppb)
                    ->update([
                        'dtmodified'            => $datenow,
                        'isfullrequestclose'    => 0
                     ]);


            // cek ke tabel header, masuk kategori mana
            $header = DB::table('tr_fppb_header')
                            ->select('*')
                            ->where('notrx','=',$request->nofppb)
                            ->first();
            $kategori = DB::table('masterkategori')
                            ->select('*')
                            ->where('idkategori','=',$header->kategorifppb)
                            ->first();
            $emailict = $kategori->email;

            // fungsi kirim email notifikasi reject ke user
            Mail::send('email.email_reject_user', [
                    'nofppb'    => $request->nofppb,
                    'reason'    => $request->reason,
                    'datafetch' => $getdetailreject
                ], function ($message) use ($request, $emailict, $getdetailreject) {
                    $message->subject('Request closed FPPB nomor'.$request->nofppb);
                    $message->from('info@djabesmen.net', 'Info');
                    $message->to($emailict);
                });
         
            return redirect()->route('serahterima.reindex')->with('alert-success','Request FPPB dengan nomor '.$request->nofppb.' Berhasil di Update ');
            break;
        }
    }


}
