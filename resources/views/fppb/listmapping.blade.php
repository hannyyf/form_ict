@extends('base')
@section('content')
<!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/index') }}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="#" class="current">List Mapping</a></div>
    <h1>List Mapping</h1>
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
                @php $no = 1; @endphp
                @foreach($data as $datas)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $datas->notrx }}</td>
                        <td>{{ $datas->dtfppb }}</td>
                        <td>{{ $datas->div_nama }}</td>
                        <td>
                            <form action="{{ url('/detailmapping/'.$datas->notrx) }}" method="get">
                                <button class="btn btn-sm btn-primary" type="submit">Detail</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection