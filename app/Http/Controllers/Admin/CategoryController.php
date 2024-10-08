<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use CreateCategoryTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    //
    public function categories(Request $request){
        $category=Category::all();

        if($request->isMethod('POST')){

            $this->validate($request, [
                'name'=>'required',
                'description'=>'required',

            ]);
            $category =new Category();
            // dd($request->hasFile('cover_image'));
            if($request->hasFile('cover_image')){
                // Storage::disk('public')->delete('category/'.$category->cover_image);
                //get file name with ext
                $fileNameWithExt=$request->file('cover_image')->getClientOriginalName();
                //get just file name
                $fileName=pathinfo($fileNameWithExt,PATHINFO_FILENAME);
                //get just file extenstion
                $extension=$request->file('cover_image')->getClientOriginalExtension();
                //file name to store
                $fileNameToStore=$fileName.'_'.time().'.'.$extension;
                //upload cover_image
                $path=$request->file('cover_image')->storeAs('public/category',$fileNameToStore);

                $category->cover_image=$fileNameToStore;
                // $category->save();

               }
            $category->name=$request->input('name');
            $category->description=$request->input('description');

            $category->save();

           return redirect('category');
        }

        return view('category.allcategories',compact('category'));
    }
    public function create(){

        return view('category.addcategory');
    }

    public function edit(Request $request ,$category_id){
        if($request->isMethod('PUT')){
            $this->validate($request, [
                'name'=>'required',
                'description'=>'required',

            ]);
            $category =Category::find($category_id);
            if($request->hasFile('cover_image')){
                Storage::disk('public')->delete('category/'.$category->cover_image);
                //get file name with ext
                $fileNameWithExt=$request->file('cover_image')->getClientOriginalName();
                //get just file name
                $fileName=pathinfo($fileNameWithExt,PATHINFO_FILENAME);
                //get just file extenstion
                $extension=$request->file('cover_image')->getClientOriginalExtension();
                //file name to store
                $fileNameToStore=$fileName.'_'.time().'.'.$extension;
                //upload cover_image
                $path=$request->file('cover_image')->storeAs('public/category',$fileNameToStore);

                $category->cover_image=$fileNameToStore;

               }
            $category->name=$request->input('name');
            $category->description=$request->input('description');

            $category->update();

            notify()->success('category is Updated','Updated');

           return redirect('category');
        }

        $category=Category::find($category_id);

        return view('category.editcategory',compact('category'));


    }

    public function destory($id){
        $delete_category=Category::find($id);
        $delete_category->delete();
        return back();
    }

     //acitve categorys
     public function activecategory($id){
        $categorys=Category::find($id);
        $categorys->status=1;
        $categorys->update();

        notify()->success('category Status is Active','Active');
        return back();
    }
    //inactive categorys
    public function inactivecategory($id){

        $categorys=Category::find($id);
        $categorys->status=0;
        $categorys->update();

        notify()->error('category Status is Inactive','Inactive');
        return back();
    }

}