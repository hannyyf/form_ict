<?php $__env->startSection('content'); ?>
<!--breadcrumbs-->
<div id="content-header">
<div id="breadcrumb"> <a href="<?php echo e(url('/index')); ?>" title="Go to Home" class="current"><i class="icon-home"></i> Home</a></div>
</div>
<!--End-breadcrumbs-->
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h1>Berhasil Login ! </h1>
            <p>Nama : <?php echo $user->name; ?></p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>