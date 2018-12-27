<?php $__env->startSection('content'); ?>
<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>

<script type="text/javascript">
    function filter() {
        var nofppb = $('#nofppb').val();
        console.log('nofppb', nofppb);
        $.ajax({
            url:"<?php echo e(route('report.filter')); ?>",
            method:"GET",
            data:{nofppb:nofppb},
            success:function(result)
            {
                console.log('resulttttttttttttt',result);
            }
        });
    }

    $(document).ready(function(){
        var data = <?php echo json_encode($datas); ?>;
		console.log(data);
        if(data.length > 0) {
            $('#table-data').DataTable();
        }
    });
</script>

<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php echo e(url('/index')); ?>" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="current">List Report Monitoring FPPB</a></div>
    <h1>List Report Monitoring FPPB</h1>
  </div>
<!--End-breadcrumbs-->

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                  <h5>Cari FPPB</h5>
                </div>
                <div class="widget-content nopadding">
                  <form class="form-horizontal" action="<?php echo e(route('report.filter')); ?>" method="get">
                     <div class="control-group">
                      <label class="control-label">No FPPB</label>
                      <div class="controls">
                        <input style="width: 20%" type="text" class="span11" name="nofppb" id="nofppb" placeholder="Masukan no FPPB">
                      </div>
                    </div>

                    <div class="form-actions">
                      <button type="submit" class="btn btn-success" onclick="filter()">Find</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>

        <div class="span12" style="margin-left: 0%">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
                </div>
                <div class="widget-content nopadding">
                    <table id="table-data" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>No. FPPB</th>
                            <th>Tanggal FPPB</th>
                            <th>Divisi Request</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php  $no = 1;  ?>
                        <?php $__empty_1 = true; $__currentLoopData = $datas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($no++); ?></td>
                                <td><?php echo e($data->notrx); ?></td>
                                <td><?php echo e($data->dtfppb); ?></td>
                                <td><?php echo e($data->div_nama); ?></td>
                                <td><?php echo e($data->statustype); ?> <?php echo e($data->approvaltype); ?></td>
                                <td>
                                    <form action="<?php echo e(url('/detailreport/'.$data->notrx)); ?>" method="get">
                                        <button class="btn btn-sm btn-primary" type="submit">Detail</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6"><b>Tidak ada data !</b></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>