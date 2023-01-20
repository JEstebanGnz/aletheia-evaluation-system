<?php

namespace Database\Seeders;

use App\Models\AssessmentPeriod;
use App\Models\Form;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = '[{"name":"¿Cumple los objetivos y metas del curso?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C1"},{"name":"¿Realiza sus clases controlando los tiempos, procesos y recursos?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C1"},{"name":"¿Orienta las clases para responder a retos o desafíos actuales de la región y el mundo?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C1"},{"name":"¿Facilita la apropiación de lo que se aprende a través de la práctica y la reflexión constante?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C1"},{"name":"¿Motiva la curiosidad permanente y la proactividad académica?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C1"},{"name":"¿Crea condiciones que favorecen el trabajo con otras personas?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C2"},{"name":"¿Brinda apoyo ante las obstáculos que surgen y se necesitan superar?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C2"},{"name":"¿Reconoce e integra las habilidades personales en actividades de grupo ?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C2"},{"name":"¿Facilita el aprendizaje a través de una relación de confianza mutua?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C2"},{"name":"¿Moviliza a los estudiantes hacia un compromiso solidario con el aprendizaje de todos?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C2"},{"name":"¿Orienta sus acciones desde el acuerdo pedagógico de clase?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C3"},{"name":"¿Tiene un trato respetuoso y cordial con los estudiantes?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C3"},{"name":"¿Se relaciona con los estudiantes influyendo de manera positiva?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C3"},{"name":"¿Toma decisiones según la situación y la perspectiva del grupo?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C3"},{"name":"¿Favorece el desarrollo profesional y personal de sus estudiantes?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C3"},{"name":"¿Escucha atentamente a los estudiantes y se asegura de comprender lo que quieren expresar?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C4"},{"name":"¿Explica de forma clara y precisa las instrucciones y los temas de la clase?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C4"},{"name":"¿Minimiza barreras o distorsiones que afectan la circulación de información?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C4"},{"name":"¿Retroalimenta de manera oportuna para mejorar el desempeño personal y de grupo?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C4"},{"name":"¿Coordina el intercambio de información útil y de manera asertiva?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C4"},{"name":"¿Adapta su plan de clase a los desafíos o retos de actualidad de la profesión ?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C5"},{"name":"¿Utiliza metodologías novedosas y dinámicas para enseñar?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C5"},{"name":"¿Prueba con frecuencia distintas  formas de solucionar problemas o de llevar a cabo la clase?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C5"},{"name":"¿Nos motiva a crear conocimiento a través de la sistematización de los ejercicios de clase?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C5"},{"name":"¿Tiene el hábito de elaborar y compartir materiales innovadores para el desarrollo de las actividades de clase?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C5"},{"name":"¿Actualiza los temas y actividades para enriquecer las clases?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C6"},{"name":"¿Establece metas de aprendizaje que son desafiantes y alcanzables?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C6"},{"name":"¿Es ágil para enfrentar imprevistos demostrando apertura y flexibilidad?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C6"},{"name":"¿Realiza actividades que se salen de lo planeado para mejorar la dinámica de clase?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C6"},{"name":"¿Orienta su enseñanza al logro de competencias y desempeños profesionales?","options":[{"value":"1","placeholder":"No se evidencia el desempeño"},{"value":"2","placeholder":"Requiere una mejora significativa"},{"value":"3","placeholder":"Logra evidenciar el indicador aunque no del todo"},{"value":"4","placeholder":"Se cumple en la mayoria de las veces"},{"value":"5","placeholder":"Es evidente a un nivel de excelencia"}],"competence":"C6"}]';

        Form::create([
            'name' => 'Formulario general',
            'type' => 'estudiantes',
            'degree' => null,
            'academic_period_id' => null,
            'service_areas' => '[{"id": null, "name": "Todas"}]',
            'creation_assessment_period_id' => AssessmentPeriod::getActiveAssessmentPeriod()->id,
            'questions' => $json
        ]);

    }
}
