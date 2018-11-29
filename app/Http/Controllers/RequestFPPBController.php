<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FPPBHeader;
use App\FPPBDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Contracts\Logging;
use Illuminate\Support\Facades\Auth;
use Mail;
use Ramsey\Uuid\Uuid;
use Validator;

class RequestFPPBController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $username = Auth::user()->username;
        $data = DB::table('vw_listrequest')
                             ->select('*')
                             ->where('issend','=','0')
                             ->where('requestedby','=',$username)
                             ->orderBy('notrx','desc')
                             ->get();
        return view('fppb.listrequest',['data'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $username = Auth::user()->username;
        $getmappingemployee = DB::table('vw_profile_karyawan')
                                ->select('*')
                                ->where('employee_id_bias','=',$username)
                                ->first();
        //$getdiv = $getmappingemployee->div_nama;

        $getdivisi = DB::table('vw_master_division')
                        ->select('*')
                        ->where('div_id','like','djabesmenU%')
                        ->get();

        $getkategori = DB::table('masterkategori')
                        ->select('*')
                        ->where('deskripsi','like','operational%')
                        ->get();

        $getproduct = DB::table('master_product')
                        ->select('*')
                        ->orderBy('nmprod','ASC')
                        ->get();

        $getuom = DB::table('master_uom')
                    ->select('*')
                    ->get();

        return view('fppb.input',[
                'getdivision'   => $getdivisi,
                'getkategori'   => $getkategori,
                'products'      => $getproduct,
                'getuom'        => $getuom,
                'divisi'        => $getmappingemployee
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        date_default_timezone_set('Asia/Jakarta');
        $datenow = date('Y-m-d H:i:s');
        // cek data user login
        $username = Auth::user()->username;
        $dtthru = '2079-06-06 23:59:00'; // nilai maksimum untuk type smalldatetime

        switch ($request->input('action')) {
            case 'save':
                DB::beginTransaction();
                try {
                    // cek nomor terakhir
                    $getlastdata    = DB::table('tr_fppb_header')
                                            ->select('*')
                                            ->orderBy('notrx','desc')
                                            ->first();
                    if (is_null($getlastdata)) {
                        $jumlah = 1;
                    } else {
                        $getlastnum     = $getlastdata->notrx;
                        $arr_lastnum    = explode("-", $getlastnum);
                        $monthyear      = $arr_lastnum[1]; // ambil bulan dan tahun dari no fppb
                        $year           = substr($monthyear,2); //ambil tahunnya
                        $yearnow        = date('Y');
                        
                        // reset numbering jika ganti tahun
                        if ($year == $yearnow) {
                            $lastnum        = $arr_lastnum[2];
                            $jumlah         = intval($lastnum);
                            $jumlah++;
                        } else {
                            $jumlah = 1;
                        }
                        
                    }

                    //format numbering                
                    $numbering = 'FPPB-'.date('mY').'-'.sprintf("%03d",$jumlah);

                    //insert ke tabel trxfppbheader
                    FPPBHeader::insert([
                        'notrx'         => $numbering,
                        'divcode'       => $request->divisi,
                        'dtfppb'        => $datenow,
                        'issend'        => 0,
                        'kategorifppb'  => $request->kategori,
                        'requestedby'   => $username
                    ]);

                    foreach ($request->no as $key => $no) {
                        $qty = intval(str_replace(',','',$request->qty[$key]));

                        if ($request->jenisbarang[$key] == 'other') {
                            
                            DB::table('tr_fppb_detail')
                                ->insert([
                                    'notrx'         => $numbering,
                                    'seqid'         => $request->no[$key],
                                    'jenisbarang'   => $request->product[$key],
                                    'qty'           => $qty,
                                    'satuan'        => $request->satuan[$key],
                                    'tglpakai'      => $request->tanggalpakai[$key],
                                    'notemanfaat'   => $request->keterangan[$key],
                                    'kodeitem'      => $request->kodeitem[$key]
                                ]);
                        } else {

                            $product = DB::table('master_product')
                                        ->select('*')
                                        ->where('idqad','=',$request->jenisbarang[$key])
                                        ->first();
                            $productname = $product->nmprod;

                            DB::table('tr_fppb_detail')
                                ->insert([
                                    'notrx'         => $numbering,
                                    'seqid'         => $request->no[$key],
                                    'jenisbarang'   => $productname,
                                    'kodeitem'      => $request->kodeitem[$key],
                                    'qty'           => $qty,
                                    'satuan'        => $request->satuan[$key],
                                    'tglpakai'      => $request->tanggalpakai[$key],
                                    'notemanfaat'   => $request->keterangan[$key]
                                ]);
                        }
                    }

                    DB::commit();
                    return redirect()->route('requestfppb.index')->with('alert-success','Data Berhasil Tersimpan dengan nomor '.$numbering);
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                    dd($e);
                }
                
                break;
            
            case 'send':
                DB::beginTransaction();
                try {
                    // cek nomor terakhir
                    $getlastdata    = DB::table('tr_fppb_header')
                                            ->select('*')
                                            ->orderBy('notrx','desc')
                                            ->first();
                    if (is_null($getlastdata)) {
                        $jumlah = 1;
                    } else {
                        $getlastnum     = $getlastdata->notrx;
                        $arr_lastnum    = explode("-", $getlastnum);
                        $monthyear      = $arr_lastnum[1]; // ambil bulan dan tahun dari no fppb
                        $year           = substr($monthyear,2); //ambil tahunnya
                        $yearnow        = date('Y');
                        
                        // reset numbering jika ganti tahun
                        if ($year == $yearnow) {
                            $lastnum        = $arr_lastnum[2];
                            $jumlah         = intval($lastnum);
                            $jumlah++;
                        } else {
                            $jumlah = 1;
                        }
                    }

                    //format numbering                
                    $numbering = 'FPPB-'.date('mY').'-'.sprintf("%03d",$jumlah);

                    //insert ke tabel trxfppbheader
                    FPPBHeader::insert([
                        'notrx'         => $numbering,
                        'divcode'       => $request->divisi,
                        'dtfppb'        => $datenow,
                        'issend'        => 1,
                        'kategorifppb'  => $request->kategori,
                        'requestedby'   => $username
                    ]);

                    foreach ($request->no as $key => $no) {
                        $qty = intval(str_replace(',','',$request->qty[$key]));
                        if ($request->jenisbarang[$key] == 'other') {
                            
                            DB::table('tr_fppb_detail')
                                ->insert([
                                    'notrx'         => $numbering,
                                    'seqid'         => $request->no[$key],
                                    'jenisbarang'   => $request->product[$key],
                                    'kodeitem'      => $request->kodeitem[$key],
                                    'qty'           => $qty,
                                    'satuan'        => $request->satuan[$key],
                                    'tglpakai'      => $request->tanggalpakai[$key],
                                    'notemanfaat'   => $request->keterangan[$key]
                                ]);
                        } else {
                            // cari deskripsi product
                            $product = DB::table('master_product')
                                        ->select('*')
                                        ->where('idqad','=',$request->jenisbarang[$key])
                                        ->first();
                            $productname = $product->nmprod;

                            DB::table('tr_fppb_detail')
                                ->insert([
                                    'notrx'         => $numbering,
                                    'seqid'         => $request->no[$key],
                                    'jenisbarang'   => $productname,
                                    'kodeitem'      => $request->kodeitem[$key],
                                    'qty'           => $qty,
                                    'satuan'        => $request->satuan[$key],
                                    'tglpakai'      => $request->tanggalpakai[$key],
                                    'notemanfaat'   => $request->keterangan[$key]
                                ]);
                        }
                    }

                    DB::table('approvalstatus')->insert([
                        'idstatus'      => Uuid::uuid4()->getHex(),
                        'notrx'         => $numbering,
                        'nik'           => $username,
                        'approvaltype'  => 1,
                        'statustype'    => '',
                        'dtfrom'        => $datenow,
                        'dtthru'        => $dtthru
                    ]);

                    // cek data spv ke table master fppb approval div head user
                    $getdataspv = DB::table('vw_master_divhead')
                                        ->select('*')
                                        ->where('div_id_bias','=',$request->divisi)
                                        ->first();

                    // cek email spv dari master employee
                    $getemail = DB::table('vw_master_employee')
                                        ->select('*')
                                        ->where('employee_id_bias','=',$getdataspv->employee_id_bias)
                                        ->first();
                    $emailspv = $getemail->employee_email; // set email

                    //get data division
                    $getdivfrommaster = DB::table('vw_master_division')
                                            ->select('*')
                                            ->where('div_id_bias','=',$request->divisi)
                                            ->where('div_id','like','djabesmen%')
                                            ->first();
                    $divnama = $getdivfrommaster->div_nama;

                    $detail = DB::table('tr_fppb_detail')
                                 ->select('*')
                                 ->where('notrx','=',$numbering)
                                 ->get();

                    Mail::send('email.email', [
                                'divisi'    => $divnama, 
                                'nofppb'    => $numbering,
                                'datafetch' => $detail
                            ], function ($message) use ($request, $emailspv, $numbering, $divnama, $detail) {
                            $message->from('info@djabesmen.net', 'Info');
                            $message->to('hannyfauzia2@gmail.com')->subject('Request for approval '.$numbering);
                    });

                    DB::commit();

                    return redirect()->route('requestfppb.index')->with('alert-success','Data Berhasil Terkirim dengan nomor '.$numbering);
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

        $getkategori = DB::table('masterkategori')
                     ->select('*')
                     ->get();

        $getproduct = DB::table('master_product')
                    ->select('*')
                    ->orderBy('nmprod','ASC')
                    ->get();

         $getuom = DB::table('master_uom')
                    ->select('*')
                    ->get();

        return view('fppb.edit_fppb',[
                                    'datafetch'     => $data,
                                    'getkategori'   => $getkategori,
                                    'header'        => $dataheader,
                                    'products'      => $getproduct,
                                    'getuom'        => $getuom
                                    ]);
    }

    public function storeEdit(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $datenow = date('Y-m-d H:i:s');
        $username = Auth::user()->username;  // cek data user login
        $dtthru = '2079-06-06 23:59:00'; // nilai maksimum untuk type smalldatetime
        switch ($request->input('action')) {
            case 'update':
            DB::beginTransaction();
            try {
                // update dtmodified tabel fppb header
                DB::table('tr_fppb_header')
                    ->where('notrx','=',$request->nofppb)
                    ->update([
                        'dtmodified'    => $datenow
                    ]);

                foreach ($request->no as $key => $value) {
                $seqid = $request->no[$key];
                $qty = intval(str_replace(',','',$request->qty[$key]));

                // cek data detailnya sudah ada atau belum
                $data = DB::table('tr_fppb_detail')
                             ->select('*')
                             ->where('notrx','=',$request->nofppb)
                             ->where('seqid','=',$seqid)
                             ->first();

                if (is_null($data)) {
                    if ($request->jenisbarang[$key] == 'other') {
                            
                            DB::table('tr_fppb_detail')
                                ->insert([
                                    'notrx'         => $request->nofppb,
                                    'seqid'         => $request->no[$key],
                                    'jenisbarang'   => $request->product[$key],
                                    'qty'           => $qty,
                                    'satuan'        => $request->satuan[$key],
                                    'tglpakai'      => $request->tanggalpakai[$key],
                                    'notemanfaat'   => $request->keterangan[$key]
                                ]);
                        } else {

                            $product = DB::table('master_product')
                                        ->select('*')
                                        ->where('idqad','=',$request->jenisbarang[$key])
                                        ->first();
                            $productname = $product->nmprod;

                            DB::table('tr_fppb_detail')
                                ->insert([
                                    'notrx'         => $request->nofppb,
                                    'seqid'         => $request->no[$key],
                                    'jenisbarang'   => $productname,
                                    'kodeitem'      => $request->kodeitem[$key],
                                    'qty'           => $qty,
                                    'satuan'        => $request->satuan[$key],
                                    'tglpakai'      => $request->tanggalpakai[$key],
                                    'notemanfaat'   => $request->keterangan[$key]
                                ]);
                        }

                } else {
                    if (is_null($request->kodeitem[$key])) {
                            DB::table('tr_fppb_detail')
                                ->where('notrx','=',$request->nofppb)
                                ->where('seqid','=',$request->no[$key])
                                ->update([
                                    'notrx'         => $request->nofppb,
                                    'seqid'         => $request->no[$key],
                                    'jenisbarang'   => $request->product[$key],
                                    'qty'           => $qty,
                                    'satuan'        => $request->satuan[$key],
                                    'tglpakai'      => $request->tanggalpakai[$key],
                                    'notemanfaat'   => $request->keterangan[$key]
                                ]);
                    } else {

                        $product = DB::table('master_product')
                                ->select('*')
                                ->where('idqad','=',$request->jenisbarang[$key])
                                ->first();
                        $productname = $product->nmprod;

                        DB::table('tr_fppb_detail')
                            ->where('notrx','=',$request->nofppb)
                            ->where('seqid','=',$request->no[$key])
                            ->update(
                                ['notrx'        => $request->nofppb,
                                 'seqid'        => $request->no[$key],
                                 'jenisbarang'  => $productname,
                                 'kodeitem'     => $request->kodeitem[$key],
                                 'qty'          => $qty,
                                 'satuan'       => $request->satuan[$key],
                                 'tglpakai'     => $request->tanggalpakai[$key],
                                 'notemanfaat'  => $request->keterangan[$key]
                             ]);
                    }
                    
                    }
                }
                DB::commit();
                return redirect()->route('requestfppb.index')->with('alert-success','Data berhasil diubah!');
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
                dd($e);
            }            
            break;
            
            case 'send':
                DB::beginTransaction();
                try {

                    // update tabel fppb header
                    DB::table('tr_fppb_header')
                        ->where('notrx','=',$request->nofppb)
                        ->update([
                            'issend'        => 1,
                            'dtmodified'    => $datenow
                        ]);
                    
                    foreach ($request->no as $key => $no) {
                    $seqid = $request->no[$key];
                    $qty = intval(str_replace(',','',$request->qty[$key]));

                    // cek data detailnya sudah ada atau belum
                    $data = DB::table('tr_fppb_detail')
                                 ->select('*')
                                 ->where('notrx','=',$request->nofppb)
                                 ->where('seqid','=',$seqid)
                                 ->first();

                    if (is_null($data)) {
                        if ($request->jenisbarang[$key] == 'other') {
                                
                                DB::table('tr_fppb_detail')
                                    ->insert([
                                        'notrx'         => $request->nofppb,
                                        'seqid'         => $request->no[$key],
                                        'jenisbarang'   => $request->product[$key],
                                        'kodeitem'      => $request->kodeitem[$key],
                                        'qty'           => $qty,
                                        'satuan'        => $request->satuan[$key],
                                        'tglpakai'      => $request->tanggalpakai[$key],
                                        'notemanfaat'   => $request->keterangan[$key]
                                    ]);
                            } else {
                                $product = DB::table('master_product')
                                            ->select('*')
                                            ->where('idqad','=',$request->jenisbarang[$key])
                                            ->first();
                                $productname = $product->nmprod;

                                DB::table('tr_fppb_detail')
                                    ->insert([
                                        'notrx'         => $request->nofppb,
                                        'seqid'         => $request->no[$key],
                                        'jenisbarang'   => $productname,
                                        'kodeitem'      => $request->kodeitem[$key],
                                        'qty'           => $qty,
                                        'satuan'        => $request->satuan[$key],
                                        'tglpakai'      => $request->tanggalpakai[$key],
                                        'notemanfaat'   => $request->keterangan[$key]
                                    ]);
                            }

                    } else {
                        if (is_null($request->kodeitem[$key])) {
                                DB::table('tr_fppb_detail')
                                    ->where('notrx','=',$request->nofppb)
                                    ->where('seqid','=',$request->no[$key])
                                    ->update([
                                        'notrx'         => $request->nofppb,
                                        'seqid'         => $request->no[$key],
                                        'jenisbarang'   => $request->product[$key],
                                        'qty'           => $qty,
                                        'satuan'        => $request->satuan[$key],
                                        'tglpakai'      => $request->tanggalpakai[$key],
                                        'notemanfaat'   => $request->keterangan[$key]
                                    ]);
                        } else {

                            $product = DB::table('master_product')
                                    ->select('*')
                                    ->where('idqad','=',$request->jenisbarang[$key])
                                    ->first();
                            $productname = $product->nmprod;

                            DB::table('tr_fppb_detail')
                                ->where('notrx','=',$request->nofppb)
                                ->where('seqid','=',$request->no[$key])
                                ->update(
                                    ['notrx'        => $request->nofppb,
                                     'seqid'        => $request->no[$key],
                                     'jenisbarang'  => $productname,
                                     'kodeitem'     => $request->kodeitem[$key],
                                     'qty'          => $qty,
                                     'satuan'       => $request->satuan[$key],
                                     'tglpakai'     => $request->tanggalpakai[$key],
                                     'notemanfaat'  => $request->keterangan[$key]
                                 ]);
                        }
                        
                        }
                    }

                    // cek status fppb sudah pernah diajukan atau belum
                    $data = DB::table('approvalstatus')
                                 ->select('*')
                                 ->where('notrx','=',$request->nofppb)
                                 ->first();

                    if(is_null($data)) {
                        DB::table('approvalstatus')->insert([
                        'idstatus'      => Uuid::uuid4()->getHex(),
                        'notrx'         => $request->nofppb,
                        'nik'           => $username,
                        'approvaltype'  => 1,
                        'statustype'    => '',
                        'dtfrom'        => $datenow,
                        'dtthru'        => $dtthru
                        ]);
                    } else {
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
                            'approvaltype'  => 1,
                            'statustype'    => '',
                            'dtfrom'        => $datenow,
                            'dtthru'        => $dtthru
                        ]);
                    }

                    $getdatadivisi = DB::table('vw_master_division')
                                        ->select('*')
                                        ->where('div_nama','=',$request->divisi)
                                        ->where('div_id','like','djabesmen%')
                                        ->first();

                    $getdataspv = DB::table('vw_master_divhead')
                                        ->select('*')
                                        ->where('div_id_bias','=',$getdatadivisi->div_id_bias)
                                        ->first();
                    $emailspv = $getdataspv->employee_email;

                    $detail = DB::table('tr_fppb_detail')
                                 ->select('*')
                                 ->where('notrx','=',$request->nofppb)
                                 ->get();
                    // convert date dari format yyyy-mm-dd ke dd-mm-yyyy gimana ya
                    // $tglpakai = date("d-m-Y", strtotime($detail->tglpakai)); 
                     Mail::send('email.email', [
                        'divisi'    => $request->divisi, 
                        'nofppb'    => $request->nofppb,
                        'datafetch' => $detail
                    ], function ($message) use ($request, $emailspv, $detail) {
                        $message->subject('Request for approval '.$request->nofppb);
                        $message->from('info@djabesmen.net', 'Info');
                        $message->to('hannyfauzia2@gmail.com');
                    });

                     DB::commit();

                return redirect()->route('requestfppb.index')->with('alert-success','Data Berhasil Terkirim!');
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                    dd($e);
                }
            break;
        }
    }

    function fetch(Request $request)
    {
        $select = $request->get('select');
        $value = $request->get('value');
        $dependent = $request->get('dependent');

        if ($value == 'other') {
            $data = DB::table('master_uom')
                    ->select('*')
                    ->get();
            $output = '<option disable="true">Pilih Satuan</option>';
            foreach ($data as $row) {
                $output .= '<option value="' .$row->iduom.'">'.$row->iduom.' - '.$row->keterangan.'</option>';
            }
            
            echo $output;
        } else {
            $data = DB::table('master_product')
                    ->select('satuan')
                    ->where('idqad','=',$value)
                    ->first();

         $output = '<option value="' .$data->satuan.'">'.$data->satuan.'</option>';
        // $output =  '<input type="text" class="span11" name="satuan[]" value="'.$data->satuan.'" style="width: 100%" readonly> ';
        echo $output;
        }
    }

    
}
