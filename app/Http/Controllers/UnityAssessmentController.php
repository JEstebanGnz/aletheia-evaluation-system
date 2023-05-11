<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnityAssessment;
use App\Http\Requests\StoreUnityAssessmentRequest;
use App\Http\Requests\UpdateUnityAssessmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;
use Psy\Util\Json;

class UnityAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {

        return response()->json(UnityAssessment::getAllAssignments());


    }


    public function getUnitAssignments(Request $request): JsonResponse
    {
        $teachers = $request->all();

        $unitTeachersId = [];

        foreach ($teachers as $teacher)
        {

            $unitTeachersId [] = $teacher['id'];

        }

        return response()->json(UnityAssessment::getUnitAssignments($unitTeachersId));


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
     * @param  \App\Http\Requests\StoreUnityAssessmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $beingAssignedUserId = $request->input('beingAssignedUserId');
        $assignedToUserId = $request->input('assignedToUserId');
        $role = $request->input('role');


        try {

            UnityAssessment::assignRolesToTeacher($beingAssignedUserId, $assignedToUserId, $role);

        }

        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Asignación guardada correctamente']);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UnityAssessment  $unityAssessment
     * @return \Illuminate\Http\Response
     */
    public function show(UnityAssessment $unityAssessment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UnityAssessment  $unityAssessment
     * @return \Illuminate\Http\Response
     */
    public function edit(UnityAssessment $unityAssessment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUnityAssessmentRequest  $request
     * @param  \App\Models\UnityAssessment  $unityAssessment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUnityAssessmentRequest $request, UnityAssessment $unityAssessment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UnityAssessment  $unityAssessment
     * @return \Illuminate\Http\Response
     */
    public function destroy(UnityAssessment $unityAssessment)
    {
        //
    }

    public function removeAssignment(Request $request): JsonResponse
    {

        $beingAssignedUserId = $request->input('beingAssignedUserId');
        $assignedToUserId = $request->input('assignedToUserId');
        $role = $request->input('role');

        UnityAssessment::removeAssignment($beingAssignedUserId,$assignedToUserId, $role );

        return response()->json(['message' => 'Asignación eliminada exitosamente']);


    }

}
