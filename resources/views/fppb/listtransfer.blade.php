@extends('base')
@section('content')
<script type="text/javascript">
    function filter() {
        var nofppb = $('#nofppb').val();
        console.log('nofppb', nofppb);
        $.ajax({
            url:"{{ route('transfer.find') }}",
            method:"GET",
            data:{nofppb:nofppb},
            success:function(result)
            {
                console.log('resulttttttttttttt',result);
            }
        });
    };
</script>

<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/index') }}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="current">List Transfer FPPB</a></div>
    <h1>List Transfer FPPB</h1>
  </div>
<!--End-breadcrumbs-->

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            @if(Session::has('alert-success'))
                <div class="alert alert-success">
                    <strong>{{ \Illuminate\Support\Facades\Session::get('alert-success') }}</strong>
                </div>
            @endif

            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                  <h5>Cari FPPB</h5>
                </div>
                <div class="widget-content nopadding">
                  <form class="form-horizontal" action="{{ route('transfer.find') }}" method="get">
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
                    <table class="table table-bordered table-striped">
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
                        @php $no = 1; @endphp
                        @forelse($datas as $data)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $data->notrx }}</td>
                                <td>{{ $data->dtfppb }}</td>
                                <td>{{ $data->div_nama }}</td>
                                <td>{{ $data->statustype }} {{$data->approvaltype}}</td>
                                <td>
                                    <form action="{{ url('/detailtransfer/'.$data->notrx) }}" method="get">
                                        <button class="btn btn-sm btn-primary" type="submit">Detail</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"><b>Tidak ada data !</b></td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

@endsection