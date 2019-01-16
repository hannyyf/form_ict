@extends('base')
@section('content')
<style type="text/css">
    #loadingmessage {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: white url('/basicloader.gif') center center no-repeat;
    }
    
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
<script type="text/javascript">
    $(document).ready(function($){
        $('.budget').mask('000,000,000,000', {reverse: true});
        $('.qty').mask('0,000,000', {reverse: true});
    });
</script>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var $j = $.noConflict();
        var nopr    = {!! json_encode($datafetch[0]->prnumber)!!}
        $j('.btndetail').click(function() {
            $('#loadingmessage').show();  // show the loading message.
            var kodeitem = this.id;
            var _token = $j('input[name="_token"]').val();
            $.ajax({
                    url:"{{ route('report.detail') }}",
                    method:"GET",
                    data:{kodeitem:kodeitem, nopr:nopr},
                    success:function(result)
                    {
                        $j("#table-detail").html(result);
                        $j("#myModal").modal('show');
                        $('#loadingmessage').hide(); // hide the loading message
                    },
                    error: function(response) {
                        $('#loadingmessage').hide(); // hide the loading message
                        console.log(response.status + " " + response.statusText);
                    }
                });
        });

        $j('.btndetailpr').click(function() {
            $('#loadingmessage').show();  // show the loading message.
            var _token = $j('input[name="_token"]').val();
            $.ajax({
                    url:"{{ route('report.detailpr') }}",
                    method:"GET",
                    data:{nopr:nopr},
                    success:function(result)
                    {
                        $j("#table-detail").html(result);
                        $j("#myModal").modal('show');
                        $('#loadingmessage').hide(); // hide the loading message
                    },
                    error: function(response) {
                        $('#loadingmessage').hide(); // hide the loading message
                        console.log(response.status + " " + response.statusText);
                    }
                });
        });

    });
</script>

<!--breadcrumbs-->   
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/index') }}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="{{ url('report') }}" class="tip-bottom">Report Monitoring</a><a href="#" class="current">Detail</a></div>
    <h1>Detail</h1>
  </div>
<!--End-breadcrumbs-->
<div class="container-fluid">
    <div class="row-fluid">
        <form class="form-horizontal"> 
        <div class="span12">
            <div class="control-group">
                <label class="control-label" style="text-align: left">Dari Bagian</label>
                <div class="controls">
                    {!! $header->div_nama !!}
                    <input type="text" name="divisi" id="divisi" readonly = "readonly" style="display:none;" value="{!! $header->div_nama !!}"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">No FPPB</label>
                <div class="controls">
                    {!! $header->notrx !!}
                    <input type="text" name="nofppb" id="nofppb" value="{!! $header->notrx !!}" style="display:none;"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">Tanggal FPPB</label>
                <div class="controls">
                    {!! $header->dtfppb !!}
                    <input type="text" name="tglfppb" id="tglfppb"  value="{!! $header->dtfppb !!}" style="display:none;"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">Kategori</label>
                <div class="controls">
                    {!! $header->deskripsi !!}
                    <input type="text" name="kategori" id="kategori" readonly = "readonly" style="display:none;" value="{!! $header->deskripsi !!}"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">No PR</label>
                <div class="controls">
                    {{ $datafetch[0]->prnumber }} 
                    <u><a class="btndetailpr" href="#" id="{{ $datafetch[0]->prnumber }}" style="color: #0023cc">Detail PR</a></u>

                </div>
            </div>

            <div class="widget-box">
                <div class="widget-content nopadding">
                    <table class="table table-bordered table-striped with-check" id="table-list" style="width: 100%">
                        <thead>
                         <tr>
                            <th>No.</th>
                            <th>Jenis Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Tanggal Pakai</th>
                            <th>Keterangan / Jenis Manfaat</th>
                            <th>Perkiraan Budget</th>
                            <th>Kode Item</th>
                         </tr>
                        </thead>
                        @foreach($datafetch as $data)
                        <tbody id="listitem">
                        <tr>
                            <td style="width: 2%">
                                {{ $data->seqid }}
                                
                                
                            </td >
                            <td style="width: 20%">
                                {{ $data->jenisbarang }}
                               
                                
                            </td>
                            <td style="width: 4%">
                                {{ $data->qty }}
                               
                                
                            </td>
                            <td style="width: 3%">
                                {{ $data->satuan }}
                               
                            </td>
                            <td style="width:8%">
                                {{ $data->tglpakai }}
                               
                            </td>
                            <td style="width: 25%">
                                {{ $data->notemanfaat}}
                               
                            </td>
                            <td style="width: 10%">
                                <label class="budget">{{ $data->perkiraanbudget }}</label>
                                
                            </td>
                            <td style="width: 15%">
                                @foreach ($kodeitems as $kodeitem)
                                @if ($data->seqid == $kodeitem->seqid)
                                <u><b><a class="btndetail" href="#" id="{{ $kodeitem->kodeitem }}" style="color: #0023cc">{{ $kodeitem->kodeitem }}</a></b></u><br>
                                @endif
                                @endforeach
                            </td>
                        </tr>
                        </tbody>
                         @endforeach
                    </table>
                </div>
                <div class="form-actions">
                  <a href="{{ URL::previous() }}" class="btn btn-md btn-primary">Kembali</a>
                </div>

                <div class="col-md-4 pull-right" style="margin-right: 25px" >
                    <table border="1">
                        <tr>
                            <td>
                                <p>Log :</p>
                                @foreach($datalog as $log)
                                <p>
                                <?php
                                    echo "I".$log->statustype." ".$log->approvaltype." ".$log->dtfrom." ".$log->employee_name;
                                    ?>
                                </p>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div> 
            </div>
        </div>
        </form>
    </div>
    
</div>
<div id='loadingmessage' style='display:none'>
   <!--  <img src='/animation.gif' class="loading-image"/> -->
</div> 
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog-md">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Detail Kode Item</h4>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
             <table id="table-data" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th style="text-align: center">No</th>
                    <th style="text-align: center">Kode Item</th>
                    <th style="text-align: center">No PR</th>
                    <th style="text-align: center">No PO</th>
                    <th style="text-align: center">Tgl PO</th>
                    <th style="text-align: center">No PO Receipt</th>
                    <th style="text-align: center">Tgl PO Receipt</th>
                </tr>
                </thead>
                <tbody id="table-detail">
                </tbody>
                
            </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
</div>
<!-- modal end -->
@endsection