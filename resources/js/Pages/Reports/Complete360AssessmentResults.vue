<template>
    <AuthenticatedLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container fluid>
            <div class="d-flex flex-column align-end mb-2" id="malparido">
                <h2 class="align-self-start">Gestionar respuestas de evaluación 360</h2>
            </div>

            <v-toolbar
                    dark
                    color="primario"
                    class="mb-1"
                    height="auto"
                >
                    <v-row class="py-3">
                        <v-col cols="3" >
                            <v-autocomplete
                                v-model="assessmentPeriod"
                                flat
                                solo-inverted
                                hide-details
                                :items="assessmentPeriods"
                                :item-text="(pStatus)=> capitalize(pStatus.name)"
                                :item-value="(assessmentPeriod)=> (assessmentPeriod.id)"
                                prepend-inner-icon="mdi-home-search"
                                label="Periodo de evaluación"
                            ></v-autocomplete>
                        </v-col>

                        <v-col cols="3" >
                            <v-autocomplete
                                v-model="unit"
                                flat
                                solo-inverted
                                hide-details
                                :items="units"
                                :item-text="(pStatus)=> capitalize(pStatus.name)"
                                item-value="identifier"
                                prepend-inner-icon="mdi-home-search"
                                label="Unidades"
                            ></v-autocomplete>
                        </v-col>

                        <v-col cols="3">
                            <v-autocomplete
                                v-model="teacher"
                                flat
                                solo-inverted
                                hide-details
                                :items="filteredTeachers"
                                :item-text="(pStatus)=> capitalize(pStatus.name)"
                                item-value="id"
                                prepend-inner-icon="mdi-account-search"
                                label="Docente"
                            ></v-autocomplete>
                        </v-col>

                        <v-col cols="2">
                            <v-select
                                v-model="role"
                                flat
                                solo-inverted
                                hide-details
                                :items="roles"
                                :item-text="(pStatus)=> capitalize(pStatus.id)"
                                item-value="name"
                                prepend-inner-icon="mdi-eye-settings"
                                label="Rol"
                            ></v-select>
                        </v-col>

                        <v-col cols="1">
                            <v-tooltip top>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    v-on="on"
                                    v-bind="attrs"
                                    icon
                                    class="mr-2 secundario--text"
                                    @click="downloadResults()"
                                >
                                    <v-icon>
                                        mdi-file-excel
                                    </v-icon>
                                </v-btn>
                            </template>
                            <span>Exportar resultados actuales a Excel</span>
                            </v-tooltip>
                        </v-col>
                    </v-row>
            </v-toolbar>

            <!--Inicia tabla-->
            <v-card>
                <v-data-table
                    :search="search"
                    loading-text="Cargando, por favor espere..."
                    :no-data-text="noDataText"
                    :loading="isLoading"
                    :headers="headers"
                    :items="filteredItems"
                    :items-per-page="20"
                    :footer-props="{
                        'items-per-page-options': [20,50,100,-1]
                    }"
                    class="elevation-1"
                >
                    <template v-slot:item.unit_role="{ item }">
                        {{capitalize(item.unit_role)}}
                    </template>

                    <template v-slot:item.graph="{ item }">
                        <v-tooltip top
                        >
                            <template v-slot:activator="{on,attrs}">
                                <v-icon
                                    v-bind="attrs"
                                    v-on="on"
                                    class="mr-2 primario--text"
                                    @click="setDialogToShowChart(item)"
                                >
                                    mdi-chart-line
                                </v-icon>
                            </template>
                            <span>Graficar resultados</span>
                        </v-tooltip>

                    </template>

                    <template v-slot:item.op_answers="{ item }">
                        <v-tooltip top>
                            <template v-slot:activator="{on,attrs}">
                                <v-icon
                                    v-bind="attrs"
                                    v-on="on"
                                    class="mr-2 primario--text"
                                    @click="setDialogToShowOpenAnswers(item)"
                                >
                                    mdi-text-box
                                </v-icon>
                            </template>
                            <span>Visualizar Comentarios</span>
                        </v-tooltip>
                    </template>
                </v-data-table>
            </v-card>

            <!--Seccion de dialogos-->

            <v-dialog
                v-model="showChartDialog"
                persistent
            >
                <v-card>
                    <v-card-text>
                        <h2 class="black--text pt-5" style="text-align: center"> Visualizando al docente: {{this.capitalize(this.selectedTeacherToGraph)}}</h2>
                    </v-card-text>

                    <v-container style="position: relative; height:60vh; width:90vw; background: #FAF9F6" id="malparido">
                        <canvas id="MiGrafica"></canvas>
                    </v-container>

                    <h5 class="gray--text" style="text-align: left; margin-left: 60px; margin-bottom: 5px"> Puedes dar click izquierdo sobre la leyenda de la linea de tendencia que desees ocultar </h5>

                    <v-card-actions>
                       <v-btn
                            color="primario"
                            class="white--text"
                            @click="confirmSavePDF = true"
                            :disabled="!teacher"
                        >
                            Descargar reporte en PDF
                        </v-btn>

                        <v-btn
                            color="primario"
                            class="grey--text text--lighten-4"
                            @click="setDialogToCancelChart()"
                        >
                            Salir
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog
                v-model="showOpenAnswersDialog"
                persistent
            >
                <v-card>
                    <v-card-text v-if="openAnswersColleagues.length > 0 || openAnswersStudents.length > 0">
                            <h2 class="black--text pt-5" style="text-align: center"> Visualizando comentarios hacia el docente: {{ this.capitalize(this.selectedTeacherOpenAnswers) }}</h2>

                            <div v-if="openAnswersStudents.length > 0">
                                <div v-for="studentAnswer in openAnswersStudents" class="mt-3">
                                    <h3 class="black--text pt-5"> PREGUNTA:  {{studentAnswer.question_name}}</h3>
                                    <div v-for="group in studentAnswer.groups" class="mt-3">
                                        <h3 class="black--text pt-5"> {{group.group_name}} - Grupo {{group.group_number}}</h3>
                                        <div v-for="studentGroupAnswer in group.answers" class="mt-3">
                                            <h4>- {{studentGroupAnswer}}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="openAnswersColleagues.length > 0">
                                <h3 class="black--text pt-5"> Comentarios por parte de profesores:</h3>
                                <div v-for="question in openAnswersColleagues" class="mt-3">
                                    <h4 class="black--text pt-3"> Pregunta: </h4>
                                    <h4 style="font-weight: bold">{{question.question_name}}</h4>
                                    <div style="margin-left: 20px">
                                        <div v-for="person in question.answers" class="mt-3">
                                            <h4 class="black--text"> {{person.name}} - ({{person.unit_role}}): </h4>
                                            <div v-for="answer in person.answers" class="mt-3">
                                                <h4> {{answer}}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </v-card-text>

                    <v-card-text v-else>
                        <h2 class="black--text pt-5" style="text-align: center"> No hay comentarios disponibles para este docente</h2>
                    </v-card-text>

                    <v-card-actions>
                        <v-btn
                            color="primario"
                            class="grey--text text--lighten-4"
                            @click="setDialogToCancelOpenAnswers()"
                        >
                            Salir
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>


        <!--Confirmar guardar PDF-->
        <confirm-dialog
            :show="confirmSavePDF"
            @canceled-dialog="confirmSavePDF = false"
            @confirmed-dialog="savePDF()"
        >
            <template v-slot:title>
                Ahora serás redirigido a la pantalla para guardar el PDF
            </template>

            Una vez allí, lo único que debes hacer es darle click al botón de <strong class="black--text"> Guardar </strong> en la parte inferior derecha de tu pantal.la

            <template v-slot:confirm-button-text>
                Descargar PDF
            </template>
        </confirm-dialog>

    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import {prepareErrorText, showSnackbar} from "@/HelperFunctions"
import ConfirmDialog from "@/Components/ConfirmDialog";
import Form from "@/models/Form";
import Snackbar from "@/Components/Snackbar";
import Chart from "chart.js/auto";
import Question from "@/models/Question";
import Papa from 'papaparse';

export default {
    components: {
        ConfirmDialog,
        AuthenticatedLayout,
        InertiaLink,
        Snackbar,
    },
    data: () => {
        return {
            sheet: false,
            //Table info
            search: '',
            headers: [

                {text: 'Profesor', value: 'name'},
                {text: 'Rol', value: 'unit_role'},
                {text: 'Orientación a la calidad educativa', value: 'first_competence_average'},
                {text: 'Trabajo Colaborativo', value: 'second_competence_average'},
                {text: 'Empatía Universitaria', value: 'third_competence_average'},
                {text: 'Comunicación', value: 'fourth_competence_average'},
                {text: 'Innovación del conocimiento', value: 'fifth_competence_average'},
                {text: 'Productividad académica', value: 'sixth_competence_average'},
                {text: 'Actores involucrados', value: 'aggregate_students_amount_reviewers'},
                {text: 'Actores totales', value: 'aggregate_students_amount_on_360_groups'},
                {text: 'Fecha de envío', value: 'submitted_at'},
                {text: 'Graficar Resultados', value: 'graph', sortable: false},
                {text: 'Visualizar Comentarios', value: 'op_answers', sortable: false},
            ],

            //Display data
            deletedFormId: 0,
            assessments: [],
            assessmentPeriod: '',
            assessmentPeriods: [],
            unit: '',
            units:[],
            teacher: '',
            selectedTeacher: '',
            teachers:[],
            selectedTeacherToGraph: '',
            selectedTeacherOpenAnswers: '',
            role:'',
            roles: [],
            dataToGraph: [],
            chart:'',
            datasets:[],
            competencesValuesAsArray:[],
            finalTeachingLadders:[],
            responseIdeals: [],
            teachingLadder:[],
            responseIdealsCompetences: [],
            responseIdealsCompetencesArray: [],
            openAnswersStudents: [],
            openAnswersColleagues: [],
            confirmSavePDF: false,
            //Snackbars
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },
            //Dialogs
            showChartDialog: false,
            showOpenAnswersDialog: false,
            isLoading: true,
            individualView: true,
            noDataText: 'Para comenzar, selecciona un periodo de evaluación y la unidad que deseas visualizar'
        }
    },

    props: {
        propsUnits: Array,
        assessmentPeriodsArray: Array,
        token: String
    },

    async created() {
        await this.checkUser();
        await this.getAssessmentPeriods();
        await this.getRoles();
        await this.getUnits();
        await this.getTeachers();
        await this.getAnswersFromTeachers();
        await this.getAnswersFromStudents();
        await this.getFinalGrades();
        this.isLoading = false;
    },

    methods: {

        addAllElementSelectionItem(model, text) {
            model.unshift({id: '', name: text});
        },

        checkUser: function (){
            if (this.propsUnits === undefined){
                this.individualView = false;
            }
            console.log(this.propsUnits);
        },

        getAssessmentPeriods: async function () {
            let request = await axios.get(route('api.assessmentPeriods.index'));
            this.assessmentPeriods = request.data;
        },

        matchProperty: function (array, propertyPath, reference) {

            return array.filter((item) => {
                const propertyArr = propertyPath.split(".");
                const propertyValue = propertyArr.reduce((obj, key) => {

                    return obj && obj[key];
                }, item);

                return propertyValue === reference;

            });
        },

        getUnits: async function (){

            let request = await axios.get(route('api.units.index', {assessmentPeriodId: this.assessmentPeriod}));
            console.log(request.data, "units");
            this.units = this.sortArrayAlphabetically(request.data);
            this.units = this.units.filter(unit => {
                return unit.teachers_from_unit.length>0 || unit.is_custom == 1;
            })

            if (this.individualView){

                this.units = this.units.filter (unit => {
                    return this.propsUnits.includes(unit.identifier)
                })
                return this.units
            }

            this.units.unshift({name: 'Todas las unidades', identifier:''})

        },

        getTeachers: async function (){
            let request = await axios.get(route('units.teachers.assigned',{assessmentPeriodId: this.assessmentPeriod}));
            this.teachers = request.data
            this.teachers.forEach(teacher =>{
                teacher.name = this.capitalize(teacher.name)
            })
        },

        getAnswersFromTeachers: async function (){
            let url = route('formAnswers.teachers.show', {assessmentPeriodId: this.assessmentPeriod});
            let request = await axios.get(url);
            this.assessments = request.data
            console.log(request.data, 'ANSWERS FROM TEACHERS');
            this.assessments.forEach(assessment =>{
                assessment.first_competence_average = assessment.first_competence_average.toFixed(1);
                assessment.aggregate_students_amount_reviewers = 1;
                assessment.aggregate_students_amount_on_360_groups = 1;
            })
            console.log(this.assessments, 'assessments totales')
        },

        getAnswersFromStudents: async function () {

            let url = route('formAnswers.studentPerspective', {assessmentPeriodId: this.assessmentPeriod});
            let request = await axios.get(url);
            let answersFromStudents = request.data;
            console.log(answersFromStudents, 'answers from students');

           answersFromStudents.forEach(answer =>{
                answer.unit_role = 'estudiante'
                this.assessments.push(answer)
            });

           this.assessments.sort(this.orderData);
        },

        async getFinalGrades(){
            let url = route('formAnswers.finalGrades', {assessmentPeriodId: this.assessmentPeriod});
            let request = await axios.get(url);
            let finalGrades = request.data;
            console.log(finalGrades, 'promedio final');

            finalGrades.forEach(answer =>{
                answer.unit_role = 'promedio final'
                answer.aggregate_students_amount_reviewers = answer.involved_actors
                answer.aggregate_students_amount_on_360_groups = answer.total_actors
                this.assessments.push(answer)
            });
            this.assessments.sort(this.orderData);
        },

        getOpenAnswersFromStudents: async function (teacherId){
            let url = route('formAnswers.teachers.openAnswersStudents', {assessmentPeriodId: this.assessmentPeriod});
            let request = await axios.post(url, {teacherId: teacherId});
            this.openAnswersStudents = request.data;
            console.log(this.openAnswersStudents);
        },

        getOpenAnswersFromColleagues: async function (teacherId){
            let url = route('formAnswers.teachers.openAnswersColleagues', {assessmentPeriodId: this.assessmentPeriod});
            let request = await axios.post(url, {teacherId: teacherId});
            this.openAnswersColleagues = request.data;
            console.log(this.openAnswersColleagues, 'respuestas de colegas');
        },


        async setDialogToShowChart(teacher){
            this.showChartDialog = true
            this.selectedTeacher = teacher;
            await this.getResponseIdealsDataset(teacher);
            this.getRolesDatasets(teacher);
            this.getGraph();
            this.getChartAsObject()
        },

        async setDialogToShowOpenAnswers(teacher){
            this.selectedTeacherOpenAnswers = teacher.name;
            this.showOpenAnswersDialog = true
            if(teacher.unit_role === 'estudiante'){
                await this.getOpenAnswersFromStudents(teacher.teacherId)
            }
            else{
                await this.getOpenAnswersFromColleagues(teacher.teacherId);
            }
        },

        async savePDF(){
            this.confirmSavePDF = false;
            this.datasets.forEach(dataset =>{
                dataset.fill = {target: 'origin',
                    above: 'rgb(255, 255, 255)',
                    below: 'rgb(255, 255, 255)'}
            })
            console.log(this.selectedTeacher);
            var winName='MyWindow';
            var winURL= route('reports.assessment360');
            var windowOption='resizable=yes,height=600,width=800,location=0,menubar=0,scrollbars=1';
            var params = { _token: this.token,
                chart: JSON.stringify(this.getChartAsObject()),
                teacherResults: JSON.stringify(this.filteredItems),
                teacherId: this.selectedTeacher.teacherId,
                assessmentPeriodId: this.assessmentPeriod
            };

            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", winURL);
            form.setAttribute("target",winName);
            for (var i in params) {
                if (params.hasOwnProperty(i)) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = i;
                    input.value = params[i];
                    form.appendChild(input);
                }
            }
            document.body.appendChild(form);
            window.open('', winName, windowOption);
            form.target = winName;
            form.submit();
            document.body.removeChild(form);
        },

        getRoles (){
            this.roles = [{id:  'Todos los roles', name: ''},{id: 'jefe', name: 'jefe'},
                {id: 'par', name: 'par'}, {id: 'autoevaluación', name: 'autoevaluación'}, {id: 'estudiante', name: 'estudiante'}, {id: 'promedio final', name:'promedio final'}]
        },

        capitalize($field){
            return $field.toLowerCase().split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        },

        sortArrayAlphabetically(array){
            return array.sort( (p1, p2) =>
                (p1.name > p2.name) ? 1 : (p1.name > p2.name) ? -1 : 0);
        },

        getFilteredAssessmentsByUnit(assessments = null) {

            if (assessments === null) {
                assessments = this.assessments;
            }
            return assessments.filter((assessment) => {

                let doesAssessmentHaveUnit = false;
                if (assessment.unit_identifier === this.unit) {
                    doesAssessmentHaveUnit = true;
                }
                return doesAssessmentHaveUnit;
            });
        },

        getFilteredAssessmentsByTeacher(assessments = null) {
            if (assessments === null) {
                assessments = this.assessments;
            }
            return this.matchProperty(assessments, 'teacherId', this.teacher)
        },

        getFilteredAssessmentsByRole(assessments = null) {
            if (assessments === null) {
                assessments = this.assessments;
            }
            return this.matchProperty(assessments, 'unit_role', this.role)
        },

        fillCompetencesArray(roleArray) {
            let array = [roleArray.first_competence_average, roleArray.second_competence_average, roleArray.third_competence_average, roleArray.fourth_competence_average,
                roleArray.fifth_competence_average, roleArray.sixth_competence_average]
            return array;
        },

        getTeachingLadderNameByParameter: async function (teachingLadderCode){

            let request = await axios.get(route('api.assessmentPeriods.teachingLadders'));
            let teachingLadders = request.data
            teachingLadders.forEach(teachingLadder =>{

                if(teachingLadder == 'NIN'){

                    this.finalTeachingLadders.unshift({name : 'Ninguno',
                        identifier:teachingLadder})
                }

                if(teachingLadder == 'AUX'){

                    this.finalTeachingLadders.unshift({name : 'Auxiliar',
                        identifier:teachingLadder})
                }

                if(teachingLadder == 'ASI'){
                    this.finalTeachingLadders.unshift({name : 'Asistente',
                        identifier:teachingLadder})
                }

                if(teachingLadder == 'ASO'){
                    this.finalTeachingLadders.unshift({name : 'Asociado',
                        identifier:teachingLadder})
                }

                if(teachingLadder == 'TIT'){
                    this.finalTeachingLadders.unshift({name : 'Titular',
                        identifier:teachingLadder})
                }

            })

            let teachingLadder = this.finalTeachingLadders.find(teachingLadder =>{
                return teachingLadder.identifier == teachingLadderCode
            })

            if (teachingLadder === undefined){
                return 'Ninguno'
            }
            return teachingLadder.name

        },

        getPossibleInitialCompetences() {
            return new Question().getPossibleCompetencesAsArrayOfObjects();
        },

        async getResponseIdealsCompetences(teachingLadder, unitIdentifier){

            let url = route('teacher.responseIdeals.get');
            let request = await axios.post(url, {teachingLadder: teachingLadder, unitIdentifier: unitIdentifier, assessmentPeriodId: this.assessmentPeriod})

            console.log(request.data, 'RESPONSE IDEALS DATASET')

            if(request.data.length === 0){
                return this.getPossibleInitialCompetences();
            }
            return request.data;
        },

        async getResponseIdealsDataset(teacher){

            console.log(teacher, 'teacher');
            this.selectedTeacherToGraph = teacher.name
            let unitIdentifier = teacher.unit_identifier
            let teacherId = teacher.teacherId;
            let info = {userId : teacherId}
            let request = await axios.post(route('teachers.getTeachingLadder'), info)
            let teachingLadder= await this.getTeachingLadderNameByParameter(request.data)

            console.log(request.data, 'RESPONSE IDEALS DATASET')
            let responseIdealsCompetences = await this.getResponseIdealsCompetences(teachingLadder, unitIdentifier);

            responseIdealsCompetences.forEach(competence =>{
                this.responseIdealsCompetencesArray.push(competence.value);
            })

            this.datasets.unshift({
                label: `Nivel Esperado (${teachingLadder == 'Ninguno' ? 'Auxiliar' : teachingLadder})`,
                data: this.responseIdealsCompetencesArray,
                backgroundColor: 'orange',
                borderColor: 'orange',
                borderWidth: 2,
                borderDash: [5, 5],
            })
        },

        getRolesDatasets(teacher){

            let teacherRolesArrays = this.filteredItems.filter((item) => {
                return item.name == teacher.name
            })

            let colors = new Question().getLineChartColors();

            teacherRolesArrays.forEach(roleArray => {

                if(roleArray.unit_role == 'promedio final')
                {
                    this.datasets.push({

                        label: this.capitalize(roleArray.unit_role),
                        data: this.fillCompetencesArray(roleArray),
                        backgroundColor: 'black',
                        borderColor: 'black',
                        borderWidth: 5
                    })
                }

                else{

                    colors.forEach(color => {
                        if(color.role === roleArray.unit_role){
                            this.datasets.push({

                                label: this.capitalize(roleArray.unit_role),
                                data: this.fillCompetencesArray(roleArray),
                                backgroundColor: color.color,
                                borderColor: color.color,
                                borderWidth: 2
                            })
                        }
                    })
                }
            })
        },

        orderData(a,b){
            if ( a.name < b.name ){
                return -1;
            }
            if ( a.name > b.name ){
                return 1;
            }
            return 0;

        },

        setDialogToCancelChart (){
            this.showChartDialog = false
            this.chart.destroy();
            this.responseIdealsCompetencesArray.length = 0;
            this.finalTeachingLadders.length= 0;
            this.datasets = [];
        },

        setDialogToCancelOpenAnswers (){
            this.showOpenAnswersDialog = false;
            this.openAnswersColleagues = [];
            this.openAnswersStudents= [];
        },

        downloadResults (){
                console.log(this.filteredItems);
                let excelInfo = this.filteredItems.map(item =>{

                    return {
                        Nombre :item.name,
                        rol: item.unit_role,
                        NombreUnidad: item.unitName,
                        PromedioC1: item.first_competence_average,
                        PromedioC2: item.second_competence_average,
                        PromedioC3: item.third_competence_average,
                        PromedioC4: item.fourth_competence_average,
                        PromedioC5: item.fifth_competence_average,
                        PromedioC6: item.sixth_competence_average,
                        ActoresInvolucrados: item.aggregate_students_amount_reviewers,
                        ActoresTotales: item.aggregate_students_amount_on_360_groups
                    }
                })

                let csv = Papa.unparse(excelInfo, {delimiter:';'});
                var csvData = new Blob(["\uFEFF"+csv], {type: 'text/csv;charset=utf-8;'});
                var csvURL =  null;
                if (navigator.msSaveBlob)
                {
                    csvURL = navigator.msSaveBlob(csvData, 'ResultadosEvaluaciónDocente360.csv');
                }
                else
                {
                    csvURL = window.URL.createObjectURL(csvData);
                }

                var tempLink = document.createElement('a');
                tempLink.href = csvURL;
                tempLink.setAttribute('download', 'ResultadosEvaluaciónDocente360.csv');
                tempLink.click();

        },

        getGraph(){

         const legendMargin ={
                       beforeInit: function(chart, options) {
                           chart.legend.afterFit = function() {
                               this.height += 10; // must use `function` and not => because of `this`
                           };
                       }
               }

                let miCanvas = document.getElementById("MiGrafica").getContext("2d");
                this.chart = new Chart(miCanvas, {
                    type:"line",
                    data:{
                        labels: ["Orientación a la calidad educativa", "Trabajo Colaborativo",
                            "Empatía Universitaria", "Comunicación", "Innovación del conocimiento","Productividad académica"],
                        datasets: this.datasets
                    },
                    options: {
                        plugins: {
                            filler: {
                                propagate: false
                            },
                        },
                        layout:{
                            padding:{
                                left: 0,
                                right: 0,
                                top: 0,
                                bottom: 0
                            }
                        },
                        legend: {
                            display: true,
                         /*   labels:{
                                padding:20
                            },*/
                           position: "bottom"
                        },
                        responsive: true,
                        tooltips: {
                            mode: "index",
                            intersect: false
                        },
                        hover: {
                            mode: "nearest",
                            intersect: false
                        },

                        scales: {
                            x:
                                {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Competencias',
                                        color: 'black',
                                        font: {
                                            size: 15,
                                            weight: 'bold',
                                            lineHeight: 1.2,
                                        },
                                    },
                                    position: 'top'
                                }
                            ,
                            y:
                                {
                                    min: 0,
                                    max: 5.4,
                                    display: true,

                                    title: {
                                        display: true,
                                        text: 'Valores obtenidos',
                                        color: 'black',
                                        font: {
                                            size: 15,
                                            weight: 'bold',
                                            lineHeight: 1.2,
                                        },


                                    },

                                    ticks:{
                                        callback: (value, index, values) => (index == (values.length-1)) ? undefined : value,
                                    },
                                }
                        }
                    },
                })
        },

        getChartAsObject(){
            return this.chart.config._config;
        },

    },

    computed: {

        filteredItems() {

            let finalAssessments = this.assessments;

            if (this.individualView){
                finalAssessments = this.getFilteredAssessmentsByUnit(finalAssessments);
            }

            else {
                if (this.unit !== '') {
                    finalAssessments = this.getFilteredAssessmentsByUnit(finalAssessments);
                }
            }

            if (this.teacher !== '') {
                finalAssessments = this.getFilteredAssessmentsByTeacher(finalAssessments);
            }
            if (this.role !== '') {
                finalAssessments = this.getFilteredAssessmentsByRole(finalAssessments);
            }

            return finalAssessments;
        },


        filteredTeachers(){

            let finalTeachers = this.teachers;
            let finalAssessments = this.assessments;

           if (this.unit !== '') {
                finalAssessments = this.getFilteredAssessmentsByUnit();
                finalTeachers = finalTeachers.filter((teacher) => {
                    return finalAssessments.some((assessment) => assessment.teacherId == teacher.id)
                });
            }

            this.addAllElementSelectionItem(finalTeachers, 'Todos los docentes');
            return finalTeachers;
        }
    },

    watch:{

        async assessmentPeriod(){
            await this.getUnits();
            await this.getTeachers();
            await this.getAnswersFromTeachers();
            await this.getAnswersFromStudents();
            await this.getFinalGrades();
        }
    }
}
</script>
