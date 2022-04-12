<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Session;
use App\Notifications\NewForum;
use App\Models\Forum;

use App\Models\User;
use Telegram;

class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $forums = Forum::latest()->paginate(10);
        return view('admin.pages.forums', \compact('forums'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::latest()->get();
        return view('admin.pages.new_forum', \compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'desc'=>'required'
        ]);
        Forum::create($request->all());
        Session::flash('message', 'Forum  Created Successfully');
        Session::flash('alert-class', 'alert-success');
        toastr()->success('Forum Started successfully!');

        $latestForum = Forum::latest()->first();
        $admins = User::where('is_admin', 1)->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewForum($latestForum));
        }

        toastr()->success('Category Created successfully!');
        Telegram::sendMessage([
            'chat_id'=>env('TELEGRAM_CHAT_ID', '-1001162615538'),
            'parse_mode'=>'HTML',
            'text'=>'<b>'.auth()->user()->name."</b>"." Created Category "."<b>".$request->title."</b>"
        ]);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $forum = Forum::find($id);
        $categories = Category::latest()->get();

        return view('admin.pages.edit_forum', \compact('forum', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $forum = Forum::find($id);
        $forum->update($request->all());
        Session::flash('message', 'Forum  Updated Successfully');
        Session::flash('alert-class', 'alert-success');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $forum = Forum::find($id);
        $forum->delete();
        Session::flash('message', 'Forum  Deleted Successfully');
        Session::flash('alert-class', 'alert-success');
        return back();
    }
}
