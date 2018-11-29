<?php $__env->startSection('content'); ?>
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
        console.log('cek saat ketik budget');
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
                    '<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>' +
                        '<option value="<?php echo e($product->idqad); ?>"><?php echo e($product->nmprod); ?> - <?php echo e($product->idqad); ?></option>' +
                    '<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>' +
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
        var rowItem = $('#tablebudget' + line);
        var i = $('#tablebudget'+line+ ' tr').size() + 1;
        var y = $('#tablebudget'+line).size();
        // $('#budgetline'+y).mask('0,000,000', {reverse: true});
        row = 
            '<tr>' +
                '<td style="width: 90%;padding: 0px"><input class="budget span11" type="text" name="budget[]" id="budget'+line+'" style="width: 90%" required>' +
                 '<div style="margin-top: 5px">' +
                    '<input type="text" class="span11" name="linebudget[]" id="budgetline'+y+'" value="'+line+'" style="display:none; width:350px;">' +
                '</div>' +
                '</td>' +
            '</tr>'
        rowItem.append(row); 
        i++;
        y++;
        return false;
    };
</script>
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php echo e(url('/index')); ?>" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="current">Detail Mapping</a></div>
    <h1>Detail Mapping</h1>
  </div>
<!--End-breadcrumbs-->


<div class="container-fluid">
    <div class="row-fluid">
        <form action="<?php echo e(route('mappingict.store')); ?>" method="post" class="form-horizontal"> 
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
                        <?php $__currentLoopData = $datafetch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tbody id="listitem">
                        <tr>
                            <td style="width: 2%">
                                <?php echo e($data->seqid); ?>

                                <input class="span11" type="text" name="no[]" id="no" style="display:none;" value="<?php echo e($data->seqid); ?>">
                                
                            </td >
                            <td style="width: 20%">
                                <?php echo e($data->jenisbarang); ?>

                                <input class="span11" type="text" name="jenisbarang[]" id="jenisbarang" readonly = "readonly" style="display:none;" value="<?php echo e($data->jenisbarang); ?>">
                                
                            </td>
                            <td style="width: 4%">
                                <?php echo e($data->qty); ?>

                                <input class="span11" type="text" name="qty[]" id="qty" readonly = "readonly" style="display:none;" value="<?php echo e($data->qty); ?>">
                                
                            </td>
                            <td style="width: 3%">
                                <?php echo e($data->satuan); ?>

                                <input class="span11" type="text" name="satuan[]" id="satuan" readonly = "readonly" style="display:none;" value="<?php echo e($data->satuan); ?>">
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
                                 <?php if($data->kodeitem == ''): ?>
                                    <table class="itembudget" id="tablebudget<?php echo e($data->seqid); ?>">
                                        <tr>
                                            <td style="width: 90%;padding: 0px" >
                                                <input class="budget span11" type="text" name="budget[]" id="budget1" value="<?php echo e($data->perkiraanbudget); ?>" style="width: 90%" required>
                                                <div style="margin-top: 5px">
                                                    <input type="text" class="span11" name="linebudget[]" id="budgetline1" value="<?php echo e($data->seqid); ?>" style="display:none;width:350px;">
                                                </div>
                                            </td>
                                            <td style="width: 10%;padding: 0px">
                                                <a href="#" id="addBudget<?php echo e($data->seqid); ?>" class=" btn btn-sm btn-primary addbudget" onclick="tambahBudget(<?php echo e($data->seqid); ?>)">+</a>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php else: ?>
                                    <input class="budget span11" type="text" name="budget[]" id="budget" value="<?php echo e($data->perkiraanbudget); ?>" readonly>
                                    <input type="text" class="span11" name="linebudget[]" id="budgetline" value="<?php echo e($data->seqid); ?>" style="display:none; width:350px;">
                                    <?php endif; ?>                   
                            </td>
                            <td style="width: 20%;">
                                 <?php if($data->kodeitem == '' || $data->kodeitem == null): ?>
                                    <table class="itemscode" id="tableitem<?php echo e($data->seqid); ?>">
                                        <tr>
                                            <td style="width: 90%;padding: 0px">
                                                <select class="span11" name="kodeitem[]" id="kodeitem1" required>
                                                    <option value="">--Pilih Kode Item--</option>
                                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($product->idqad); ?>"><?php echo e($product->nmprod); ?> - <?php echo e($product->idqad); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <div style="margin-top: 5px">
                                                    <input type="text" class="span11" name="linekodeitem[]" id="product<?php echo e($data->seqid); ?>" value="<?php echo e($data->seqid); ?>" style="display:none;width:350px;">
                                                </div>
                                            </td>
                                            <td style="width: 10%;padding: 0px">
                                                <a href="#" id="addItem<?php echo e($data->seqid); ?>" class=" btn btn-sm btn-primary additemcode" onclick="tambahItem(<?php echo e($data->seqid); ?>)">+</a>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php else: ?>
                                    <input type="text" name="kodeitem[]" id="kodeitem" value="<?php echo e($data->kodeitem); ?>" readonly>
                                    <input type="text" class="span11" name="linekodeitem[]" id="product" value="<?php echo e($data->seqid); ?>" style="display:none; width:350px;">
                                 <?php endif; ?>
                            </td>
                        </tr>
                        </tbody>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            </div>
        </div>
        </form>
    </div>    
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>