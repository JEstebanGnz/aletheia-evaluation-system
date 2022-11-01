<?php

namespace App\Http\Controllers;

use App\Http\Requests\CopyFormRequest;
use App\Http\Requests\DestroyFormRequest;
use App\Models\Form;
use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Form::with(['academicPeriod', 'assessmentPeriod', 'unit', 'serviceArea'])->get());
    }

    public function copy(CopyFormRequest $request, Form $form): JsonResponse
    {
        $newForm = $form->replicate(['name']);
        $newForm->name = 'Copia de ' . $form->name;
        $newForm->save();


        $newFormQuestion = $form->formQuestions->replicate(['form_id']);
        $newFormQuestion->form_id = $newForm->id;
        $newFormQuestion->save();


        return response()->json(['message' => 'Formulario copiado exitosamente']);
    }

    /**
     * Update or create a new form.
     *
     * @param UpdateFormRequest $request
     * @return JsonResponse
     */
    public function store(UpdateFormRequest $request): JsonResponse
    {
        if ($request->input('type') === 'estudiantes') {
            Form::createStudentForm($request);
        } elseif ($request->input('type') === 'otros') {
            Form::createOthersForm($request);
        }
        return response()->json(['message' => 'Formulario actualizado exitosamente']);

    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(DestroyFormRequest $request, Form $form): JsonResponse
    {
        if (count($form->formAnswers) !== 0) {
            return response()->json(['message' => 'No puedes borrar un formulario con respuestas']);
        }
        $form->delete();
        return response()->json(['message' => 'Formulario borrado exitosamente']);
    }
}
