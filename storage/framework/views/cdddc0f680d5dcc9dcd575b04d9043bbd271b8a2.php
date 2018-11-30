<?php $__env->startSection('content'); ?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var rowItem = $('#listitem');
        var i = $('#listitem tr').length + 1;
        var counter = 1;
        console.log('cek size i', i);
        //Add item
        $('#addItem').click(function() {
            row = 
            '<tr>' +
                '<td style="width: 3%"><input class="span11" type="text" name="no[]" style="width:100%;" value="'+i+'" required></td>' + 
                '<td style="width: 25%">' +
                '<select class="form-control jenisbarangc" name="jenisbarang[]" id="jenisbarang'+i+'" style="width:100%" required>' +
                 '<option value="">Pilih Product</option>'+
                '<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>' +
                    '<option value="<?php echo e($product->idqad); ?>"><?php echo e($product->nmprod); ?> - <?php echo e($product->idqad); ?></option>' +
                '<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>' +
                    '<option value="other">Other</option>' +
                '</select>' +
                '<div style="margin-top: 5px">' +
                    '<input class="span11" type="text" name="product[]" id="product'+i+'" style="display: none; width:80%;">' +
                    '<input type="text" class="span11" name="kodeitem[]" id="kodeitem'+i+'" style="display: none;">' +
                '</div>' +
                '</td>' +
                '<td style="width: 5%"><input class="span11" type="text" name="qty[]" id="qty" style="width:100%;" required></td>' +
                '<td style="width: 4%">' +
                        '<select class="form-control" name="satuan[]" id="satuan'+i+'" style="width:100%" required>' +
                            '<option value="">Pilih Satuan</option>' +
                        '</select>' +
                '</td>' +
                '<td style="width: 5%"><input class="span11" type="date" name="tanggalpakai[]" required></td>' +
                '<td style="width: 20%"><textarea class="span11" type="text" name="keterangan[]" rows="5" cols="40" required></textarea></td>' +
            '</tr>'
        rowItem.append(row);  
            counter++;
            i++;
            return false;
        });

        $(document).on('change', '.jenisbarangc', function (e) {
            /*fungsi menampilkan freetext jika item yang dipilih other*/
            var a = counter;
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
            $.ajax({
                url:"<?php echo e(route('requestfppb.fetch')); ?>",
                method:"GET",
                data:{select:select, value:value, _token: _token, dependent:dependent},
                success:function(result)
                {
                    $('#satuan' +line).html(result);
                },
                error: function(response) {
                    console.log(response.status + " " + response.statusText);
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
    <div id="breadcrumb"> <a href="<?php echo e(url('/index')); ?>" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="current">New Request</a></div>
    <h1>Create New Request FPPB</h1>
  </div>
<!--End-breadcrumbs-->

<div class="container-fluid">
    <div class="row-fluid">
        <form action="<?php echo e(route('requestfppb.store')); ?>" method="post" class="form-horizontal" name="myForm">
                <?php echo e(csrf_field()); ?>

        <div class="span12">
            <div class="control-group">
                <label class="control-label" style="text-align: left">Dari Bagian :</label>
                <div class="controls">
                <input type="text" readonly = "readonly" style="width:300px;" value="<?php echo e($divisi->div_nama); ?>"/>
                <input type="text" name="divisi" id="divisi" readonly = "readonly" style="display:none;" value="<?php echo e($divisi->div_id_bias); ?>"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">Tanggal FPPB :</label>
                <div class="controls">
                    <input type="text" name="tglfppb" id="tglfppb" readonly = "readonly"  value="<?php echo date('d/m/Y');?>"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">Kategori :</label>
                <div class="controls">
                    <select class="form-control" name="kategori" id="kategori" style="width: 30%" required>
                    <option value="">--Pilih Kategori--</option>
                    <?php $__currentLoopData = $getkategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($kategori->idkategori); ?>"><?php echo e($kategori->deskripsi); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
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
                        <tbody id="listitem">
                        <tr>
                            <td style="width: 3%">
                                <input class="span11" type="text" name="no[]" id="no" style="width:100%;" value="1">
                            </td>
                            <td style="width: 25%">
                                <select class="form-control span11 jenisbarangc" name="jenisbarang[]" id="jenisbarang1" data-dependent="satuan" style="width: 100%" required>
                                    <option value="">Pilih Product</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($product->idqad); ?>"><?php echo e($product->nmprod); ?> - <?php echo e($product->idqad); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <option value="other">Other</option>
                                </select>
                                <div style="margin-top: 5px">
                                    <input type="text" class="span11" name="product[]" id="product1" style="display: none; width:100%;">
                                    <input type="text" class="span11" name="kodeitem[]" id="kodeitem1" style="display: none;">
                                </div>
                            </td>
                            <td style="width: 5%">
                                <input class="qty span11" type="text" name="qty[]" id="qty" style="width:100%;" required>
                            </td>
                        
                           
                            <td style="width: 4%">
                                 <select class="form-control span11" name="satuan[]" id="satuan1" style="width:100%" required>
                                    <option value="">Pilih Satuan</option>
                                </select>
                            </td>
                            <td style="width: 3%">
                                <input class="span11" type="date" name="tanggalpakai[]" id="tanggalpakai" style="width: 100%" required>
                            </td>
                            <td style="width: 20%">
                                <textarea class="span11" type="text" name="keterangan[]" id="keterangan" rows="5" cols="40" required></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-actions">
                  <button type="submit" class="btn btn-md btn-primary" name="action" value="save">Save</button>
                  <button type="submit" class="btn btn-md btn-primary" name="action" value="send">Send</button>
                </div>
            </div>
        </div>
        </form>
    </div>    
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>