<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <title>Form ICT</title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="/assets/css/fullcalendar.css" />
    <link rel="stylesheet" href="/assets/css/matrix-style.css" />
    <link rel="stylesheet" href="/assets/css/matrix-media.css" />
    <link href="/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/jquery.gritter.css" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>

</head>

<body>

<!--Header-part-->
<div id="header">
  <h1><a href="#" class="fa fa-align-justify"></a></h1>
</div>
<!--close-Header-part--> 

<!--top-Header-menu-->
<div id="user-nav" class="navbar navbar-inverse">
  <ul class="nav">
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="icon icon-user"></i><span class="text">Welcome <?php echo e(Auth::user()->name); ?></span><b class="caret"></b>
        </a>

        <ul class="dropdown-menu" role="menu">
            <li>
                <a href="<?php echo e(route('logout')); ?>"
                    onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    Logout
                </a>

                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                    <?php echo e(csrf_field()); ?>

                </form>
            </li>
        </ul>
    </li>
  </ul>
</div>
<!--close-top-Header-menu-->

<!--sidebar-menu-->
<div id="sidebar">
  <ul>
    <?php if(Auth::user()->jabatan == 'user'): ?>
    <li>
        <a href="<?php echo e(url('requestfppb')); ?>">Request FPPB</a>
    </li>
    <li>
        <a href="<?php echo e(url('/detailrequested')); ?>">Approve Request Close</a>
    </li>
     <li>
        <a href="<?php echo e(url('/report')); ?>">Report Monitoring</a>
    </li>
    <?php elseif(Auth::user()->jabatan == 'ictmanager'): ?>
     <li>
        <a href="<?php echo e(url('requestfppb')); ?>">Request FPPB</a>
    </li>
    <li>
        <a href="<?php echo e(url('approvedivhead')); ?>">Approve FPPB Div Head</a>
    </li>
     <li>
        <a href="<?php echo e(url('approveictmgr')); ?>">Approve FPPB Div Head ICT</a>
    </li>
    <li>
        <a href="<?php echo e(url('/detailrequested')); ?>">Approve Request Close</a>
    </li>
     <li>
        <a href="<?php echo e(url('/report')); ?>">Report Monitoring</a>
    </li>

    <?php elseif(Auth::user()->jabatan == 'divhead' || Auth::user()->jabatan == 'ictmanager' ): ?>
    <li>
        <a href="<?php echo e(url('requestfppb')); ?>">Request FPPB</a>
    </li>
    <li>
        <a href="<?php echo e(url('approvedivhead')); ?>">Approve FPPB Div Head</a>
    </li>
    <li>
        <a href="<?php echo e(url('/detailrequested')); ?>">Approve Request Close</a>
    </li>
    <li>
        <a href="<?php echo e(url('/report')); ?>">Report Monitoring</a>
    </li>

    <?php elseif(Auth::user()->jabatan == 'director'): ?>
     <li>
        <a href="<?php echo e(url('approvedirector')); ?>">Approve FPPB Director</a>
    </li>
    <li>
        <a href="<?php echo e(url('/report')); ?>">Report Monitoring</a>
    </li>

    <?php elseif(Auth::user()->jabatan == 'ict'): ?>
    <li>
      <a href="<?php echo e(url('requestfppb')); ?>">Request FPPB</a>
    </li>
    <li>
      <a href="<?php echo e(url('reviewict')); ?>">Review ICT</a>
    </li>
    <li>
      <a href="<?php echo e(url('mappingict')); ?>">Mapping dan Generate PR</a>
    </li>
    <li>
      <a href="<?php echo e(url('serahterima')); ?>">Request Close</a>
    </li>
    <li>
        <a href="<?php echo e(url('/detailrequested')); ?>">Approve Request Close</a>
    </li>
     <li>
        <a href="<?php echo e(url('/report')); ?>">Report Monitoring</a>
    </li>

    <?php elseif(Auth::user()->jabatan == 'dic'): ?>
     <li>
        <a href="<?php echo e(url('approvedirector')); ?>">Approve FPPB Director</a>
    </li>
    <li>
        <a href="<?php echo e(url('approvedic')); ?>">Approve FPPB DIC</a>
    </li>
    <li>
        <a href="<?php echo e(url('/report')); ?>">Report Monitoring</a>
    </li>
    
    <?php endif; ?>
  </ul>
</div>
<!--sidebar-menu-->

<!--main-container-part-->
<div id="content">
    <?php echo $__env->yieldContent('content'); ?> 
</div>
<!--end-main-container-part-->

<!--Footer-part-->

<div class="row-fluid">
  <div id="footer" class="span12"> 2018 &copy; ICT PT Djabesmen </div>
</div>

<!--end-Footer-part-->

<script src="/assets/js/excanvas.min.js"></script> 
<script src="/assets/js/jquery.min.js"></script> 
<script src="/assets/js/jquery.ui.custom.js"></script> 
<script src="/assets/js/bootstrap.min.js"></script> 
<script src="/assets/js/jquery.flot.min.js"></script> 
<script src="/assets/js/jquery.flot.resize.min.js"></script> 
<script src="/assets/js/jquery.peity.min.js"></script> 
<script src="/assets/js/fullcalendar.min.js"></script> 
<script src="/assets/js/matrix.js"></script> 
<script src="/assets/js/matrix.dashboard.js"></script> 
<script src="/assets/js/jquery.gritter.min.js"></script> 
<script src="/assets/js/matrix.interface.js"></script> 
<script src="/assets/js/matrix.chat.js"></script> 
<script src="/assets/js/jquery.validate.js"></script> 
<script src="/assets/js/matrix.form_validation.js"></script> 
<script src="/assets/js/jquery.wizard.js"></script> 
<script src="/assets/js/jquery.uniform.js"></script> 
<script src="/assets/js/select2.min.js"></script> 
<script src="/assets/js/matrix.popover.js"></script> 
<script src="/assets/js/jquery.dataTables.min.js"></script> 
<script src="/assets/js/matrix.tables.js"></script> 

<script type="text/javascript">
  // This function is called from the pop-up menus to transfer to
  // a different page. Ignore if the value returned is a null string:
  function goPage (newURL) {

      // if url is empty, skip the menu dividers and reset the menu selection to default
      if (newURL != "") {
      
          // if url is "-", it is this page -- reset the menu:
          if (newURL == "-" ) {
              resetMenu();            
          } 
          // else, send page to designated URL            
          else {  
            document.location.href = newURL;
          }
      }
  }

// resets the menu selection upon entry to this page:
function resetMenu() {
   document.gomenu.selector.selectedIndex = 2;
}
</script>
</body>
</html>