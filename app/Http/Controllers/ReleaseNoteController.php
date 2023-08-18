<?php

namespace App\Http\Controllers;

use App\Models\ReleaseNote;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

class ReleaseNoteController extends Controller
{
    public function index()
    {
        $data = ReleaseNote::orderBy("id", "desc")->with('poster')->get();

        return view("release_note.data", compact("data"));
    }

    public function store(Request $request)
    {
        $releaseNote = new ReleaseNote();
        $releaseNote->version_number = $request->version_number;
        $releaseNote->is_added = ($request->is_added == "on") ? true : false;
        $releaseNote->added_note = $request->added_feature;
        $releaseNote->is_changed = ($request->is_changed == "on") ? true : false;
        $releaseNote->changed_note = $request->changed_feature;
        $releaseNote->is_fixed = ($request->is_fixed == "on") ? true : false;
        $releaseNote->fixed_note = $request->fixed_feature;
        $releaseNote->posted_by = Sentinel::getUser()->id;
        $releaseNote->save();

        User::query()->update(['release_note_status' => true]);

        return redirect()->route('release-note');
    }

    public function changeStatus()
    {
        $user = User::find(Sentinel::getUser()->id);
        $user->release_note_status = false;
        $user->save();

        return redirect()->route('dashboard');
    }
}
