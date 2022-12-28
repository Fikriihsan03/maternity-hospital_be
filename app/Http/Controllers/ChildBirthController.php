<?php

namespace App\Http\Controllers;

use App\Models\ChildBirth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class ChildBirthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'mother_name' => 'required',
            'mother_age' => 'required',
            'gestational_age' => 'required|integer',
            'baby_gender' => 'required',
            'baby_weight' => 'required|integer',
            'baby_length' => 'required|integer',
            'birthing_method' => 'required',
            'birth_description' => 'required'

        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Please fill all form'], 422);
        }
        $childBirth = ChildBirth::create($request->all());
        return response()->json(['message' => 'success', 'data' => $childBirth]);
    }

    public function update(Request $request, $id)
    {
        $childBirth = ChildBirth::find($id);
        if (empty($childBirth)) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $childBirth->mother_name = $request->input('mother_name');
        $childBirth->mother_age = $request->input('mother_age');
        $childBirth->gestational_age = $request->input('gestational_age');
        $childBirth->baby_gender = $request->input("baby_gender");
        $childBirth->baby_weight = $request->input("baby_weight");
        $childBirth->baby_length = $request->input("baby_length");
        $childBirth->birth_description = $request->input('birth_description');
        $childBirth->save();

        return response()->json(['data' => $childBirth], 200);
    }
    public function destroy(Request $request, $id)
    {
        $childBirth = ChildBirth::find($id);
        if (empty($childBirth)) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $childBirth->delete();
        return response()->json(["message" => "removed successfully"], 200);
    }
};
