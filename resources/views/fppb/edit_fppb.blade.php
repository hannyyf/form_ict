@extends('base')
@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var rowItem = $('#table-list').find('tbody:last');
        var i = $('#listitem tr').length + 1;
        var counter = $('#listitem tr').length;
        //Add item
        $('#addItem').click(function() {
            row = 
            '<tr>' +
                '<td style="width: 3%"><input class="span11" type="text" name="no[]" style="width:100%;" value="'+i+'" required></td>' + 
                '<td style="width: 25%">' +
                '<input class="span11" type="text" name="jenisbarang[]" id="jenisbarang" style="width:100%;"  required>' +
                // '<select class="form-control jenisbarangc" name="jenisbarang[]" id="jenisbarang'+i+'" style="width: 100%" required>' +
                //     '<option value="">Pilih Product</option>' +
                // '@foreach($products as $product)' +
                //     '<option value="{{ $product->idqad }}">{{ $product->nmprod }} - {{ $product->idqad }}</option>' +
                // '@endforeach' +
                //     '<option value="other">Other</option>' +
                // '</select>' +
                '<div style="margin-top: 5px">' +
                    '<input class="span11" type="text" name="product[]" id="product'+i+'" style="display: none; width:350px;">' +
                     '<input type="text" class="span11" name="kodeitem[]" id="kodeitem'+i+'" style="display: none;">' +
                '</div>' +
                '</td>' +
                '<td style="width: 5%"><input class="span11" type="text" name="qty[]" style="width:80%;" required></td>' +
                '<td style="width: 4%">' +
                        // '<select class="form-control" name="satuan[]" id="satuan'+i+'" style="width: 100%" required>' +
                        //     '<option value="">Pilih Satuan</option>' +
                        // '</select>' +
                        '<select class="form-control span11" name="satuan[]" id="satuan1" style="width:100%" required>' +
                        '<option value="">Pilih Satuan</option>'+
                        '@foreach($getuom as $uom)'+
                        '<option value="{{ $uom->iduom }}">{{ $uom->iduom }} - {{ $uom->keterangan }}</option>'+
                        '@endforeach'+
                    '</select>'+      
                '</td>' +
                '<td style="width: 5%"><input class="span11" type="date" name="tanggalpakai[]" required></td>' +
                '<td style="width: 20%"><textarea class="span11" type="text" name="keterangan[]" rows="5" cols="40" required></textarea></td>' +
            '</tr>'
        rowItem.append(row);  
            i++;
            counter++
            return false;
        });

        $(document).on('change', '.jenisbarangc', function (e) {
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
    });
    $(document).ready(function($){
        $('.qty').mask('0,000,000', {reverse: true});
    });
    
</script>
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/index') }}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="tip-bottom">List Request</a><a href="#" class="current">Edit</a></div>
    <h1>Edit Request</h1>
  </div>
<!--End-breadcrumbs-->


<div class="container-fluid">
    <div class="row-fluid">
        <form action="{{ url('/update') }}" method="post" class="form-horizontal"> 
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


            <div class="control-group">
                <div class="controls pull-right" style="margin: 10px" >
                    <a href="#" id="addItem" class=" btn btn-sm btn-primary">Add Item</a>
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
                     </tr>
                    </thead>
                @foreach($datafetch as $data)

                <tbody id="listitem">
                    <tr>
                        <td style="width: 3%">
                            <input class="span11" type="text" name="no[]" id="no" style="width:100%;" value="{{ $data->seqid }}" required>
                        </td >
                        <td style="width: 25%">
                            <input class="span11" type="text" name="jenisbarang[]" id="jenisbarang" style="width:100%;" value = "{{ $data->jenisbarang }}" required>
                            <!--  <select class="form-control jenisbarangc" name="jenisbarang[]" id="jenisbarang{{ $data->seqid }}" style="width: 100%" required>
                                <option value="{{ $data->kodeitem }}">C</option>
                                <option disabled="true">Choose One</option>
                                @foreach($products as $product)
                                <option value="{{ $product->idqad }}">{{ $product->nmprod }} - {{ $product->idqad }}</option>
                                @endforeach
                                <option value="other">Other</option>
                            </select>
                            <div style="margin-top: 5px">
                                <input type="text" name="product[]" id="product{{ $data->seqid }}" style="display: none; width:100%" value="{{ $data->jenisbarang }}">
                                <input type="text" class="span11" name="kodeitem[]" id="kodeitem{{ $data->seqid }}" style="display: none;">
                            </div> -->
                        </td>
                        <td style="width: 5%">
                            <input class="span11" type="text" name="qty[]" id="qty" style="width:80%;" value="{{ $data->qty }}" required>
                        </td>
                        <td style="width: 4%">
                             <select class="form-control " name="satuan[]" id="satuan{{ $data->seqid }}" style="width: 100%" required>
                                <option value="{{ $data->satuan }}">{{ $data->satuan }}</option>
                                <option disabled="true">Pilih Satuan</option>
                                @foreach($getuom as $uom)
                                <option value="{{ $uom->iduom }}">{{ $uom->iduom }} - {{ $uom->keterangan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 3%">
                            <input class="span11" type="date" name="tanggalpakai[]" id="tanggalpakai" value="{{ $data->tglpakai }}" required>
                        </td>
                        <td style="width: 20%">
                            <textarea class="span11" type="text" name="keterangan[]" id="keterangan" rows="5" cols="40" required><?php echo $data->notemanfaat;?></textarea>
                        </td>
                    </tr>
                </tbody>
                 @endforeach
                </table>
            </div>
                <div class="form-actions">
                  <button type="submit" class="btn btn-md btn-primary" name="action" value="update">Update</button>
                  <button type="submit" class="btn btn-md btn-primary" name="action" value="send">Send</button>
                </div>
        </div>
        </div>
        </form>
    </div>
</div> 
@endsection