<!DOCTYPE html>
<html>
<head>
    <title>Reporte por docente ALETHEIA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"></head>
<body class="mx-5 my-5">

<img src="/images/whiteLogo.png"
     style="max-height: 300px; max-width:300px; object-fit: contain">


<p  style="margin-top: 30px"> Sistema de Información para evaluación docente: <strong> ALETHEIA </strong></p>

<p> Periodo de evaluación: <strong> {{$assessmentPeriodName}} </strong> </p>

<p> <strong> Reporte por áreas de servicio </strong> </p>

<p style="margin-bottom: 30px"> Visualizando desempeño por área(s) de servicio del docente: <strong> {{ucwords($teacherName)}}: </strong></p>

<table class="table" style="max-width: 85%; margin: auto" >
    <thead>
    <tr>
        <th scope="col">Rol</th>
        <th scope="col">Área de servicio</th>
        @foreach($labels as $label)
        <th scope="col">{{$label}}</th>
        @endforeach
        <th scope="col"> Estudiantes involucrados</th>
        <th scope="col"> Estudiantes totales</th>
    </tr>
    </thead>

    <tbody>

    @foreach($teacherResults as $teacherResult)
    <tr>
        <td style="text-transform: capitalize">Estudiante</td>
        <td>{{$teacherResult->service_area_name}}</td>
        <td>{{$teacherResult->first_competence_average}}</td>
        <td>{{$teacherResult->second_competence_average}}</td>
        <td>{{$teacherResult->third_competence_average}}</td>
        <td>{{$teacherResult->fourth_competence_average}}</td>
        <td>{{$teacherResult->fifth_competence_average}}</td>
        <td>{{$teacherResult->sixth_competence_average}}</td>
        <td>{{$teacherResult->aggregate_students_amount_reviewers}}</td>
        <td>{{$teacherResult->aggregate_students_amount_on_service_area}}</td>
    </tr>
    @endforeach
    </tbody>
</table>

<div style="margin-top: 20px" >
    <img src="https://quickchart.io/chart?c={{$chart}}" style="max-width: 80%; margin-left: 5%; margin-top: 5%" >
</div>


<h3> Descripción por grupos </h3>
<table class="table" style="max-width: 85%; margin-top: 50px" >
    <thead>
    <tr>
        <th scope="col">Asignatura</th>
        <th scope="col">Área de servicio</th>
        <th scope="col">Número de grupo</th>
        @foreach($labels as $label)
            <th scope="col">{{$label}}</th>
        @endforeach
        <th scope="col"> Actores involucrados</th>
        <th scope="col"> Actores totales</th>
    </tr>
    </thead>
    <tbody>
    @foreach($serviceAreasGroups as $serviceAreaGroups)
        @foreach($serviceAreaGroups as $group)
        <tr>
            <td style="text-transform: capitalize">{{$group->group_name}}</td>
            <td style="text-transform: capitalize">{{$group->name}}</td>
            <td style="text-transform: capitalize">{{$group->group_number}}</td>
            <td>{{$group->first_competence_average}}</td>
            <td>{{$group->second_competence_average}}</td>
            <td>{{$group->third_competence_average}}</td>
            <td>{{$group->fourth_competence_average}}</td>
            <td>{{$group->fifth_competence_average}}</td>
            <td>{{$group->sixth_competence_average}}</td>
            <td>{{$group->students_amount_reviewers}}</td>
            <td>{{$group->students_amount_on_group}}</td>
        </tr>
        @endforeach
    @endforeach
    </tbody>
</table>


@if(count($openAnswersFromStudents) > 0)
    <div style="margin-top: 30px">
        <h3> Comentarios de parte de Estudiantes</h3>
        @foreach($openAnswersFromStudents as $question)
            <h4 class="black--text pt-3"> Pregunta: </h4>
            <h5 style="font-weight: bold">{{$question->question_name}}</h5>
            <div style="margin-left: 20px">
                @foreach($question->service_areas as $service_area)
                    <p class="pt-3">Área de Servicio: <span style="font-weight: bold;" > {{$service_area->service_area_name}} </span></p>
                    <div style="margin-left: 30px">
                    @foreach($service_area->groups as $group)
                        <p><span style="font-weight: bold "> {{$group->group_name}} - Grupo {{$group->group_number}}</span></p>
                            <div style="margin-left: 40px">
                        @foreach($group->answers as $answer)
                            <p>- {{$answer}} </p>
                        @endforeach
                            </div>
                    @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endif




{{--
@if(count($openAnswersFromStudents) > 0)
    <div style="margin-top: 30px">
        <h3> Comentarios por parte de estudiantes</h3>
        @foreach($openAnswersFromStudents as $serviceArea)
            <h3 style="margin-top: 25px"> <strong> {{$serviceArea->service_area_name}}</strong></h3>
            @foreach($serviceArea->groups as $group)
                <p><span style="margin-left: 20px; font-weight: bold"> {{$group->group_name}} - Grupo {{$group->group_number}}</span></p>
                @foreach($group->answers as $groupAnswer)
                    <p> <span style=" margin-left: 40px; "> - {{$groupAnswer}}</span></p>
                @endforeach
            @endforeach
        @endforeach
    </div>
@endif
--}}

<h5 style="margin-top: 100px" > Reporte generado en: {{$timestamp}}</h5>
</body>


<script>
    window.addEventListener('load', function (){
        window.print();
    })
</script>

</html>
