<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
    
</head>

<body>

<!--Header-part-->
<div id="header">
  <!-- <h1>Header</h1> -->
</div>
<!--close-Header-part--> 

<!--top-Header-menu-->
<div id="user-nav" class="navbar navbar-inverse">
  <ul class="nav">
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="icon icon-user"></i> <span class="text"> Welcome {{ Auth::user()->name }}</span><b class="caret"></b>
        </a>

        <ul class="dropdown-menu" role="menu">
            <li>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        </ul>
    </li>
    <li class=""><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <i class="icon icon-off"></i> <span class="text">Logout</span></a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    </li>
  </ul>
</div>
<!--close-top-Header-menu-->

<!--sidebar-menu-->
<div id="sidebar"><a href="#" class="visible-phone"><i class="icon icon-home"></i> Menu</a>
  <ul>
    @if (Auth::user()->jabatan == 'user')
    <li>
        <a href="{{ url('requestfppb') }}" title="Request FPPB" class="tip-right"><i class="icon icon-plus"></i><span>Request FPPB</span></a>
    </li>
    <li>
        <a href="{{ url('/detailrequested') }}" title="Approve Request Close FPPB" class="tip-right"><i class="fas fa-user-check"></i><span>Approve Request Close</span></a>
    </li>
     <li>
        <a href="{{ url('/report') }}" title="Report Monitoring" class="tip-right"><i class="icon icon-search"></i><span>Report Monitoring</span></a>
    </li>
    @elseif (Auth::user()->jabatan == 'ictmanager')
    <li>
        <a href="{{ url('requestfppb') }}" title="Request FPPB" class="tip-right"><i class="icon icon-plus"></i><span>Request FPPB</span></a>
    </li>
    <li>
        <a href="{{ url('approvedivhead') }}" title="Approve Div Head" class="tip-right"><i class="fas fa-check"></i><span>Approve FPPB Div Head</span></a>
    </li>
     <li>
        <a href="{{ url('approveictmgr') }}" title="Approve ICT Manager" class="tip-right"><i class="fas fa-check-double"></i><span>Approve FPPB ICT Manager</span></a>
    </li>
    <li>
        <a href="{{ url('/detailrequested') }}" title="Approve Request Close" class="tip-right"><i class="fas fa-user-check"></i><span>Approve Request Close</span></a>
    </li>
    <li>
        <a href="{{ url('/report') }}" title="Report Monitoring" class="tip-right"><i class="icon icon-search"></i><span>Report Monitoring</span></a>
    </li>

    @elseif (Auth::user()->jabatan == 'divhead' || Auth::user()->jabatan == 'ictmanager' )
    <li>
        <a href="{{ url('requestfppb') }}" title="Request FPPB" class="tip-right"><i class="icon icon-plus"></i><span>Request FPPB</span></a>
    </li>
    <li>
        <a href="{{ url('approvedivhead') }}" title="Approve Div Head" class="tip-right"><i class="fas fa-check"></i><span>Approve FPPB Div Head</span></a>
    </li>
    <li>
        <a href="{{ url('/detailrequested') }}" title="Approve Request Close" class="tip-right"><i class="fas fa-user-check"></i><span>Approve Request Close</span></a>
    </li>
    <li>
        <a href="{{ url('/report') }}" title="Report Monitoring" class="tip-right"><i class="icon icon-search"></i><span>Report Monitoring</span></a>
    </li>

    @elseif (Auth::user()->jabatan == 'director')
     <li>
        <a href="{{ url('approvedirector') }}" title="Approve Director" class="tip-right"><i class="fas fa-check"></i><span>Approve FPPB Director</span></a>
    </li>
   <!--  <li>
        <a href="{{ url('/report') }}" title="Report Monitoring" class="tip-right"><i class="icon icon-search"></i><span>Report Monitoring</span></a>
    </li> -->

    @elseif (Auth::user()->jabatan == 'ict')
    <li>
    <a href="{{ url('requestfppb') }}" title="Request FPPB" class="tip-right"><i class="icon icon-plus"></i><span>Request FPPB</span></a>
    </li>
    <li>
      <a href="{{ url('reviewict') }}" title="Review ICT" class="tip-right"><i class="fa fa-check-square"></i><span>Review ICT</span></a>
    </li>
    <li>
      <a href="{{ url('mappingict') }}" title="Mapping dan Generate PR" class="tip-right"><i class="far fa-copy"></i><span>Mapping dan Generate PR</span></a>
    </li>
    <li>
      <a href="{{ url('serahterima') }}" title="Request Close" class="tip-right"><i class="fa fa-times-circle" aria-hidden="true"></i><span>Request Close</span></a>
    </li>
    <li>
        <a href="{{ url('/detailrequested') }}" title="Approve Request Close" class="tip-right"><i class="fas fa-user-check"></i><span>Approve Request Close</span></a>
    </li>
    <li>
        <a href="{{ url('/report') }}" title="Report Monitoring" class="tip-right"><i class="icon icon-search"></i><span>Report Monitoring</span></a>
    </li>
    <li>
        <a href="{{ route('transfer.index') }}" title="Transfer FPPB" class="tip-right"><i class="fas fa-exchange-alt"></i><span>Transfer FPPB</span></a>
    </li>

    @elseif (Auth::user()->jabatan == 'dic')
    <li>
        <a href="{{ url('approvedirector') }}" title="Approve Director" class="tip-right"><i class="fas fa-check"></i><span>Approve FPPB Director</span></a>
    </li>
    <li>
        <a href="{{ url('approvedic') }}" title="Approve DIC" class="tip-right"><i class="fas fa-check-double"></i><span>Approve FPPB DIC</span></a>
    </li>
   <!--  <li>
        <a href="{{ url('/report') }}" title="Report Monitoring" class="tip-right"><i class="icon icon-search"></i><span>Report Monitoring</span></a>
    </li> -->
    
    @endif
  </ul>
</div>
<!--sidebar-menu-->

<!--main-container-part-->
<div id="content">
    @yield('content') {{-- Semua file konten kita akan ada di bagian ini --}}
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