<?php

namespace App\Http\Controllers;

use App\Models\ChildBirth;
use App\Models\Mothers;
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
    public function __construct()
    {
        //
    }
    public function birthHistory(Request $request)
    {
        $year = $request->query('year');
        $perPage = $request->query("per-page");
        $month = $request->query('month');

        if ($month == null) {
            $baseData = ChildBirth::select(['*'])->whereRaw('YEAR(created_at) = ' . intval($year))->orderBy('created_at', 'desc')->paginate($perPage);
        } else {
            $baseData = ChildBirth::select(['*'])->whereRaw('YEAR(created_at) = ' . intval($year))->whereRaw('MONTH(created_at) = ' . intval($month))->orderBy('created_at', 'desc')->paginate($perPage);
        }
        if (count($baseData) == 0) {
            return response()->json(['message' => 'Child Birth Record Not Found'], 404);
        }
        return response()->json(['message' => 'success', 'data' => $baseData], 200);
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
        $isRegisteredMother = Mothers::where('mother_nik', $request->input('mother_nik'))->get();
        $childData = [

            'mother_age' => $request->input('mother_age'),
            'gestational_age' => $request->input('gestational_age'),
            'baby_gender' => $request->input('baby_gender'),
            'baby_weight' => $request->input('baby_weight'),
            'baby_length' => $request->input('baby_length'),
            'birthing_method' => $request->input('birthing_method'),
            'birth_description' => $request->input('birth_description')
        ];
        if ($isRegisteredMother->isEmpty()) {
            $isRegisteredMother->mother_name = $request->input('mother_name');
            $isRegisteredMother->nik = $request->input('mother_nik');
            $savingMotherData = Mothers::create([
                'mother_nik' => $request->input('mother_nik'),
                'mother_name' => $request->input('mother_name')
            ]);

            $childBirth = ChildBirth::create([
                'mother_id' => $savingMotherData->id,
                ...$childData
            ]);
        } else {
            $totalBabyFromOneMotherADay = count(ChildBirth::where('mother_id', $isRegisteredMother[0]['id'])->whereDate('created_at', '=', Carbon::today()->toDateString())->get());
            if ($totalBabyFromOneMotherADay > 0) {
                $childBirth = ChildBirth::create([
                    'mother_id' => $isRegisteredMother[0]['id'],
                    ...$childData,
                    'created_at' => Carbon::now()->addMinutes($totalBabyFromOneMotherADay * 15),
                    'updated_at' => Carbon::now()->addMinutes($totalBabyFromOneMotherADay * 15)
                ]);
            }
            $childBirth = ChildBirth::create([
                'mother_id' => $isRegisteredMother[0]['id'],
                ...$childData
            ]);
        }

        return response()->json(['message' => 'success', 'data' => $childBirth], 200);
    }

    public function update(Request $request, $id)
    {
        $childBirth = ChildBirth::find($id);
        if (empty($childBirth)) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $childBirth->mother_age = $request->input('mother_age');
        $childBirth->gestational_age = $request->input('gestational_age');
        $childBirth->baby_gender = $request->input("baby_gender");
        $childBirth->baby_weight = $request->input("baby_weight");
        $childBirth->baby_length = $request->input("baby_length");
        $childBirth->birthing_method = $request->input("birthing_method");
        $childBirth->birth_description = $request->input('birth_description');
        $childBirth->save();

        return response()->json(['message' => "success", 'data' => $childBirth], 200);
    }

    public function detail(Request $request, $id)
    {
        $childBirth = ChildBirth::find($id);
        if (empty($childBirth)) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $motherName = Mothers::find($childBirth->mother_id);
        return response()->json(['message' => "success", 'mother_name' => $motherName->mother_name, 'data' => $childBirth], 200);
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

    public function report(Request $request)
    {
        $yearList = ChildBirth::selectRaw('YEAR(created_at) AS year')->distinct()->get();
        return response()->json(['message' => 'success', 'data' => $yearList]);
    }

    function detailReport($data)
    {
        $total_baby = count($data);
        function getAmount($data, $property, $comparison)
        {
            $result =  count($data->filter(function ($val) use ($comparison, $property) {
                switch ($property) {
                    case 'birth_description':
                        return $val->birth_description == $comparison;
                    case 'baby_gender':
                        return $val->baby_gender == $comparison;
                    case 'birthing_method':
                        return $val->birthing_method == $comparison;
                }
            }));
            return $result;
        }
        $healthyBaby = getAmount($data, 'birth_description', 'healthy');
        $disabledBaby = getAmount($data, 'birth_description', 'disabled');
        $diedBaby = getAmount($data, 'birth_description', 'died');
        $maleBaby = getAmount($data, 'baby_gender', 'male');
        $femaleBaby = getAmount($data, 'baby_gender', 'female');
        $waterBirthing = getAmount($data, 'birthing_method', 'water');
        $vaginalBirthing = getAmount($data, 'birthing_method', 'vaginal');
        $lotusBirthing = getAmount($data, 'birthing_method', 'lotus');
        $gentleBirthing = getAmount($data, 'birthing_method', 'gentle');
        $caesarBirthing = getAmount($data, 'birthing_method', 'caesar');
        $finalData = [
            'total_baby' => $total_baby,
            'birth_description' => ['healthy' => $healthyBaby, 'disabled' => $disabledBaby, 'died' => $diedBaby],
            'baby_gender' => ['male' => $maleBaby, 'female' => $femaleBaby],
            'birthing_method' => ['lotus' => $lotusBirthing, 'water' => $waterBirthing, 'vaginal' => $vaginalBirthing, 'gentle' => $gentleBirthing, 'caesar' => $caesarBirthing]
        ];
        return $finalData;
    }

    public function annualReport(Request $request, $year)
    {
        $baseData = ChildBirth::select(['*'])->whereRaw('YEAR(created_at) = ' . intval($year))->get();
        $motherAgeList = ChildBirth::select(['mother_age'])->whereRaw('YEAR(created_at) = ' . intval($year))->distinct()->get();
        $unTwinsBaby = $baseData->unique(function ($item) {
            return $item->mother_id . $item->mother_age . $item->created_at->format('m');
        });
        $averageGestationalAge = $unTwinsBaby->sum('gestational_age') / count($unTwinsBaby);
        $totalMother = count($unTwinsBaby);
        $detailReport = $this->detailReport($baseData);

        for ($i = 0; $i < count($motherAgeList); $i++) {
            $age = $motherAgeList[$i]['mother_age'];
            $totalBaby = count($baseData->filter(function ($val) use ($age) {
                return $val->mother_age == $age;
            }));
            $motherAgeList[$i]['total_baby'] = $totalBaby;
        }
        $finalReport = ['total_mother' => $totalMother, 'average_gestational_age' => $averageGestationalAge, ...$detailReport,  'maternal_age_group' => $motherAgeList];

        return response()->json(['message' => 'success', 'data' => $finalReport,]);
    }

    public function monthlyReport(Request $request, $year, $month)
    {
        $baseData = ChildBirth::select(['*'])->whereRaw('YEAR(created_at) = ' . intval($year))->whereRaw('MONTH(created_at) = ' . intval($month))->get();
        $motherAgeList = ChildBirth::select(['mother_age'])->whereRaw('YEAR(created_at) = ' . intval($year))->whereRaw('MONTH(created_at) = ' . intval($month))->distinct()->get();

        $unTwinsBaby = $baseData->unique(function ($item) {
            return $item->mother_id . $item->mother_age . $item->created_at->format('m');
        });
        $averageGestationalAge = $unTwinsBaby->sum('gestational_age') / count($unTwinsBaby);
        $totalMother = count($unTwinsBaby);
        $detailReport = $this->detailReport($baseData);

        for ($i = 0; $i < count($motherAgeList); $i++) {
            $age = $motherAgeList[$i]['mother_age'];
            $totalBaby = count($baseData->filter(function ($val) use ($age) {
                return $val->mother_age == $age;
            }));
            $motherAgeList[$i]['total_baby'] = $totalBaby;
        }
        $finalReport = ['total_mother' => $totalMother, 'average_gestational_age' => $averageGestationalAge, ...$detailReport,  'maternal_age_group' => $motherAgeList];

        return response()->json(['message' => 'success', 'data' => $finalReport]);
    }
};
