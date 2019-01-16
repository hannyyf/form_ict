<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Mail;

class ReportMonitoringController extends Controller
{

    public function connectqaddjm(){
        $conn =  odbc_connect('QAD42','sysprogress','s',SQL_CUR_USE_ODBC)or die('Could not connect: ' . odbc_error());

        return $conn;
    }

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
        $datas       = DB::table('vw_transaksi')
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
                        ->select('kodeitem', 'seqid')
                        ->where('notrx','=',$notrx)
                        ->get();

        return view('fppb.detailreport',[
                                    'datafetch' => $datas,
                                    'header'    => $dataheader,
                                    'datalog'   => $datalog,
                                    'kodeitems' => $kodeitems,
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

    public function getdetail(Request $request) {
        $kodeitem   = $request->get('kodeitem');
        $nopr       = $request->get('nopr');
        
        $con = $this->connectqaddjm();
        $query = odbc_exec($con,"select a.rqd_nbr, a.rqd_line, b.pod_nbr, c.prh_line, c.prh_receiver, c.prh_rcp_date, d.po_ord_date from PUB.rqd_det a 
            left join PUB.pod_det b ON (a.rqd_nbr = b.pod_req_nbr and a.rqd_line = b.pod_req_line)
            left join PUB.prh_hist c ON (b.pod_nbr = c.prh_nbr and b.pod_line = c.prh_line)
            left join PUB.po_mstr d ON (d.po_nbr = b.pod_nbr)
            where a.rqd_nbr = '".$nopr."' and a.rqd_part = '".$kodeitem."'");
        // where a.rqd_nbr = 'R1805050' and a.rqd_part = '2105013002' ");

        $myarr = array();
        while( $data=odbc_fetch_array($query)) {
            array_push($myarr, $data);
        };
        $no = 1;
        if(empty($myarr)) {

            $output = '<tr>
                        <td colspan="7">Belum ada kode item yang di mapping</td>
                        </tr>';
        } else {
            $output = '<tr>
                            <td>'.$no++.'</td>
                            <td>'.$kodeitem.'</td>
                            <td>'.$nopr.'</td>
                            <td>'.$myarr[0]['pod_nbr'].'</td>
                            <td>'.$myarr[0]['po_ord_date'].'</td>
                            <td>'.$myarr[0]['prh_receiver'].'</td>
                            <td>'.$myarr[0]['prh_rcp_date'].'</td>
                        </tr>';
        }
        

        echo $output;
    }

    public function getdetailpr(Request $request) {
        $nopr       = $request->get('nopr');
        
        $con = $this->connectqaddjm();
        $query = odbc_exec($con,"select a.rqd_nbr, a.rqd_line, a.rqd_part, b.pod_nbr, c.prh_line, c.prh_receiver, c.prh_rcp_date, d.po_ord_date from PUB.rqd_det a 
            left join PUB.pod_det b ON (a.rqd_nbr = b.pod_req_nbr and a.rqd_line = b.pod_req_line)
            left join PUB.prh_hist c ON (b.pod_nbr = c.prh_nbr and b.pod_line = c.prh_line)
            left join PUB.po_mstr d ON (d.po_nbr = b.pod_nbr)
            where a.rqd_nbr = '".$nopr."'");
        // where a.rqd_nbr = 'R1805050' and a.rqd_part = '2105013002' ");

        $myarr = array();
        while( $data=odbc_fetch_array($query)) {
            array_push($myarr, $data);
        };
        $no = 1;
        if(empty($myarr)) {

            $output = '<tr>
                        <td colspan="7">Belum ada kode item yang di mapping</td>
                        </tr>';
        } else {

                $output = '<div style="display:none">';
            foreach ($myarr as $key => $value) {
                $output .= '<tr>
                            <td>'.$no++.'</td>
                            <td>'.$myarr[$key]['rqd_part'].'</td>
                            <td>'.$myarr[$key]['rqd_nbr'].'</td>
                            <td>'.$myarr[$key]['pod_nbr'].'</td>
                            <td>'.$myarr[$key]['po_ord_date'].'</td>
                            <td>'.$myarr[$key]['prh_receiver'].'</td>
                            <td>'.$myarr[$key]['prh_rcp_date'].'</td>
                        </tr>';
            }
                $output .= '</div>';
        }
        

        echo $output;

    }
}
