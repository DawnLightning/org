<?php

namespace App\Http\Middleware;

use Closure;
use Jtools\Uchelper;
class Cauth {
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @param \Closure $next        	
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		//$path = public_path ();
		
		//include_once app_path().'/Uchelper/uchelper.php';
		$uc=new Uchelper();
		if($uc->_SGLOBAL ['supe_uid']==0){
			return redirect()->guest('/');
		}
		return $next ( $request );
	}
}
