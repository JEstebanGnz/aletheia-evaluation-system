<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeriod;
use App\Models\Reports;
use App\Models\ServiceArea;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportsController extends Controller
{

    public function index()
    {
        return Inertia::render('Reports/Index');
    }


    public function downloadPDF(Request $request){

        $assessmentPeriodName = AssessmentPeriod::select(['name'])->where('active', '=', 1)->first()->name;
        $chart = urlencode(json_encode($request->input('myChart')));
        $pdf = Pdf::loadView('report', compact( 'assessmentPeriodName', 'chart'));
        return view('report', compact( 'assessmentPeriodName', 'chart'));
    }


    public function showFaculty(Request $request){

        $user = auth()->user();

//Ingenierías
        //Meisel y William
       if($user->id == 240 || $user->id == 270){

            $units = ['025-1', '014-1','028-1', '018-1', '022-1', '105-1','104-1'];

        }

       //Luisa Gallo
        if($user->id == 159){

            $units = ['025-1', '014-1'];

        }

        //Carolina Saavedra
        if($user->id == 144){

            $units = ['018-1', '022-1'];

        }

        //Del Rio
        if($user->id == 31){

            $units = ['028-1'];

        }
//Ingenierías



//Facultad Ciencias Naturales
       //Nyckyertt
        if($user->id == 183){

            $units = ['083-1', '007-1'];

        }

        //Bilma
        if($user->id == 155){

            $units = ['083-1'];

        }
//Facultad Ciencias Naturales



//Facultad Ciencias Económicas

        //Paola Henoe
        if($user->id == 192){

            $units = ['103-1', '106-1', '021-1', '027-1', '013-1' ,'017-1', '092-1', '024-1'];
        }

        //Alejandro Gutiérrez

        if($user->id == 215){

            $units = ['092-1', '024-1', '013-1', '017-1', '103-1'];
        }

        //Paula Lorena Rodríguez

        if($user->id == 154){

            $units = ['021-1', '027-1', '106-1'];
        }

//Facultad Ciencias Económicas


//Facultad Derecho

        //Alexander Cruz
        if($user->id == 122957){

            $units = ['008-1', '012-1'];
        }


        //Cesar Barrera
        if($user->id == 98){

            $units = ['012-1'];
        }


//Facultad Derecho


//Facultad Humanidades

        //Daniel Lopera
        if($user->id == 218){

            $units = ['011-1', '110-1', '111-1'];
        }


        //Torrente
        if($user->id == 46){

            $units = ['110-1'];
        }


        //Tatiana Ávila
        if($user->id == 40){

            $units = ['111-1'];
        }

//Proyectos Especiales

        //Hernan Lopez-Garay
        if($user->id == 263077){

            $units = ['087-1'];
        }
//Proyectos Especiales

        return Inertia::render('Reports/FacultyOrProgramResults', ['propsUnits' => $units]);
    }

    public function showServiceArea(Request $request){

        $userId = auth()->user()->id;

        $serviceAreasArray = [];

        $serviceAreas = DB::table('service_area_user')->where('user_id', '=', $userId)->get();


        foreach ($serviceAreas as $serviceArea){

            $serviceAreasArray [] = $serviceArea->service_area_code;

        }

/*        dd($serviceAreasArray);*/

        return Inertia::render('Reports/ServiceAreaResults', ['propsServiceAreas' => $serviceAreasArray]);
    }


}
