<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function GetLogin(Request $request)
    {
      	$user = $request->input('user');
        $portal = $request->input('portal');
        $pass = $request->input('pass');

        $seluser = DB::select("SELECT * from users where username = '".$user."'");

        // return response()->json([$seluser]);
          $data = array();
        foreach ($seluser as $seluser) {
            $data[] = $seluser;
        }

        // $val = $seluser->password;
        $val = '123456';
          // return $val;

          $url = 'localhost:8000/login';
      //$myvars = 'username='.$seluser->username.'&password='.$seluser->password;
      $myvars = array('username' => $seluser->username, 'g_pass' => $val);

      $_SESSION['portal'] = 'yes';
      echo'
        <form style="display : none;" id="loginform" method="POST" action="http://localhost:8000/login">
        <input type="hidden" name="_token" value="ec5tcXtejW0ayMvHrIRePwLBrtxbemWPJ7EQM4OJVTeRn3BnQ6nUyx3CZdzt">
        <input type="text" name="identity" value="'.$user.'">  
        <input type="text" name="password" value="'.$val.'">  
        <button type="submit" class="button primary">Login</button>
        </form>
        ';


        echo'
      <script>

      document.getElementById("loginform").submit();

      </script>
        ';
                
    }

    public function djmappindex()
    {
        $db_ext = DB::connection('sqlsrv_djmapp');
        $fetdjmappusr = $db_ext->table('ptl_user')
                     ->join('ptl_divisi','ptl_user.user_div','=','ptl_divisi.div_id')
                     ->get();
        return $fetdjmappusr;
    }
}