<?php

namespace App\Http\Controllers;

use App\Models\ChildBirth;
use App\Models\Mothers;
use App\Repo\Interfaces\IChildBirth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use stdClass;

class ChildBirthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $childBirthRepo;
    public function __construct(IChildBirth $childBirthRepo)
    {
        $this->childBirthRepo = $childBirthRepo;
    }
    public function birthHistory(Request $request)
    {
        return $this->childBirthRepo->getBirthHistory($request);
    }

    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'mother_name' => 'required',
            'mother_nik' => 'required|integer|min_digits:16',
            'mother_age' => 'required|integer',
            'gestational_age' => 'required|numeric',
            'baby_gender' => 'required',
            'baby_weight' => 'required|numeric',
            'baby_length' => 'required|numeric',
            'birthing_method' => 'required',
            'birth_description' => 'required'

        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Please fill all form correctly'], 422);
        }
        return $this->childBirthRepo->saveChildAndMotherData($request);
    }

    public function update(Request $request, $id)
    {
        return $this->childBirthRepo->updateChildData($request, $id);
    }

    public function detail(Request $request, $id)
    {
        return $this->childBirthRepo->detailChildData($id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->childBirthRepo->destroyChildData($id);
    }

    public function report(Request $request)
    {
        return $this->childBirthRepo->getReportList();
    }
    public function annualReport(Request $request, $year)
    {
        return $this->childBirthRepo->getAnnualReport($year);
    }

    public function monthlyReport(Request $request, $year, $month)
    {
        return $this->childBirthRepo->getMonthlyReport($year, $month);
    }
};
