@extends('base')
@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
<script type="text/javascript">
    $(document).ready(function($){
        $('.budget').mask('000,000,000,000', {reverse: true});
        $('.qty').mask('0,000,000', {reverse: true});
    });

    function change_checkbox(el){
        var i;
        if(el.checked){
            $('#btnRequestClose').prop('disabled',false);
        }else{
            $('#btnRequestClose').prop('disabled',true);
        }
    }
</script>
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/index') }}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="tip-bottom">List Request Close</a><a href="#" class="current">Detail</a></div>
    <h1>Detail</h1>
  </div>
<!--End-breadcrumbs-->


<div class="container-fluid">
    <div class="row-fluid">
        <form action="{{ route('serahterima.store') }}" method="post" class="form-horizontal"> 
                {{ csrf_field() }}
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


            <div class="widget-box">
                <div class="widget-content nopadding">
                    <table class="table table-bordered table-striped with-check" id="table-list" style="width: 100%">
                        <thead>
                         <tr>
                            <th></th>
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
                            <td style="width: 3%">
                                <input type="checkbox" value="{{$data->seqid}}" name="selected[]" class="selected" id="selected{{$data->seqid}}" onchange="change_checkbox(this)">
                            </td>
                            <td style="width: 2%">
                                {{ $data->seqid }}
                                <input class="span11" type="text" name="no[]" id="no" style="display:none;" value="{{ $data->seqid }}">
                                
                            </td >
                            <td style="width: 20%">
                                {{ $data->jenisbarang }}
                                <input class="span11" type="text" name="jenisbarang[]" id="jenisbarang" readonly = "readonly" style="display:none;" value="{{ $data->jenisbarang }}">
                                
                            </td>
                            <td style="width: 4%">
                                {{ $data->qty }}
                                <input class="span11" type="text" name="qty[]" id="qty" readonly = "readonly" style="display:none;" value="{{ $data->qty }}">
                                
                            </td>
                            <td style="width: 3%">
                                {{ $data->satuan }}
                                <input class="span11" type="text" name="satuan[]" id="satuan" readonly = "readonly" style="display:none;" value="{{ $data->satuan }}">
                            </td>
                            <td style="width:8%">
                                {{ $data->tglpakai }}
                                <input class="span11" type="text" name="tanggalpakai[]" id="tanggalpakai" value="{{ $data->tglpakai }}" style="display:none;">
                            </td>
                            <td style="width: 25%">
                                {{ $data->notemanfaat}}
                                <textarea class="span11" type="text" name="keterangan[]" id="keterangan" rows="5" cols="40" readonly ="readonly" style="display:none;"><?php echo $data->notemanfaat;?></textarea>
                            </td>
                            <td style="width: 10%">
                                <label class="budget">{{ $data->perkiraanbudget }}</label>
                                <input class="budget span11" type="text" name="budget[]" id="budget" style="display:none;" value="{{ $data->perkiraanbudget }}" >
                            </td>
                            <td style="width: 15%">
                                @foreach ($kodeitems as $kodeitem)
                                @if ($data->seqid == $kodeitem->seqid)
                                {{ $kodeitem->kodeitem }} <br>
                                
                                <input class="span11" type="text" name="kodeitem[]" id="kodeitem" value="{{ $kodeitem->kodeitem }}" style="display:none;" readonly="readonly">
                                @endif
                                @endforeach
                            </td>
                        </tr>
                        </tbody>
                         @endforeach
                    </table>
                </div>
                <div class="form-actions">
                  <button type="submit" class="btn btn-md btn-primary request" name="action" value="approve" id="btnRequestClose" disabled>Request Close</button>
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
@endsection