<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class BranchController extends Controller
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
        $data = Branch::all();
        return view('branch.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // I will come back to this endpo
        return view('branch.create');
    }

    public function change()
    {
        $branches = array();
//        foreach (BranchUser::where('user_id', Sentinel::getUser()->id)->orderBy('created_at',
//            'desc')->get() as $key) {
//            if (!empty($key->branch)) {
//                $branches[$key->branch_id] = $key->branch->name;
//            }
//        }
        foreach (Branch::orderBy('id', 'asc')->get() as $key) {
            if (!empty($key->id)) {
                $branches[$key->id] = $key->name;
            }
        }
        //get custom fields
        return view('branch.change', compact('branches'));
    }

    public function updateChange(Request $request)
    {
        $request->session()->put('branch_id', $request->branch_id);
        //get custom fields
        return redirect('dashboard');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $branch = new Branch();
        $branch->name = $request->name;
        $branch->notes = $request->notes;
        $branch->save();
         return redirect()->back()->with("success" , 'general.successfully_saved' );
    }


    public function show($branch)
    {
        $users = array();
        foreach (User::all() as $key) {
            $users[$key->id] = $key->first_name . ' ' . $key->last_name . '(' . $key->id . ')';
        }
        return view('branch.show', compact('branch', 'users'));
    }


    public function edit($branch)
    {
        return view('branch.edit', compact('branch'));
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
        $branch = Branch::find($id);
        $branch->name = $request->name;
        $branch->notes = $request->notes;
        $branch->save();
         return redirect()->back()->with("success" , 'general.successfully_saved' );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $branch = Branch::find($id);
        if ($branch->default_branch == 1) {
            return redirect()->back()->with("warning" , "You cannot delete default branch. Its need to keep things working well." );
        }
        Branch::destroy($id);
      return redirect()->back()->with("success" , 'general.successfully_saved' );
    }

    public function addUser(Request $request, $id)
    {
        if (BranchUser::where('branch_id', $id)->where('user_id', $request->user_id)->count() > 0) {
            return redirect()->back()->with("warning" , trans('general.user_already_added_to_branch'));
        }
        $user = new BranchUser();
        $user->branch_id = $id;
        $user->user_id = $request->user_id;
        $user->save();
        return redirect()->back()->with("success" , 'general.successfully_saved' );
    }

    public function removeUser(Request $request, $id)
    {
        BranchUser::destroy($id);
        return redirect()->back()->with("success" , 'general.successfully_saved' );
    }

}
