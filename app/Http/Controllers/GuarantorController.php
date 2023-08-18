<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\GeneralHelper;
use App\Models\Borrower;

use App\Models\Country;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Guarantor;
use App\Models\Saving;
use App\Models\SavingTransaction;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Clickatell\Api\ClickatellHttp;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class GuarantorController extends Controller
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
        $data = Guarantor::all();

        return view('guarantor.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('loans.guarantor.create')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $countries = array();
        foreach (Country::all() as $key) {
            $countries[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'guarantors')->get();
        return view('guarantor.create', compact('custom_fields', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('loans.guarantor.create')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $guarantor = new Guarantor();
        $guarantor->first_name = $request->first_name;
        $guarantor->last_name = $request->last_name;
        $guarantor->user_id = Sentinel::getUser()->id;
        $guarantor->gender = $request->gender;
        $guarantor->country_id = $request->country_id;
        $guarantor->title = $request->title;
        $guarantor->branch_id = session('branch_id');
        $guarantor->mobile = $request->mobile;
        $guarantor->notes = $request->notes;
        $guarantor->email = $request->email;
        if ($request->hasFile('photo')) {
            $file = array('photo' => $request->file('photo'));
            $rules = array('photo' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
          return redirect()->back()->with("warning",trans('general.validation_error'));
            } else {
                $fname = "guarantor_" . uniqid() . '.' . $request->file('photo')->guessExtension();
                $guarantor->photo = $fname;
                $request->file('photo')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $guarantor->unique_number = $request->unique_number;
        $guarantor->dob = $request->dob;
        $guarantor->address = $request->address;
        $guarantor->city = $request->city;
        $guarantor->state = $request->state;
        $guarantor->zip = $request->zip;
        $guarantor->phone = $request->phone;
        $guarantor->business_name = $request->business_name;
        $guarantor->working_status = $request->working_status;
        $files = array();
        if (!empty($request->file('files'))) {
            $count = 0;
            foreach ($request->file('files') as $key) {
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
           return redirect()->back()->with("warning",trans('general.validation_error'));
                } else {
                    $fname = "borrower_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }
                $count++;
            }
        }
        $guarantor->files = serialize($files);
        $guarantor->save();
        $custom_fields = CustomField::where('category', 'guarantors')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $guarantor->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "guarantors";
            $custom_field->save();
        }
        GeneralHelper::audit_trail("Added guarantor  with id:" . $guarantor->id);
        return redirect('guarantor/data')->with("success" , trans('general.successfully_saved'));
    }


    public function show($guarantor)
    {
      
        //get custom fields
        $custom_fields = CustomFieldMeta::where('category', 'guarantors')->where('parent_id', $guarantor->id)->get();
        return view('guarantor.show', compact('guarantor', 'custom_fields'));
    }


    public function edit($guarantor)
    {
        if (!Sentinel::hasAccess('loans.guarantor.update')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $countries = array();
        foreach (Country::all() as $key) {
            $countries[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'guarantors')->get();
        return view('guarantor.edit', compact('guarantor', 'custom_fields', 'countries'));
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
        if (!Sentinel::hasAccess('loans.guarantor.update')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $guarantor = Guarantor::find($id);
        $guarantor->first_name = $request->first_name;
        $guarantor->last_name = $request->last_name;
        $guarantor->gender = $request->gender;
        $guarantor->country_id = $request->country_id;
        $guarantor->title = $request->title;
        $guarantor->mobile = $request->mobile;
        $guarantor->notes = $request->notes;
        $guarantor->email = $request->email;
        if ($request->hasFile('photo')) {
            $file = array('photo' => $request->file('photo'));
            $rules = array('photo' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
          return redirect()->back()->with("warning",trans('general.validation_error'));
            } else {
                $fname = "guarantor_" . uniqid() . '.' . $request->file('photo')->guessExtension();
                $guarantor->photo = $fname;
                $request->file('photo')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $guarantor->unique_number = $request->unique_number;
        $guarantor->dob = $request->dob;
        $guarantor->address = $request->address;
        $guarantor->city = $request->city;
        $guarantor->state = $request->state;
        $guarantor->zip = $request->zip;
        $guarantor->phone = $request->phone;
        $guarantor->business_name = $request->business_name;
        $guarantor->working_status = $request->working_status;
        $files = unserialize($guarantor->files);
        $count = count($files);
        if (!empty($request->file('files'))) {
            foreach ($request->file('files') as $key) {
                $count++;
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
           return redirect()->back()->with("warning",trans('general.validation_error'));
                } else {
                    $fname = "guarantor_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }

            }
        }
        $guarantor->files = serialize($files);
        $guarantor->save();
        $custom_fields = CustomField::where('category', 'guarantors')->get();
        foreach ($custom_fields as $key) {
            if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                'guarantors')->first())
            ) {
                $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'guarantors')->first();
            } else {
                $custom_field = new CustomFieldMeta();
            }
            $kid = $key->id;
            $custom_field->name = $request->$kid;
            $custom_field->parent_id = $id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "guarantors";
            $custom_field->save();
        }
        GeneralHelper::audit_trail("Updated guarantor  with id:" . $guarantor->id);
       return redirect('guarantor/data')->with("success" , trans('general.successfully_saved'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('loans.guarantor.delete')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        Guarantor::destroy($id);
       return redirect('guarantor/data')->with("success" , trans('general.successfully_deleted'));
    }

    public function deleteFile(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.guarantor.delete')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $borrower = Guarantor::find($id);
        $files = unserialize($borrower->files);
        @unlink(public_path() . '/uploads/' . $files[$request->id]);
        $files = array_except($files, [$request->id]);
        $borrower->files = serialize($files);
        $borrower->save();

    }
}
