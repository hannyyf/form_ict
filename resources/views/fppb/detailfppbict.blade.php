@extends('base')
@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
<script type="text/javascript">
    $(document).ready(function($){
        $('.budget').mask('000,000,000,000', {reverse: true});
        $('.qty').mask('0,000,000', {reverse: true});
    });

    $(document).ready(function(){   
        $('#noteict').keyup(function() {
       if ($(this).val().length == 0) {
            $('#btnsubmit').prop('disabled',true);
        } else {
            $('#btnsubmit').prop('disabled', false);
        } 
        });
    });

    $(document).on('change', '.selectpicker', function (e) {
    var id = this.id;
    var split = id.split('jenisbarang');
    var line = split[1];
    var b = $('#jenisbarang' + line).val();
    if (b == 'other') {
        $("#product"+line+"").show();
        $("#kodeitem"+line+"").val('');
        
    } else {
        $("#product"+line+"").hide();
        $("#kodeitem"+line+"").val(b);
    };

    /*fungsi menyesuaikan uom dengan jenis yang dipilih*/
    var select = $('#jenisbarang' + line).attr('id');
    var value = $('#jenisbarang' + line).val();
    var dependent = $('#jenisbarang' + line).data('dependent');
    var _token = $('input[name="_token"]').val();
    console.log('cek select', select);
    $.ajax({
        url:"{{ route('requestfppb.fetch') }}",
        method:"POST",
        data:{select:select, value:value, _token: _token, dependent:dependent},
        success:function(result)
        {
            $('#satuan' +line).html(result);
        }
    });
});
</script>
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/index') }}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="tip-bottom">List Review ICT</a><a href="#" class="current">Detail</a></div>
    <h1>Detail</h1>
  </div>
<!--End-breadcrumbs-->

<div class="container-fluid">
    <div class="row-fluid">
        <form action="{{ route('reviewict.store') }}" method="post" class="form-horizontal"> 
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
                    <table class="table table-bordered table-striped table-responsive" id="table-list" style="width: 100%">
                        <thead>
                         <tr>
                            <th>No.</th>
                            <th>Jenis Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Tanggal Pakai</th>
                            <th>Keterangan / Jenis Manfaat</th>
                            <th>Perkiraan Budget</th>
                         </tr>
                        </thead>
                        @foreach($datafetch as $data)
                        <tbody id="listitem">
                        <tr>
                            <td style="width: 2%">
                                {{ $data->seqid }}
                                <input class="span11" type="text" name="no[]" id="no" style="display:none;" value="{{ $data->seqid }}">
                                
                            </td >
                            <td style="width: 20%">
                                <!-- {{ $data->jenisbarang }}
                                <input class="span11" type="text" name="jenisbarang[]" id="jenisbarang" readonly = "readonly" style="display:none;" value="{{ $data->jenisbarang }}"> -->
                            <select class="form-control selectpicker" name="jenisbarang[]" id="jenisbarang{{ $data->seqid }}" style="width: 100%" required>
                                <option value="{{ $data->jenisbarang }}">{{ $data->jenisbarang }}</option>
                                <option disabled="true">Choose One</option>
                                @foreach($products as $product)
                                <option value="{{ $product->idqad }}">{{ $product->nmprod }}</option>
                                @endforeach
                                <option value="other">Other</option>
                            </select>
                            <div style="margin-top: 5px">
                                <input type="text" name="product[]" id="product{{ $data->seqid }}" style="display: none; width:95%" value="{{ $data->jenisbarang }}">
                                <input type="text" class="span11" name="kodeitem[]" id="kodeitem{{ $data->seqid }}" style="display: none;">
                            </div>
                                
                            </td>
                            <td style="width: 3%">
                                {{ $data->qty }}
                                <input class="span11" type="text" name="qty[]" id="qty" readonly = "readonly" style="display:none;" value="{{ $data->qty }}">
                                
                            </td>
                            <td style="width: 4%">
                               <!--  {{ $data->satuan }}
                                <input class="span11" type="text" name="satuan[]" id="satuan" readonly = "readonly" style="display:none;" value="{{ $data->satuan }}"> -->
                            <select class="form-control " name="satuan[]" id="satuan{{ $data->seqid }}" style="width: 100%"  data-show-subtext="true" data-live-search="true" required>
                                <option value="{{ $data->satuan }}">{{ $data->satuan }}</option>
                                <option disabled="true">Pilih Satuan</option>
                                @foreach($getuom as $uom)
                                <option value="{{ $uom->iduom }}">{{ $uom->iduom }} - {{ $uom->keterangan }}</option>
                                @endforeach
                            </select>
                            </td>
                            <td style="width:8%">
                                {{ $data->tglpakai }}
                                <input class="span11" type="text" name="tanggalpakai[]" id="tanggalpakai" value="{{ $data->tglpakai }}" style="display:none;">
                            </td>
                            <td style="width: 15%">
                                {{ $data->notemanfaat}}
                                <textarea class="span11" type="text" name="keterangan[]" id="keterangan" rows="5" cols="40" readonly ="readonly" style="display:none;"><?php echo $data->notemanfaat;?></textarea>
                            </td>
                            <td style="width: 15%">
                                <input class="budget span11" type="text" name="budget[]" id="budget" style="width:100%;" required>
                            </td>

                        </tr>
                        </tbody>
                         @endforeach
                    </table>
                </div>
                <div class="form-actions">
                  <button type="submit" class="btn btn-sm btn-primary" name="action" id="btnsubmit" value="approve" disabled>Submit Review</button>
                </div>
            </div>
                <div class="col-md-4 pull-right" style="margin-right: 25px; margin-bottom: 10px" >
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

                <div class="col-md-6 pull-left" style="margin-left: 25px">
                    <textarea class="span11" type="text" name="noteict" id="noteict" rows="5" cols="40" placeholder="Rekomendasi ICT"></textarea>
                </div>
                <div class="col-md-6 pull-left" style="margin-left: 25px">
                    <textarea class="span11" type="text" name="notedir" id="notedir" rows="5" cols="40" placeholder="Catatan Direktur" disabled><?php echo $header->notedir;?></textarea>
                </div>
                </div>
                 
            </div>
        </form>
    </div>
</div>
@endsection