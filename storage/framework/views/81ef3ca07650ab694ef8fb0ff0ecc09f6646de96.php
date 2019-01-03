<?php $__env->startSection('content'); ?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
<script type="text/javascript">
    $(document).ready(function($){
        $('.budget').mask('000,000,000,000', {reverse: true});
        $('.qty').mask('0,000,000', {reverse: true});
    });
</script>
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php echo e(url('/index')); ?>" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="<?php echo e(url('report')); ?>" class="tip-bottom">Report Monitoring</a><a href="#" class="current">Detail</a></div>
    <h1>Detail</h1>
  </div>
<!--End-breadcrumbs-->


<div class="container-fluid">
    <div class="row-fluid">
        <form class="form-horizontal"> 
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
                            <td style="width: 25%">
                                <?php echo e($data->notemanfaat); ?>

                                <textarea class="span11" type="text" name="keterangan[]" id="keterangan" rows="5" cols="40" readonly ="readonly" style="display:none;"><?php echo $data->notemanfaat;?></textarea>
                            </td>
                            <td style="width: 10%">
                                <label class="budget"> <?php echo e($data->perkiraanbudget); ?> </label>
                                <input class="budget span11" type="text" name="budget[]" id="budget" value="<?php echo e($data->perkiraanbudget); ?>" readonly ="readonly" style="display:none;">
                            </td>
                            <td style="width: 15%">
                                <?php $__currentLoopData = $kodeitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kodeitem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($data->seqid == $kodeitem->seqid): ?>
                                <?php echo e($kodeitem->kodeitem); ?>

                                
                                <input class="span11" type="text" name="kodeitem[]" id="kodeitem" value="<?php echo e($kodeitem->kodeitem); ?>" style="display:none;" readonly="readonly">
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                        </tr>
                        </tbody>
                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </table>
                </div>
                <div class="form-actions">
                  <a href="<?php echo e(URL::previous()); ?>" class="btn btn-md btn-primary">Kembali</a>
                </div>

                <div class="col-md-4 pull-right" style="margin-right: 25px" >
                    <table border="1">
                        <tr>
                            <td>
                                <p>Log :</p>
                                <?php $__currentLoopData = $datalog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p>
                                    <?php
                                    echo "I".$log->statustype." ".$log->approvaltype." ".$log->employee_name." ".$log->dtfrom;
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