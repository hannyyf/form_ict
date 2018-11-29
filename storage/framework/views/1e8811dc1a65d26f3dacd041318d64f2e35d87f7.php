<?php $__env->startSection('content'); ?>
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php echo e(url('/index')); ?>" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="current">List Approve Director</a></div>
    <h1>List Approve Director</h1>
  </div>
<!--End-breadcrumbs-->

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <?php if(Session::has('alert-success')): ?>
                <div class="alert alert-success">
                    <strong><?php echo e(\Illuminate\Support\Facades\Session::get('alert-success')); ?></strong>
                </div>
            <?php endif; ?>
             <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>No. FPPB</th>
                    <th>Tanggal FPPB</th>
                    <th>Divisi Request</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php  $no = 1;  ?>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $datas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($no++); ?></td>
                        <td><?php echo e($datas->notrx); ?></td>
                        <td><?php echo e($datas->dtfppb); ?></td>
                        <td><?php echo e($datas->div_nama); ?></td>
                        <td>
                            <form action="<?php echo e(url('/detaildir/'.$datas->notrx)); ?>" method="get">
                                <button class="btn btn-sm btn-primary" type="submit">Detail</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>