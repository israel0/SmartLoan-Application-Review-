<?php

namespace App\Http\Controllers;

use App\Models\ProvisionRate;
use App\Models\Tax;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class ProvisionRateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel', 'branch']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ProvisionRate::all();
        return view('provision.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('provision.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('coffmmunication')) {
            return redirect('/')->with("warning" , "Permission Denied" );
        }
        $provision=new ProvisionRate();
        $provision->title=$request->name;
        $provision->percentage=$request->percentage;
        $provision->notes=$request->notes;
        $provision->save();
        return redirect('loan/provision/data')->with("success" , trans("general.successfully_saved"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function edit($provision)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        return View::make('provision.edit', compact('provision'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            return redirect('/')->with("warning" , "Permission Denied" );
        }
        $provision=ProvisionRate::find($id);
        $provision->rate=$request->rate;
        $provision->notes=$request->notes;
        $provision->save();
        return redirect('loan/provision/data')->with("success" , trans("general.successfully_saved"));;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('loans.updatsse')) {
            return redirect('/')->with("warning" , "Permission Denied" );
        }
        ProvisionRate::destroy($id);
        Flash::success("Successfully Deleted");
        return redirect('loan/provision/data')->with("success" , trans("general.successfully_deleted"));;;
    }
}
