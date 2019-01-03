<?php $__env->startSection('content'); ?>
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
                '<td style="width: 3%"><input class="span11" type="text" name="no[]" style="width:100%;" value="'+i+'"></td>' + 
                '<td style="width: 25%">' +
                '<select class="form-control jenisbarangc" name="jenisbarang[]" id="jenisbarang'+i+'" style="width: 100%">' +
                '<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>' +
                    '<option value="<?php echo e($product->idqad); ?>"><?php echo e($product->nmprod); ?> - <?php echo e($product->idqad); ?></option>' +
                '<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>' +
                    '<option value="other">Other</option>' +
                '</select>' +
                '<div style="margin-top: 5px">' +
                    '<input class="span11" type="text" name="product[]" id="product'+i+'" style="display: none; width:350px;">' +
                     '<input type="text" class="span11" name="kodeitem[]" id="kodeitem'+i+'" style="display: none;">' +
                '</div>' +
                '</td>' +
                '<td style="width: 5%"><input class="span11" type="text" name="qty[]" style="width:80%;"></td>' +
                '<td style="width: 4%">' +
                        '<select class="form-control" name="satuan[]" id="satuan'+i+'" style="width: 100%">' +
                            '<option value="">Pilih Satuan</option>' +
                        '</select>' +
                '</td>' +
                '<td style="width: 5%"><input class="span11" type="date" name="tanggalpakai[]"></td>' +
                '<td style="width: 20%"><textarea class="span11" type="text" name="keterangan[]" rows="5" cols="40"></textarea></td>' +
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
                url:"<?php echo e(route('requestfppb.fetch')); ?>",
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
    <div id="breadcrumb"> <a href="<?php echo e(url('/index')); ?>" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="tip-bottom">List Request</a><a href="#" class="current">Edit</a></div>
    <h1>Edit Request</h1>
  </div>
<!--End-breadcrumbs-->


<div class="container-fluid">
    <div class="row-fluid">
        <form action="<?php echo e(url('/update')); ?>" method="post" class="form-horizontal"> 
                <?php echo e(csrf_field()); ?>

        <div class="span12">
            <div class="control-group">
                <label class="control-label" style="text-align: left">Dari Bagian :</label>
                <div class="controls">
                    <input type="text" name="divisi" id="divisi" readonly = "readonly" style="width:300px;" value="<?php echo $header->div_nama; ?>"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">No FPPB :</label>
                <div class="controls">
                    <input type="text" name="nofppb" id="nofppb" readonly = "readonly" value="<?php echo $header->notrx; ?>"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">Tanggal FPPB :</label>
                <div class="controls">
                    <input type="text" name="tglfppb" id="tglfppb" readonly = "readonly"  value="<?php echo $header->dtfppb; ?>"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">Kategori :</label>
                <div class="controls">
                    <input type="text" name="kategori" id="kategori" readonly = "readonly" style="width:300px;" value="<?php echo $header->deskripsi; ?>"/>
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
                <?php $__currentLoopData = $datafetch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <tbody id="listitem">
                    <tr>
                        <td style="width: 3%">
                            <input class="span11" type="text" name="no[]" id="no" style="width:100%;" value="<?php echo e($data->seqid); ?>">
                        </td >
                        <td style="width: 25%">
                             <select class="form-control jenisbarangc" name="jenisbarang[]" id="jenisbarang<?php echo e($data->seqid); ?>" style="width: 100%">
                                <option value="<?php echo e($data->kodeitem); ?>"><?php echo e($data->jenisbarang); ?></option>
                                <option disabled="true">Choose One</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($product->idqad); ?>"><?php echo e($product->nmprod); ?> - <?php echo e($product->idqad); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <option value="other">Other</option>
                            </select>
                            <div style="margin-top: 5px">
                                <input type="text" name="product[]" id="product<?php echo e($data->seqid); ?>" style="display: none; width:100%" value="<?php echo e($data->jenisbarang); ?>">
                                <input type="text" class="span11" name="kodeitem[]" id="kodeitem<?php echo e($data->seqid); ?>" style="display: none;">
                            </div>
                        </td>
                        <td style="width: 5%">
                            <input class="span11" type="text" name="qty[]" id="qty" style="width:80%;" value="<?php echo e($data->qty); ?>">
                        </td>
                        <td style="width: 4%">
                             <select class="form-control " name="satuan[]" id="satuan<?php echo e($data->seqid); ?>" style="width: 100%">
                                <option value="<?php echo e($data->satuan); ?>"><?php echo e($data->satuan); ?></option>
                            </select>
                        </td>
                        <td style="width: 3%">
                            <input class="span11" type="date" name="tanggalpakai[]" id="tanggalpakai" value="<?php echo e($data->tglpakai); ?>">
                        </td>
                        <td style="width: 20%">
                            <textarea class="span11" type="text" name="keterangan[]" id="keterangan" rows="5" cols="40"><?php echo $data->notemanfaat;?></textarea>
                        </td>
                    </tr>
                </tbody>
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>