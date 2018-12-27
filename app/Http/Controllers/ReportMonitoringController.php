<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Mail;

class ReportMonitoringController extends Controller
{
    public function index()
    {
        $username = Auth::user()->username;
		$role = Auth::user()->jabatan;
        $getdiv = DB::table('vw_profile_karyawan')
                        ->select('*')
                        ->where('employee_id_bias','=',$username)
                        ->first();
        $divuser = $getdiv->div_id_bias;
		
		if ($role == 'ict') {
			$datas  = DB::table('vw_listreport')
                    ->select('*')
                    ->orderBy('notrx','asc')
                    ->get();
		} else {
			$datas  = DB::table('vw_listreport')
                    ->select('*')
                    ->where('divcode','=',$divuser)
                    ->orderBy('notrx','asc')
                    ->get();
		}
        

        return view('fppb.listreport',['datas' => $datas]);
    }

    public function indexTransfer()
    {
        $username = Auth::user()->username;
        $datas  = DB::table('vw_listtransfer')
                    ->select('*')
                    ->where('nik','=',$username)
                    ->orderBy('notrx','asc')
                    ->get();

        return view('fppb.listtransfer',['datas' => $datas]);
    }


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

     public function editTransfer($notrx)
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


        return view('fppb.detailtransfer',[
                                    'datafetch' => $data,
                                    'header'    => $dataheader,
                                    'datalog'   => $datalog,
                                    'kodeitems' => $kodeitems
                                    ]);
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

    public function findTransfer(Request $request) {
        $nofppb     = $request->get('nofppb');
        $datas      = DB::table('vw_listtransfer')
                        ->select('*')
                        ->where('notrx','=',$nofppb)
                        ->orderBy('notrx','asc')
                        ->get();
        return view('fppb.listtransfer',['datas' => $datas]);
    }

    public function getKategori(Request $request) {
        $data      = DB::table('masterkategori')
                        ->select('*')
                        ->where('deskripsi','like','operational%')
                        ->get();
        $output = '<option value="">--Pilih Kategori--</option>';
            foreach ($data as $row) {
                $output .= '<option value="' .$row->idkategori.'">'.$row->deskripsi.'</option>';
            }
            
        echo $output;
    }

     public function updateKategori(Request $request) {
        DB::table('tr_fppb_header')
            ->where('notrx','=',$request->nofppbmodal)
            ->update([
                'kategorifppb'  => $request->kategorimodal
            ]);

        // cek data detail
        $detail = DB::table('tr_fppb_detail')
                     ->select('*')
                     ->where('notrx','=',$request->nofppbmodal)
                     ->get();


        // cek email ict first layer
        $ict = DB::table('masterkategori')
                ->select('*')
                ->where('idkategori','=',$request->kategorimodal)
                ->first();
        $emailict = $ict->email;
        $keterangan = $request->keteranganmodal;

        // fungsi kirim email notifikasi ke ict first layer
        Mail::send('email.email_transfer', [
                'nofppb'    => $request->nofppbmodal,
                'datafetch' => $detail,
                'keterangan' => $keterangan
            ], function ($message) use ($request, $emailict, $detail, $keterangan) {
                $message->from('info@djabesmen.net', 'Info');
                $message->to($emailict)->subject('Informasi transfer FPPB nomor '.$request->nofppbmodal);
            });

        return redirect()->route('transfer.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppbmodal.' Berhasil di Transfer ! ');
    }
}
