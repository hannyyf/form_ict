<!DOCTYPE html>
<html lang="en">
    
<head>
        <title>Form ICT</title><meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="/assets/css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="/assets/css/matrix-login.css" />
        <link href="/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
     <script>

    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
      alert('Anda membuka aplikasi di Android, Silahkan login langsung di aplikasi ini');
    }
    else{
      alert('silakan gunakan aplikasi Single Login untuk masuk ke aplikasi yang diinginkan');
      window.open('http://djmapp.djabesmen.net/', '_self');
    }
    </script>
    </head>
    <body>
        <div id="loginbox">            
            <form id="loginform" class="form-vertical" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="control-group normal_text"> <h3><img src="/assets/img/logodjm.jpg" alt="Logo" /></h3></div>
                <div class="control-group form-group{{ $errors->has('identity') ? ' has-error' : '' }}">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lg"><i class="icon-user"> </i></span><input id="identity" type="text" placeholder="NIK" name="identity" value="{{ old('identity') }}" style="background-color: #dedede" required autofocus/>
                                @if ($errors->has('identity'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('identity') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                </div>
                <div class="control-group form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_ly"><i class="icon-lock"></i></span><input id="password" type="password" placeholder="Password" name="password" style="background-color: #dedede" required/>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" style="width: 100%;background-color: #004488;color: #fff; padding: 10px;font-size:16px" id="login" /> Login</button>
                </div>
            </form>
        </div>
        
        <script src="/assets/js/jquery.min.js"></script>  
        <script src="/assets/js/matrix.login.js"></script> 
        
    </body>

</html>