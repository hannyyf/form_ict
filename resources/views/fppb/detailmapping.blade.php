@extends('base')
@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){     
        $('.budget').keyup(function() {
            if ($(this).val().length == 0) {
                $('#btnproses').prop('disabled',true);
            } else {
                $('#btnproses').prop('disabled', false);
            }
        });
    });

    $(document).ready(function($){
        $('.budget').mask('000,000,000,000', {reverse: true});
        $('.qty').mask('0,000,000', {reverse: true});
    });

    function tambahItem(line) {
        var rowItem = $('#tableitem' + line);
        var i = $('#tableitem'+line+ ' tr').size() + 1;
        row = 
            '<tr>' +
                '<td style="width: 90%;padding: 0px"><select class="span11" name="kodeitem[]" id="kodeitem'+line+'" required>' +
                '<option value="">--Pilih Kode Item--</option>'+
                    '@foreach($products as $product)' +
                        '<option value="{{ $product->idqad }}">{{ $product->nmprod }} - {{ $product->idqad }}</option>' +
                    '@endforeach' +
                    '</select>' +
                 '<div style="margin-top: 5px">' +
                    '<input type="text" class="span11" name="linekodeitem[]" id="product'+line+'" value="'+line+'" style="display:none; width:350px;">' +
                '</div>' +
                '</td>' +
            '</tr>'
        rowItem.append(row); 
        i++;
        return false;
    };

    function tambahBudget(line) {
         // $('.budget').mask('000,000,000,000', {reverse: true});
        var rowItem = $('#tablebudget' + line);
        var i = $('#tablebudget'+line+ ' tr').size() + 1;
        // $('#budgetline'+y).mask('0,000,000', {reverse: true});
        row = 
            '<tr>' +
                '<td style="width: 90%;padding: 0px"><input class="budget span11" type="text" name="budget[]" id="budget'+line+'" style="width: 90%" required>' +
                 '<div style="margin-top: 5px">' +
                    '<input type="text" class="span11" name="linebudget[]" id="budgetline'+line+'" value="'+line+'" style="display:none; width:350px;">' +
                '</div>' +
                '</td>' +
            '</tr>'
        rowItem.append(row); 
        i++;
        return false;
    };
</script>
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/index') }}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="current">Detail Mapping</a></div>
    <h1>Detail Mapping</h1>
  </div>
<!--End-breadcrumbs-->


<div class="container-fluid">
    <div class="row-fluid">
        <form action="{{ route('mappingict.store') }}" method="post" class="form-horizontal"> 
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
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-list" style="width: 100%">
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
                            <td style="width: 15%">
                                {{ $data->notemanfaat}}
                                <textarea class="span11" type="text" name="keterangan[]" id="keterangan" rows="5" cols="40" readonly ="readonly" style="display:none;"><?php echo $data->notemanfaat;?></textarea>
                            </td>
                            <td style="width: 15%">
                                 @if ($data->kodeitem == '')
                                    <table class="itembudget" id="tablebudget{{ $data->seqid }}">
                                        <tr>
                                            <td style="width: 90%;padding: 0px" >
                                                <input class="budget span11" type="text" name="budget[]" id="budget1" value="{{ $data->perkiraanbudget }}" style="width: 90%" required>
                                                <div style="margin-top: 5px">
                                                    <input type="text" class="span11" name="linebudget[]" id="budgetline1" value="{{ $data->seqid }}" style="display:none;width:350px;">
                                                </div>
                                            </td>
                                            <td style="width: 10%;padding: 0px">
                                                <a href="#" id="addBudget{{ $data->seqid }}" class=" btn btn-sm btn-primary addbudget" onclick="tambahBudget({{ $data->seqid }})">+</a>
                                            </td>
                                        </tr>
                                    </table>
                                    @else
                                    <input class="budget span11" type="text" name="budget[]" id="budget" value="{{ $data->perkiraanbudget }}" readonly>
                                    <input type="text" class="span11" name="linebudget[]" id="budgetline" value="{{ $data->seqid }}" style="display:none; width:350px;">
                                    @endif                   
                            </td>
                            <td style="width: 20%;">
                                 @if ($data->kodeitem == '' || $data->kodeitem == null)
                                    <table class="itemscode" id="tableitem{{ $data->seqid }}">
                                        <tr>
                                            <td style="width: 90%;padding: 0px">
                                                <select class="span11" name="kodeitem[]" id="kodeitem1" required>
                                                    <option value="">--Pilih Kode Item--</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->idqad }}">{{ $product->nmprod }} - {{ $product->idqad }}</option>
                                                @endforeach
                                                </select>
                                                <div style="margin-top: 5px">
                                                    <input type="text" class="span11" name="linekodeitem[]" id="product{{ $data->seqid }}" value="{{ $data->seqid }}" style="display:none;width:350px;">
                                                </div>
                                            </td>
                                            <td style="width: 10%;padding: 0px">
                                                <a href="#" id="addItem{{ $data->seqid }}" class=" btn btn-sm btn-primary additemcode" onclick="tambahItem({{ $data->seqid }})">+</a>
                                            </td>
                                        </tr>
                                    </table>
                                    @else
                                    <input type="text" name="kodeitem[]" id="kodeitem" value="{{ $data->kodeitem }}" readonly>
                                    <input type="text" class="span11" name="linekodeitem[]" id="product" value="{{ $data->seqid }}" style="display:none; width:350px;">
                                 @endif
                            </td>
                        </tr>
                        </tbody>
                        @endforeach
                    </table>
                </div>
                <div class="form-actions">
                  <button type="submit" class="btn btn-md btn-primary" name="action" id="btnproses" value="approve">Process</button>
                </div>
                <div class="col-md-6 pull-left" style="margin-left: : 25px">
                    <textarea class="span11" type="text" name="noteict" id="noteict" rows="5" cols="50" placeholder="Rekomendasi ICT" disabled><?php echo $header->noteict;?></textarea>
                </div>
                <div class="col-md-6 pull-left" style="margin-left: : 25px">
                    <textarea class="span11" type="text" name="notedir" id="notedir" rows="5" cols="50" placeholder="Catatan Direktur" disabled><?php echo $header->notedir;?></textarea>
                </div>

                <div class="col-md-6 pull-right" style="margin-right: 25px; margin-top: 10px" >
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