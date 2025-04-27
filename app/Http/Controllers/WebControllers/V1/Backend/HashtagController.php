<?php

namespace App\Http\Controllers\WebControllers\V1\Backend;

use App\Http\Controllers\BaseController;
use App\Services\HashtagServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HashtagController extends BaseController
{
    private $hashtagServices;

    public function __construct(HashtagServices $hashtagServices)
    {
        $this->hashtagServices = $hashtagServices;
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $hashtags = $this->hashtagServices->index($request);
        return view('Backend.pages.hashtags.list', compact('hashtags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Backend.pages.hashtags.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $hashtag= $request->only([
            'name',
        ]);

        DB::beginTransaction();
        try {

            $result = $this->hashtagServices->save($hashtag);
            DB::commit();

            $request->session()->flash('msgSuccess', trans('hashtag.msg_content.msg_add_success'));
            return redirect()->route('hashtags.edit', ['id' => $result->id]);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('msgFail', $e->getMessage());
            return back()->with(['hashtags' => $hashtag]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request,string $id)
    {
        $hashtag = $this->hashtagServices->show($id);
        return view('Backend.pages.hashtags.edit', compact('hashtag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $hashtag = $request->only([
            'id',
            'name',
        ]);

        DB::beginTransaction();
        try {

            $result = $this->hashtagServices->save($hashtag);
            DB::commit();

            $request->session()->flash('msgSuccess', trans('hashtag.msg_content.msg_edit_success'));
            return redirect()->route('hashtags.edit', ['id' => $result->id]);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('msgFail', $e->getMessage());
            return back()->with(['hashtag' => $hashtag]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $result = $this->hashtagServices->delete($id);
        if (!$result) {
            $request->session()->flash('msgFail', trans('hashtag.msg_content.msg_delete_fail'));
        }
        return redirect()->route('hashtags.list');
    }
}
