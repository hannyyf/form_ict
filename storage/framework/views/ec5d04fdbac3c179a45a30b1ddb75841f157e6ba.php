<?php $__env->startSection('content'); ?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
<script type="text/javascript">
    var $j = $.noConflict(true);
    $j(document).ready(function($){
        $j('.budget').mask('000,000,000,000', {reverse: true});
        $j('.qty').mask('0,000,000', {reverse: true});
    });

    $j(document).ready(function(){   
        $j('#noteict').keyup(function() {
       if ($j(this).val().length == 0) {
            $j('#btnsubmit').prop('disabled',true);
        } else {
            $j('#btnsubmit').prop('disabled', false);
        } 
        });
    });

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<script type="text/javascript">
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
        url:"<?php echo e(route('requestfppb.fetch')); ?>",
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
    <div id="breadcrumb"> <a href="<?php echo e(url('/index')); ?>" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="tip-bottom">List Review ICT</a><a href="#" class="current">Detail</a></div>
    <h1>Detail</h1>
  </div>
<!--End-breadcrumbs-->

<div class="container-fluid">
    <div class="row-fluid">
        <form action="<?php echo e(route('reviewict.store')); ?>" method="post" class="form-horizontal"> 
                <?php echo e(csrf_field()); ?>

        <div class="span12">
        <div class="control-group">
                <label class="control-label" style="text-align: left">Dari Bagian</label>
                <div class="controls">
                    <?php echo $header->div_nama; ?>

                    <input type="text" name="divisi" id="divisi" readonly = "readonly" style="display:none;" value="<?php echo $header->div_nama; ?>"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">No FPPB</label>
                <div class="controls">
                    <?php echo $header->notrx; ?>

                    <input type="text" name="nofppb" id="nofppb" value="<?php echo $header->notrx; ?>" style="display:none;"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">Tanggal FPPB</label>
                <div class="controls">
                    <?php echo $header->dtfppb; ?>

                    <input type="text" name="tglfppb" id="tglfppb"  value="<?php echo $header->dtfppb; ?>" style="display:none;"/>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" style="text-align: left">Kategori</label>
                <div class="controls">
                    <?php echo $header->deskripsi; ?>

                    <input type="text" name="kategori" id="kategori" readonly = "readonly" style="display:none;" value="<?php echo $header->deskripsi; ?>"/>
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
                        <?php $__currentLoopData = $datafetch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tbody id="listitem">
                        <tr>
                            <td style="width: 2%">
                                <?php echo e($data->seqid); ?>

                                <input class="span11" type="text" name="no[]" id="no" style="display:none;" value="<?php echo e($data->seqid); ?>">
                                
                            </td >
                            <td style="width: 20%">
                                <!-- <?php echo e($data->jenisbarang); ?>

                                <input class="span11" type="text" name="jenisbarang[]" id="jenisbarang" readonly = "readonly" style="display:none;" value="<?php echo e($data->jenisbarang); ?>"> -->
                            <select class="form-control selectpicker" name="jenisbarang[]" id="jenisbarang<?php echo e($data->seqid); ?>" style="width: 100%" data-show-subtext="true" data-live-search="true" required>
                                <option value="<?php echo e($data->jenisbarang); ?>"><?php echo e($data->jenisbarang); ?></option>
                                <option disabled="true">Choose One</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($product->idqad); ?>"><?php echo e($product->nmprod); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <option value="other">Other</option>
                            </select>
                            <div style="margin-top: 5px">
                                <input type="text" name="product[]" id="product<?php echo e($data->seqid); ?>" style="display: none; width:95%" value="<?php echo e($data->jenisbarang); ?>">
                                <input type="text" class="span11" name="kodeitem[]" id="kodeitem<?php echo e($data->seqid); ?>" style="display: none;">
                            </div>
                                
                            </td>
                            <td style="width: 3%">
                                <?php echo e($data->qty); ?>

                                <input class="span11" type="text" name="qty[]" id="qty" readonly = "readonly" style="display:none;" value="<?php echo e($data->qty); ?>">
                                
                            </td>
                            <td style="width: 4%">
                               <!--  <?php echo e($data->satuan); ?>

                                <input class="span11" type="text" name="satuan[]" id="satuan" readonly = "readonly" style="display:none;" value="<?php echo e($data->satuan); ?>"> -->
                            <select class="form-control " name="satuan[]" id="satuan<?php echo e($data->seqid); ?>" style="width: 100%"  data-show-subtext="true" data-live-search="true" required>
                                <option value="<?php echo e($data->satuan); ?>"><?php echo e($data->satuan); ?></option>
                                <option disabled="true">Pilih Satuan</option>
                                <?php $__currentLoopData = $getuom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($uom->iduom); ?>"><?php echo e($uom->iduom); ?> - <?php echo e($uom->keterangan); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            </td>
                            <td style="width:8%">
                                <?php echo e($data->tglpakai); ?>

                                <input class="span11" type="text" name="tanggalpakai[]" id="tanggalpakai" value="<?php echo e($data->tglpakai); ?>" style="display:none;">
                            </td>
                            <td style="width: 15%">
                                <?php echo e($data->notemanfaat); ?>

                                <textarea class="span11" type="text" name="keterangan[]" id="keterangan" rows="5" cols="40" readonly ="readonly" style="display:none;"><?php echo $data->notemanfaat;?></textarea>
                            </td>
                            <td style="width: 15%">
                                <input class="budget span11" type="text" name="budget[]" id="budget" style="width:100%;" required>
                            </td>

                        </tr>
                        </tbody>
                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </table>
                </div>

                <div class="control-group">
                    <?php if($header->lampiran == null): ?>
                    <div class="controls pull-left" style="margin: 10px" >
                        <label for="file">Lampiran</label>
                        <input type="file" name="filename" id="file" value="">
                    </div>
                    <?php else: ?>
                    <div class="controls pull-left" style="margin: 10px" >
                        <label for="file">Lampiran</label>
                        <a href="/uploads/<?php echo e($header->lampiran); ?>"> <?php echo e($header->lampiran); ?> </a>
                    </div>
                    <?php endif; ?>
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
                                <?php $__currentLoopData = $datalog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p>
                                    <?php
                                    echo "I".$log->statustype." ".$log->approvaltype." ".$log->dtfrom." ".$log->employee_name;
                                    ?>
                                </p>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>