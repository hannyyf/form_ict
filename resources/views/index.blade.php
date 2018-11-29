@extends('base')
@section('content')
<!--breadcrumbs-->
<div id="content-header">
<div id="breadcrumb"> <a href="{{ url('/index') }}" title="Go to Home" class="current"><i class="icon-home"></i> Home</a></div>
</div>
<!--End-breadcrumbs-->
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h1>Berhasil Login ! </h1>
            <p>Nama : {!! $user->name !!}</p>
        </div>
    </div>
</div>
@endsection