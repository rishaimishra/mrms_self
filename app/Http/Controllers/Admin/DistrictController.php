<?php

namespace App\Http\Controllers\Admin;

use App\Grids\AdminUsersGrid;
use App\Grids\DistrictsGrid;
use App\Models\AdminUser;
use App\Models\District;
use App\Models\Property;
use App\Models\User;
use App\Rules\Name;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
         $ids= User::get()->pluck('assign_district_id')->toArray();
        $query = District::query();
        if (request()->user()->hasRole('Super Admin')) {
            // $query = $query->whereIn('id',$ids);
            $query = $query;
        } else {
            $query = $query->whereIn('id', $ids);
        }
        //dd(District::where('id',13)->get());
        return (new DistrictsGrid())
            ->create(['query' => $query, 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.district.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $district = District::find($id);

        return view('admin.district.edit', compact('district'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $request->validate( [
            'name' => ['required', 'max:70'],
            'council_name' => ['required', 'max:150'],
            'council_short_name' => ['required', 'max:70'],
            'council_address' => ['required', 'max:70'],
            'penalties_note' => ['nullable', 'max:250'],
            'warning_note' => ['nullable', 'max:250'],
            'collection_point.*' => ['nullable'],
            'collection_point2.*' => ['nullable'],
            'bank_details.*' => ['nullable'],
            'enquiries_email' => ['required', 'email', 'max:70'],
            'enquiries_phone' => ['required', 'max:250'],
            'enquiries_phone2' => ['nullable', 'max:250'],
            'feedback' => ['nullable', 'max:500'],
            'primary_logo' => 'nullable|mimes:jpg,jpeg,png|max:4096',
            'secondary_logo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:4096'],
            'chif_administrator_sign' => ['nullable', 'mimes:jpg,jpeg,png', 'max:4096'],
            'ceo_sign' => ['nullable', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);
        $district = District::findOrFail($id);
        $district->council_name = $request->council_name;
        $district->council_short_name = $request->council_short_name;
        $district->council_address = $request->council_address;
        $district->penalties_note = $request->penalties_note;
        $district->warning_note = $request->warning_note;
        $district->collection_point = ($request->collection_point);
        $district->collection_point2 = ($request->collection_point2);
        $district->bank_details = ($request->bank_details);
        $district->enquiries_email = $request->enquiries_email;
        $district->enquiries_phone = $request->enquiries_phone;
        $district->enquiries_phone2 = $request->enquiries_phone2;
        $district->feedback = $request->feedback;
        $district->sq_meter_value = $request->sq_meter_value;

        if ($request->hasFile('primary_logo')) {
            $district->primary_logo = $request->primary_logo->store(District::IMAGE_PATH);
        }

        if ($request->hasFile('secondary_logo')) {
            $district->secondary_logo = $request->secondary_logo->store(District::IMAGE_PATH);
        }

        if ($request->hasFile('chif_administrator_sign')) {
            $district->chif_administrator_sign = $request->chif_administrator_sign->store(District::IMAGE_PATH);
        }

        if ($request->hasFile('ceo_sign')) {
            $district->ceo_sign = $request->ceo_sign->store(District::IMAGE_PATH);
        }

        $district->save();

        return redirect()->back()->with('success', 'District successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (request()->user()->hasRole('Super Admin')) {
            $district = District::findOrFail($request->district);
            $district->delete();
            return Redirect()->route('admin.districts.index')->with('success', 'District Deleted Successfully !');
        } else {
            return Redirect()->route('admin.districts.index')->with('error', "You can't delete district. ");
        }
    }
}
