<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\BranchUser;
use Closure;

use Sentinel;

class CheckBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('branch_id')) {
            //try to set the session
            if (count(Branch::all()) == 0) {
                //no branches, return
               return redirect()->back()->with("success" , "No branches yet");
            } else {
                //we have branches
                if (count(BranchUser::where('user_id', Sentinel::getUser()->id)->get()) > 0) {
                    //try to set 1 branch as current
                    foreach (BranchUser::where('user_id', Sentinel::getUser()->id)->orderBy('created_at',
                        'desc')->get() as $key) {
                        if (!empty($key->branch)) {
                            //set session and exit
                            $request->session()->put('branch_id', $key->branch_id);
                            return $next($request);
                        }
                    }
                } else {
                    redirect()->back()->with("success" , "No permission" );
                }
            }
                 redirect()->back()->with("success" , "No permission" );
        } elseif (!empty(Branch::find($request->session()->has('branch_id')))) {
            return $next($request);
        }else{
              redirect()->back()->with("success" , "No permission" );
        }
    }
}
