<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(\Auth::user() && \Auth::user()->can('Manage Testimonial')){
            
            $testimonials = Testimonial::where('store_id', Auth::user()->current_store)->where('created_by', Auth::user()->id)->get();

            return view('testimonial.index', compact('testimonials'));
        } else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(\Auth::user() && \Auth::user()->can('Create Testimonial')){
            return view('testimonial.create');
        } else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(\Auth::user() && \Auth::user()->can('Create Testimonial')){
            $validator = \Validator::make(
                $request->all(), [
                    'title' => 'required|max:120',
                    'sub_title' => 'required|max:120',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if(!empty($request->image))
            {
                $image_size = $request->file('image')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if($result == 1)
                {
                    $filenameWithExt  = $request->file('image')->getClientOriginalName();
                    $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension        = $request->file('image')->getClientOriginalExtension();
                    $fileNameToStores = $filename . '_' . time() . '.' . $extension;
                    $dir        = 'uploads/testimonial_image/';
                    
                    $path = Utility::upload_file($request, 'image', $fileNameToStores, $dir, []);

                    if($path['flag'] == 1){
                        $url = $path['url'];
                    }else{
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
            }

            $testimonial              = new Testimonial();
            if(!empty($fileNameToStores)){
                $testimonial->image       = $fileNameToStores;
            }
            $testimonial->title       = $request->title;
            $testimonial->sub_title   = $request->sub_title;
            $testimonial->ratting     = $request->rate;
            $testimonial->description = $request->description ?? '';
            $testimonial->store_id    = \Auth::user()->current_store;
            $testimonial->created_by  = \Auth::user()->creatorId();
            $testimonial->save();

            return redirect()->back()->with('success', __('Testimonial Created Successfully!') . ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
        } else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if(\Auth::user() && \Auth::user()->can('Manage Testimonial')){
            // return view('testimonial.create');
        } else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if(\Auth::user() && \Auth::user()->can('Edit Testimonial')){
            $testimonial = Testimonial::where('id', $id)->where('store_id', \Auth::user()->current_store)->first();
            if (isset($testimonial) && !empty($testimonial)){
                return view('testimonial.edit', compact('testimonial'));
            } else {
                return redirect()->back()->with('error', __('Testimonial not found.'));
            }
        } else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user() && \Auth::user()->can('Edit Testimonial')){
            $testimonial = Testimonial::where('id', $id)->where('store_id', \Auth::user()->current_store)->first();
            
            if (isset($testimonial) && !empty($testimonial)){

                $validator = \Validator::make(
                    $request->all(), [
                        'title' => 'required|max:120',
                        'sub_title' => 'required|max:120',
                    ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                if(!empty($request->image))
                {
                    $fileName = $testimonial->image !== 'avatar.png' ? $testimonial->image : '' ;
                    $filePath ='uploads/testimonial_image/'. $fileName;

                    $image_size = $request->file('image')->getSize();
                    $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                    if($result == 1){
                        Utility::changeStorageLimit(\Auth::user()->creatorId(), $filePath);
                        $filenameWithExt  = $request->file('image')->getClientOriginalName();
                        $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension        = $request->file('image')->getClientOriginalExtension();
                        $fileNameToStores = $filename . '_' . time() . '.' . $extension;
                        $dir        = 'uploads/testimonial_image/';
                        
                        $path = Utility::upload_file($request,'image',$fileNameToStores,$dir,[]);

                        if($path['flag'] == 1){
                            $url = $path['url'];
                        }else{
                            return redirect()->back()->with('error', __($path['msg']));
                        }
                    }
                }

                if(!empty($fileNameToStores)){
                    $testimonial->image       = $fileNameToStores;
                }
                $testimonial->title       = $request->title;
                $testimonial->sub_title   = $request->sub_title;
                $testimonial->ratting     = $request->rate;
                $testimonial->description = $request->description ?? '';
                $testimonial->store_id    = \Auth::user()->current_store;
                $testimonial->created_by  = \Auth::user()->creatorId();
                $testimonial->update();

                return redirect()->back()->with('success', __('Testimonial Updated Successfully!') . ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
            } else {
                return redirect()->back()->with('error', __('Testimonial not found.'));
            }
        } else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(\Auth::user() && \Auth::user()->can('Delete Testimonial')){
            $testimonial = Testimonial::where('id', $id)->where('store_id', \Auth::user()->current_store)->first();
            if (isset($testimonial) && !empty($testimonial)){

                $fileName = $testimonial->image !== 'avatar.png' ? $testimonial->image : '' ;
                $filePath = 'uploads/testimonial_image/'. $fileName;

                Utility::changeStorageLimit(\Auth::user()->creatorId(), $filePath);

                $testimonial->delete();

                return redirect()->back()->with('success', __('Testimonial Deleted Successfully!'));
            } else {
                return redirect()->back()->with('error', __('Testimonial not found.'));
            }
        } else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
