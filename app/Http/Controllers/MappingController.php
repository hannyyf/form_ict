<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Mail;
use Illuminate\Support\Facades\Storage;
use Orchestra\Parser\Xml\Facade as XmlParser;

class MappingController extends Controller
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
        $data = DB::table('vw_listmapping')
                             ->select('*')
                             ->where('isfullapproved','=','1')
                             ->where('nik','=',$username)
                             ->whereIn('approvaltype', [7,8])
                             ->where('dtfrom','<=',$datenow)
                             ->where('dtthru','>=',$datenow)
                             ->orderBy('notrx','desc')
                             ->get();

        return view('fppb.listmapping',['data'=>$data]);
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
        $datenow  = date('Y-m-d H:i:s');
        $dtnow    = date('Y-m-d');
        $username = Auth::user()->username;  // cek data user login
        $dtthru   = '2079-06-06 23:59:00'; // nilai maksimum untuk type smalldatetime

        DB::beginTransaction();
        try {
          foreach ($request->no as $key => $value) { // looping sebanyak data
            // disini nanti code buat mapping ke qadnya ya
            $updatekodeitem = DB::table('tr_fppb_detail')
                                ->where('notrx','=',$request->nofppb)
                                ->where('seqid','=',$request->no[$key])
                                ->update([
                                    // 'kodeitem'  => $request->kodeitem[$key],
                                    'ispr'      => 1,
                                    'dtpr'      => $datenow
                                ]);
          }

          foreach ($request->kodeitem as $key => $value) { // looping sebanyak kode item
            $getdetailitem = DB::table('master_product')
                              ->select('*')
                              ->where('idqad','=',$request->kodeitem[$key])
                              ->first();
            $satuanitem = $getdetailitem->satuan;                
            // insert ke tabel generate pr, data ini akan dipakai untuk mapping ke qad
            DB::table('generate_pr')
                ->insert([
                  'notrx'     => $request->nofppb,
                  'seqid'     => $request->linekodeitem[$key],
                  'tglpr'     => $dtnow,
                  'kodeitem'  => $request->kodeitem[$key],
                  'satuan'    => $satuanitem
                ]);
          }

          foreach ($request->budget as $key => $value) { // looping sebanyak budget
            $budget = intval(str_replace(',','',$request->budget[$key]));         
            // insert ke tabel generate pr, data ini akan dipakai untuk mapping ke qad
            DB::table('generate_pr')
                ->where('notrx','=',$request->nofppb)
                ->where('seqid','=',$request->linebudget[$key])
                ->update([
                  'budget'    => $budget
                ]);
          }
          /*Start mencari sitecode dari kategori yang sudah dipilih*/
          $header   = DB::table('tr_fppb_header')
                        ->select('*')
                        ->where('notrx','=',$request->nofppb)
                        ->first();
          $kategori   = DB::table('masterkategori')
                        ->select('*')
                        ->where('idkategori','=',$header->kategorifppb)
                        ->first();
          /*End mencari sitecode dari kategori yang sudah dipilih*/

          // update dtmodified tabel fppb header
          DB::table('tr_fppb_header')
              ->where('notrx','=',$request->nofppb)
              ->update([
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
              'approvaltype'  => 9,
              'statustype'    => '',
              'dtfrom'        => $datenow,
              'dtthru'        => $dtthru
          ]);

          $nofppb     = $request->nofppb;
          $noteict    = $request->noteict;
          $notedir    = $request->notedir;
          $sitecode   = $kategori->sitecode;
          $emailict   = $kategori->email; // email ict first layer
          $emailopr   = 'ictopr@djabesmen.co.id'; // email ict opr global

          // $url    = 'http://qaddjm2016:8080/qxilive/services/QdocWebService';
          $url    = 'http://qaddjm2016:8080/qxisim/services/QdocWebService';
          $getxml = $this->xml($nofppb,$dtnow,$noteict,$notedir,$sitecode);
          
          $send = $this->sendInBound($url,$getxml, $nofppb);
          Storage::disk('local')->put($nofppb.'.xml', $getxml); //simpan file xml ke storage
          
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
          if($send[0] == 'success') {
              $getnopr = substr($send[8],-9,-1);
              DB::table('generate_pr')
                  ->where ('notrx','=',$nofppb)
                  ->update(['prnumber' => $getnopr]);

              $detail = DB::table('vw_mapping_qad')
                        ->select('*')
                        ->where('notrx','=',$nofppb)
                        ->get();
              DB::commit();
              // fungsi kirim email notifikasi reject ke user
              Mail::send('email.email_generate_pr', [
                'nofppb'    => $nofppb,
                'datafetch' => $detail
            ], function ($message) use ($request, $emailrequester, $detail, $emailict, $emailopr) {
                $message->subject('Informasi pembuatan requisition FPPB nomor'.$request->nofppb);
                $message->from('info@djabesmen.net', 'Info');
                $message->to($emailrequester);
                // $message->cc($emailict, $emailopr);
                $message->cc($emailict, 'theblues.purple@gmail.com');
            });

              return redirect()->route('mappingict.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppb.' Berhasil di Update ');

          } elseif ($send[0] == 'error') {

              $expresp = explode('+0700', $send[8]);
              $expresp2 = explode('0MfgPro', $expresp[1]);
              DB::rollback();
                // return ['loginbound'=>$send[0],'error'=>$expresp2[0]];
              return redirect()->route('mappingict.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppb.' gagal di mapping dengan error '.$expresp2[0].' ');

          } elseif($send[0] == 'warning') {
              $getnopr = substr($send[8],-9,-1);
              DB::table('generate_pr')
                  ->where ('notrx','=',$nofppb)
                  ->update(['prnumber' => $getnopr]);

              $detail = DB::table('vw_mapping_qad')
                        ->select('*')
                        ->where('notrx','=',$nofppb)
                        ->get();

              $expresp = explode('+0700', $send[8]);
              $expresp2 = explode('0MfgPro', $expresp[1]);
              DB::commit();
              // fungsi kirim email notifikasi reject ke user
              Mail::send('email.email_generate_pr', [
                'nofppb'    => $nofppb,
                'datafetch' => $detail
            ], function ($message) use ($request, $emailrequester, $detail, $emailict, $emailopr) {
                $message->subject('Informasi pembuatan requisition FPPB nomor'.$request->nofppb);
                $message->from('info@djabesmen.net', 'Info');
                $message->to($emailrequester);
                // $message->cc($emailict, $emailopr);
                $message->cc($emailict, 'theblues.purple@gmail.com');
            });
              return redirect()->route('mappingict.index')->with('alert-success','Data FPPB dengan nomor '.$request->nofppb.' berhasil di mapping dengan warning '.$expresp2[0].' ');

          } else {
            return ['loginbound'=>'error']; 
          }
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

       $getproduct = DB::table('master_product')
                    ->select('*')
                    ->orderBy('nmprod','ASC')
                    ->get();

        return view('fppb.detailmapping',[
                                    'datafetch' => $data,
                                    'header'    => $dataheader,
                                    'datalog'   => $datalog,
                                    'products'   => $getproduct
                                    ]);
    }

    public function xml($nofppb,$datenow,$noteict,$notedir,$sitecode) {
        $xml = '
              <soapenv:Envelope xmlns="urn:schemas-qad-com:xml-services"
                xmlns:qcom="urn:schemas-qad-com:xml-services:common"
                xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
                <soapenv:Header>
                  <wsa:Action/>
                  <wsa:To>urn:services-qad-com:simulasi</wsa:To>
                  <wsa:MessageID>urn:services-qad-com::simulasi</wsa:MessageID>
                  <wsa:ReferenceParameters>
                    <qcom:suppressResponseDetail>false</qcom:suppressResponseDetail>
                  </wsa:ReferenceParameters>
                  <wsa:ReplyTo>
                    <wsa:Address>urn:services-qad-com:</wsa:Address>
                  </wsa:ReplyTo>
                </soapenv:Header>
                <soapenv:Body>
                  <maintainRequisition>
                    <qcom:dsSessionContext>
                      <qcom:ttContext>
                        <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                        <qcom:propertyName>domain</qcom:propertyName>
                        <qcom:propertyValue>DJM</qcom:propertyValue>
                      </qcom:ttContext>
                      <qcom:ttContext>
                        <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                        <qcom:propertyName>scopeTransaction</qcom:propertyName>
                        <qcom:propertyValue>false</qcom:propertyValue>
                      </qcom:ttContext>
                      <qcom:ttContext>
                        <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                        <qcom:propertyName>version</qcom:propertyName>
                        <qcom:propertyValue>eB2_2</qcom:propertyValue>
                      </qcom:ttContext>
                      <qcom:ttContext>
                        <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                        <qcom:propertyName>mnemonicsRaw</qcom:propertyName>
                        <qcom:propertyValue>false</qcom:propertyValue>
                      </qcom:ttContext>
                      <qcom:ttContext>
                        <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                        <qcom:propertyName>action</qcom:propertyName>
                        <qcom:propertyValue/>
                      </qcom:ttContext>
                      <qcom:ttContext>
                        <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                        <qcom:propertyName>entity</qcom:propertyName>
                        <qcom:propertyValue/>
                      </qcom:ttContext>
                      <qcom:ttContext>
                        <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                        <qcom:propertyName>email</qcom:propertyName>
                        <qcom:propertyValue/>
                      </qcom:ttContext>
                      <qcom:ttContext>
                        <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                        <qcom:propertyName>emailLevel</qcom:propertyName>
                        <qcom:propertyValue/>
                      </qcom:ttContext>
                    </qcom:dsSessionContext>
                    <dsRequisition>
                      <requisition>
                        <operation>A</operation>
                        <rqmShip>DJM-AC</rqmShip>
                        <rqmReqDate>'.$datenow.'</rqmReqDate>
                        <rqmNeedDate>'.$datenow.'</rqmNeedDate>
                        <rqmRqbyUserid>bta</rqmRqbyUserid>
                        <rqmEndUserid>ASA</rqmEndUserid>
                        <rqmRmks>'.$nofppb.'</rqmRmks>
                        <rqmCc>3601</rqmCc>
                        <rqmSite>djm-ac</rqmSite>
                        <rqmEntity>DJM</rqmEntity>
                        <rqmProject>NOPRJECT</rqmProject>
                        <rqmCurr>idr</rqmCurr>
                        <rqmDirect>false</rqmDirect>
                        <emailOptEntry>N</emailOptEntry>
                        <hdrCmmts>true</hdrCmmts>
                        <rqmDiscPct>0</rqmDiscPct>
                        <yn>true</yn>
                        <approveOrRoute>false</approveOrRoute>
                        <approvalComments>false</approvalComments>
                        <routeToApr>text</routeToApr>
                        <routeToBuyer>text</routeToBuyer>
                        <allInfoCorrect>true</allInfoCorrect>
                        <requisitionTransComment>
                          <operation>A</operation>
                          <cmtSeq>1</cmtSeq>
                          <cmtCmmt>'.trim($sitecode).'/'.$nofppb.'</cmtCmmt>
                          <cmtCmmt></cmtCmmt>
                          <cmtCmmt>'.$noteict.'</cmtCmmt>
                          <cmtCmmt>'.$notedir.'</cmtCmmt>
                          <prtOnQuote>false</prtOnQuote>
                          <prtOnSo>false</prtOnSo>
                          <prtOnInvoice>false</prtOnInvoice>
                          <prtOnPacklist>false</prtOnPacklist>
                          <prtOnPo>true</prtOnPo>
                          <prtOnRct>false</prtOnRct>
                          <prtOnRtv>false</prtOnRtv>
                          <prtOnShpr>false</prtOnShpr>
                          <prtOnBol>false</prtOnBol>
                          <prtOnCus>false</prtOnCus>
                          <prtOnProb>false</prtOnProb>
                          <prtOnSchedule>false</prtOnSchedule>
                          <prtOnIsrqst>false</prtOnIsrqst>
                          <prtOnDo>false</prtOnDo>
                          <prtOnIntern>false</prtOnIntern>
                        </requisitionTransComment>';

            $getdetail = DB::table('tr_fppb_detail')
                          ->join('generate_pr', function ($join) {
                            $join->on('tr_fppb_detail.notrx', '=', 'generate_pr.notrx');
                            $join->on('tr_fppb_detail.seqid','=','generate_pr.seqid');
                          })
                          ->select('tr_fppb_detail.notrx', 'tr_fppb_detail.jenisbarang', 'tr_fppb_detail.qty', 'tr_fppb_detail.tglpakai', 'tr_fppb_detail.notemanfaat', 'generate_pr.budget','generate_pr.seqid', 'generate_pr.kodeitem', 'generate_pr.satuan')
                          ->where('tr_fppb_detail.notrx','=',$nofppb)
                          ->get();
            $no = 1;
            foreach ($getdetail as $detail) {
             $xml .=    '<lineDetail>
                          <operation>A</operation>
                          <line>'.$no++.'</line>
                          <lYn>true</lYn>
                          <rqdSite>DJM-AC</rqdSite>
                          <rqdPart>'.$detail->kodeitem.'</rqdPart>
                          <rqdReqQty>'.$detail->qty.'</rqdReqQty>
                          <rqdUm>'.trim($detail->satuan).'</rqdUm>
                          <rqdPurCost>'.$detail->budget.'</rqdPurCost>
                          <rqdNeedDate>'.$detail->tglpakai.'</rqdNeedDate>
                          <rqdProject>NOPRJECT</rqdProject>
                          <desc1>'.$detail->jenisbarang.'</desc1>
                          <rqdLotRcpt>false</rqdLotRcpt>
                          <rqdUmConv>1</rqdUmConv>
                          <rqdMaxCost>'.$detail->budget.'</rqdMaxCost>
                          <lineCmmts>true</lineCmmts>
                            <lineDetailTransComment>
                              <operation>A</operation>
                              <cmtSeq>1</cmtSeq>
                              <cmtCmmt>'.trim($sitecode).'/'.$nofppb.'</cmtCmmt>
                              <cmtCmmt></cmtCmmt>
                              <cmtCmmt>'.$detail->notemanfaat.'</cmtCmmt>
                              <prtOnQuote>true</prtOnQuote>
                              <prtOnSo>false</prtOnSo>
                              <prtOnInvoice>false</prtOnInvoice>
                              <prtOnPacklist>false</prtOnPacklist>
                              <prtOnPo>true</prtOnPo>
                              <prtOnRct>false</prtOnRct>
                              <prtOnRtv>false</prtOnRtv>
                              <prtOnShpr>false</prtOnShpr>
                              <prtOnBol>false</prtOnBol>
                              <prtOnCus>false</prtOnCus>
                              <prtOnProb>false</prtOnProb>
                              <prtOnSchedule>false</prtOnSchedule>
                              <prtOnIsrqst>false</prtOnIsrqst>
                              <prtOnDo>false</prtOnDo>
                              <prtOnIntern>false</prtOnIntern>
                            </lineDetailTransComment>
                        </lineDetail>'; 
            }
      $xml .=          '</requisition>
                    </dsRequisition>
                  </maintainRequisition>
                </soapenv:Body>
              </soapenv:Envelope>';

      return $xml;
    }

    public function sendInbound($url, $xml, $nofppb) {
       try {
        $ch = curl_init();

        if (FALSE === $ch)
            throw new Exception('failed to initialize');

        curl_setopt($ch, CURLOPT_URL, $url);

        // For xml, change the content-type.
        curl_setopt($ch,CURLOPT_HTTPHEADER,array (
        'SOAPAction:""',
        'Content-Type: text/xml;charset=UTF-8',
        ));

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // ask for results to be returned

        // Send to remote and return data to caller.
        $result = curl_exec($ch);
        Storage::disk('local_two')->put($nofppb.'.xml', $result); //simpan file xml ke storage

        // file_put_contents('public/generatepr.xml', $result, FILE_APPEND);

        if (FALSE === $result)
            throw new \Exception(curl_error($ch), curl_errno($ch));

       
        $doc = new \DOMDocument();
        $doc->loadxml($result);

        $context = $doc->documentElement;
        $xpath = new \DOMXPath($doc);

        $bla = [];
        
        foreach($xpath->query('/soapenv:Envelope/soapenv:Body', $context) as $node ) {
            $bla = $node->nodeValue;
        }
        $get = explode('QAD', $bla);

        return $get;
    } catch(Exception $e) {

        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);

    }
    }
    
}
