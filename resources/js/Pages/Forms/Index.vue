<template>
    <AuthenticatedLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container>
            <div class="d-flex flex-column align-end mb-5">
                <h2 class="align-self-start">Gestionar formularios</h2>
                <div class="d-flex justify-end mt-5">
                    <v-bottom-sheet v-model="sheet">
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                class="mr-3"
                                color="red"
                                dark
                                v-bind="attrs"
                                v-on="on"
                            >
                                Otras opciones
                            </v-btn>
                        </template>
                        <v-list>
                            <v-subheader>Menú de otras opciones</v-subheader>
                            <v-list-item
                                @click="openMigrateFormsDialog"
                            >
                                <v-list-item-avatar>

                                    <v-icon>
                                        mdi-rotate-right
                                    </v-icon>

                                </v-list-item-avatar>
                                <v-list-item-title>Migrar formularios de periodos anteriores</v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                @click="getFormsWithoutQuestions"
                            >
                                <v-list-item-avatar>
                                    <v-avatar
                                        size="32px"
                                        tile
                                    >
                                        <v-icon>
                                            mdi-file-question
                                        </v-icon>
                                    </v-avatar>
                                </v-list-item-avatar>
                                <v-list-item-title>Ver formularios sin preguntas</v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                @click="getAllForms(true)"
                            >
                                <v-list-item-avatar>
                                    <v-avatar
                                        size="32px"
                                        tile
                                    >
                                        <v-icon>
                                            mdi-file-document-outline
                                        </v-icon>
                                    </v-avatar>
                                </v-list-item-avatar>
                                <v-list-item-title>Ver todos los formularios</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-bottom-sheet>
                    <v-btn
                        class="mr-3"
                        @click="openFormDialog('create','othersForm')"
                    >
                        Crear formulario para otros
                    </v-btn>
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="openFormDialog('create','studentForm')"
                    >
                        Crear formulario para estudiantes
                    </v-btn>
                </div>
            </div>

            <!--Inicia tabla-->
            <h3 class="mb-5">Formularios para estudiantes</h3>
            <v-data-table
                loading-text="Cargando, por favor espere..."
                :loading="isLoading"
                :headers="studentTableHeaders"
                :items="studentsForms"
                :items-per-page="20"
                :footer-props="{
                        'items-per-page-options': [20,50,100,-1]
                    }"
                class="elevation-1"
            >
                <template v-slot:item="{ item }">
                    <tr>
                        <td>
                            {{ item.name }}
                        </td>
                        <td>
                            {{ item.degree ? item.degree : 'Todos' }}
                        </td>
                        <td>
                            {{ getTableAcademicPeriod(item.academicPeriodId) }}
                        </td>
                        <td>
                            <span>
                                {{ truncateDescription(getTableServiceAreas(item.serviceAreas))}}
                            </span>
                        </td>
                        <td>
                            {{ item.description === '' ? 'No proporcionada' : truncateDescription(item.description) }}
                        </td>

                        <td class="d-flex" style="gap: 5px">
                            <v-tooltip top>
                                <template v-slot:activator="{on,attrs}">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        class="mr-2 primario--text"
                                        @click="openFormDialog('edit','studentForm',item)"
                                    >
                                        mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Editar formulario</span>
                            </v-tooltip>

                            <v-tooltip top>
                                <template v-slot:activator="{on,attrs}">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        class="primario--text"
                                        @click="copy(item.id)"
                                    >
                                        mdi-content-copy
                                    </v-icon>
                                </template>
                                <span>Copiar formulario</span>
                            </v-tooltip>

                            <v-tooltip top>
                                <template v-slot:activator="{on,attrs}">

                                    <InertiaLink as="v-icon" class="primario--text"
                                                 v-bind="attrs"
                                                 v-on="on"
                                                 :href="route('forms.show.view',{form:item.id})">
                                        mdi-format-list-bulleted
                                    </InertiaLink>
                                </template>
                                <span>Editar preguntas</span>

                            </v-tooltip>
                            <v-tooltip top>
                                <template v-slot:activator="{on,attrs}">

                                    <InertiaLink
                                        v-bind="attrs"
                                        v-on="on" as="v-icon" class="primario--text"
                                        :href="route('tests.preview',{testId:item.id})">
                                        mdi-check
                                    </InertiaLink>
                                </template>
                                <span>Visualizar formulario</span>
                            </v-tooltip>


                            <v-tooltip top>
                                <template v-slot:activator="{on,attrs}">

                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        class="primario--text"
                                        @click="confirmDeleteForm(item)"
                                    >
                                        mdi-delete
                                    </v-icon>
                                </template>
                                <span>Borrar formulario</span>
                            </v-tooltip>
                        </td>
                    </tr>
                </template>
            </v-data-table>
            <h3 class="mt-10 mb-5">Formularios para otros roles (par, jefe, autoevaluación)</h3>
            <v-data-table
                loading-text="Cargando, por favor espere..."
                :loading="isLoading"
                :headers="othersTableHeaders"
                :items="othersForms"
                :items-per-page="20"
                :footer-props="{
                        'items-per-page-options': [20,50,100,-1]
                    }"
                class="elevation-1"
            >
                <template v-slot:item="{ item }">
                    <tr>
                        <td>
                            {{ item.name }}
                        </td>
                        <td>
                            {{ getTableAssessmentPeriod(item.assessmentPeriodId).name }}
                        </td>
                        <td>
                            {{ item.unitRole != null ? item.unitRole : 'Todos' }}
                        </td>
                        <td>
                            {{ item.teachingLadder != null ? item.teachingLadder : 'Todos' }}
                        </td>
                        <td>
                            {{ truncateDescription(getTableUnits(item.units))}}
                        </td>

                        <td>
                            {{ item.description === '' ? 'No proporcionada' : truncateDescription(item.description) }}
                        </td>

                        <td>
                            <v-icon
                                class="mr-2 primario--text"
                                @click="openFormDialog('edit','othersForm',item)"
                            >
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                class="primario--text"
                                @click="copy(item.id)"
                            >
                                mdi-content-copy
                            </v-icon>
                            <InertiaLink as="v-icon" class="primario--text"
                                         :href="route('forms.show.view',{form:item.id})">
                                mdi-format-list-bulleted
                            </InertiaLink>

                            <v-icon
                                class="primario--text"
                                @click="confirmDeleteForm(item)"
                            >
                                mdi-delete
                            </v-icon>
                        </td>
                    </tr>
                </template>
            </v-data-table>
            <!--Acaba tabla-->

            <!------------Seccion de dialogos ---------->

            <!--Crear o editar form -->
            <v-dialog
                v-model="createStudentFormDialog"
                persistent
                max-width="650px"
            >
                <v-card>
                    <v-card-title>
                        <span>
                        </span>
                        <span
                            class="text-h5">Crear un nuevo formulario para estudiantes</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre del formulario *"
                                        required
                                        v-model="studentForm.name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea
                                        label="Descripción del formulario *"
                                        required
                                        rows="3"
                                        v-model="studentForm.description"
                                    ></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="studentForm.degree"
                                        :items="degrees"
                                        label="Nivel de formación"
                                        :item-value="(degree)=> degree.value"
                                        :item-text="(degree)=> degree.name.charAt(0).toUpperCase() + degree.name.slice(1)"
                                    ></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="studentForm.academicPeriodId"
                                        :items="academicPeriods"
                                        label="Periodo académico"
                                        :item-text="(academicPeriod)=>academicPeriod.name"
                                        :item-value="(academicPeriod)=>academicPeriod.id"
                                    ></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        multiple
                                        v-model="studentForm.serviceAreas"
                                        :items="serviceAreas"
                                        label="Área de servicio"
                                        :item-text="(serviceArea)=>serviceArea.name"
                                        :item-value="(serviceArea)=>serviceArea.code"
                                    >
                                        <template v-slot:selection="{ item, index }">
                                            <v-chip v-if="index === 0">
                                                <span>{{ item.name }}</span>
                                            </v-chip>
                                            <span
                                                v-if="index === 1"
                                                class="grey--text text-caption"
                                            >
                                              (+{{ studentForm.serviceAreas.length - 1 }} otros)
                                            </span>
                                        </template>

                                    </v-select>
                                </v-col>
                            </v-row>
                        </v-container>
                        <small>Los campos con * son obligatorios</small>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primario"
                            text
                            @click="createStudentFormDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="createForm('studentForm')"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog
                v-model="createOthersFormDialog"
                persistent
                max-width="650px"
            >
                <v-card>
                    <v-card-title>
                        <span>
                        </span>
                        <span class="text-h5">Crear un nuevo formulario para otros</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre del formulario *"
                                        required
                                        v-model="othersForm.name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea
                                        label="Descripción del formulario *"
                                        required
                                        rows="3"
                                        v-model="othersForm.description"
                                    ></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="othersForm.assessmentPeriodId"
                                        :items="assessmentPeriods"
                                        label="Periodo de evaluación"
                                        :item-text="(assessmentPeriod)=>assessmentPeriod.name"
                                        :item-value="(assessmentPeriod)=>assessmentPeriod.id"
                                    ></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="othersForm.unitRole"
                                        :items="roles"
                                        label="Rol"
                                        :item-text="(role)=> role.name"
                                        :item-value="(role)=>role.value"
                                    ></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="othersForm.teachingLadder"
                                        :items="teachingLadders"
                                        label="Escalafón"
                                        :item-text="(teachingLadder)=> this.othersForm.unitRole === null ? 'Todos' :teachingLadder.name"
                                        :item-value="(teachingLadder)=> this.othersForm.unitRole === null ? null: teachingLadder.value"
                                    ></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="othersForm.units"
                                        :items="units"
                                        multiple
                                        label="Unidades"
                                        :item-text="(unit)=> unit.name"
                                        :item-value="(unit)=>unit.identifier"
                                    >
                                        <template v-slot:selection="{ item, index }">
                                            <v-chip v-if="index === 0">
                                                <span>{{ item.name }}</span>
                                            </v-chip>
                                            <span
                                                v-if="index === 1"
                                                class="grey--text text-caption"
                                            >
                                              (+{{ othersForm.units.length - 1 }} otros)
                                            </span>
                                        </template>
                                    </v-select>
                                </v-col>
                            </v-row>
                        </v-container>
                        <small>Los campos con * son obligatorios</small>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primario"
                            text
                            @click="createOthersFormDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="createForm('othersForm')"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog
                v-model="migrateFormsDialog"
                persistent
                max-width="700"
            >
                <v-card>
                    <v-card-title class="text-h5">
                        Migrar formularios anteriores
                    </v-card-title>
                    <v-card-text>Selecciona el periodo de evaluación del que quieres migrar los formularios
                    </v-card-text>
                    <v-select
                        color="primario"
                        v-model="selectedAssessmentPeriod"
                        :items="assessmentPeriodsMigrateList"
                        label="Selecciona un periodo de evaluación"
                        :item-value="(role)=>role"
                        :item-text="(role)=>role.name"
                        class="pa-6"
                    ></v-select>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primario"
                            text
                            @click="migrateForms(selectedAssessmentPeriod)"
                        >
                            Aceptar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="migrateFormsDialog = false"
                        >
                            Cancelar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!--Confirmar borrar rol-->
            <confirm-dialog
                :show="deleteFormDialog"
                @canceled-dialog="deleteFormDialog = false"
                @confirmed-dialog="deleteForm(deletedFormId)"
            >
                <template v-slot:title>
                    Estas a punto de eliminar el rol seleccionado
                </template>

                ¡Cuidado! esta acción es irreversible

                <template v-slot:confirm-button-text>
                    Borrar
                </template>
            </confirm-dialog>

        </v-container>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import {prepareErrorText, showSnackbar} from "@/HelperFunctions"
import ConfirmDialog from "@/Components/ConfirmDialog";
import Form from "@/models/Form";
import Snackbar from "@/Components/Snackbar";

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
            studentTableHeaders: [
                {text: 'Nombre', value: 'name'},
                {text: 'Nivel de formación', value: 'degree'},
                {text: 'Periodo académico', value: 'academic_period.name'},
                {text: 'Área de servicio', value: 'service_area.name'},
                {text: 'Descripción del formulario', value: 'description'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            othersTableHeaders: [
                {text: 'Nombre', value: 'name'},
                {text: 'Periodo de evaluación', value: 'assessment_period.name'},
                {text: 'Rol', value: 'unit_role'},
                {text: 'Escalafón', value: 'teaching_ladder'},
                {text: 'Unidades', value: 'unit.name'},
                {text: 'Descripción del formulario', value: 'description'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            forms: [],
            studentsForms: [],
            othersForms: [],
            //data for modals
            academicPeriods: [],
            assessmentPeriods: [],
            serviceAreas: [],
            units: [],
            teachingLadders: [],
            roles: [],
            degrees: [],

            //Forms models
            studentForm: new Form(),
            isServiceAreaDisabled: false,
            isAcademicPeriodDisabled: false,
            othersForm: new Form(),
            isRoleDisabled: false,
            isTeacherLadderDisabled: false,
            isUnitDisabled: false,
            formMethod: 'create',
            deletedFormId: 0,

            //Snackbars
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },
            //Dialogs
            deleteFormDialog: false,
            createStudentFormDialog: false,
            editStudentFormDialog: false,
            createOthersFormDialog: false,
            editOthersFormDialog: false,
            migrateFormsDialog: false,
            assessmentPeriodsMigrateList: [],
            selectedAssessmentPeriod: [],
            isLoading: true,

        }
    },
    async created() {
        await this.getAllForms();
        this.getCurrentAssessmentPeriodAcademicPeriods();
        this.getServiceAreas();
        this.getAssessmentPeriods();
        this.getUnits();
        this.getTeachingLadders();
        this.getRoles();
        this.getDegrees();

        this.isLoading = false;
    },

    methods: {

        truncateDescription(description) {
            const maxLength = 70; // Set the maximum length of characters
            if (!description) {
                return 'No proporcionada'; // Return default text if description is null or empty
            }

            return description.length > maxLength ? description.substring(0, maxLength) + '...' : description || 'No proporcionada';
        },

        getTableAcademicPeriod: function (academicPeriodId) {
            if (academicPeriodId === null) {
                return 'Todos';
            }
            const selectedAcademicPeriod = this.academicPeriods.find((academicPeriod) => academicPeriod.id === academicPeriodId);
            if (selectedAcademicPeriod === undefined) {
                return 'Error obteniendo periodo académico';
            }
            return selectedAcademicPeriod.name;
        },

        getFormsWithoutQuestions: async function () {
            let request = await axios.get(route('api.forms.withoutQuestions'));
            this.forms = Form.createFormsFromArray(request.data);
            this.formatForms();
            showSnackbar(this.snackbar, 'Se han cargado los formularios sin preguntas', 'success');
            this.sheet = false;
        },

        migrateForms: async function (assessmentPeriod) {
            let request = await axios.get(route('api.forms.copyFromPeriod', {assessmentPeriod}));
            await this.getAllForms();
            this.migrateFormsDialog = false;
            showSnackbar(this.snackbar, 'Se han cargado los formularios del periodo de evaluación seleccionado', 'success');
            this.sheet = false;
        },

        getTableServiceAreas: function (formServiceAreas) {
            if (!Array.isArray(formServiceAreas)) {
                return 'Ninguna';
            }

            const names = [];
            const serviceAreas = this.serviceAreas;

            for (const serviceArea of formServiceAreas) {
                names.push(serviceArea === null ? 'Todas' : (serviceAreas.find(pServiceArea => pServiceArea.code === serviceArea)?.name || ''));
            }
            return names.join(', ');
        },

        getTableAssessmentPeriod: function (assessmentPeriodId) {
            const selectedAssessmentPeriod = this.assessmentPeriods.find(pAssessmentPeriod => pAssessmentPeriod.id == assessmentPeriodId);
            return selectedAssessmentPeriod === undefined ? 'Error al tratar de obtener periodo de evaluación' : selectedAssessmentPeriod;
        },
        getTableUnits: function (formUnits) {

            if (!Array.isArray(formUnits)) {
                return 'Ninguna';
            }
            const names = [];
            const units = this.units;

            formUnits.forEach((unit) => {
                const selectedUnit = units.find(pUnit => pUnit.identifier == unit);
                if (!selectedUnit) {
                    return;
                }
                names.push(selectedUnit.name);
            })

            return names.join(', ');
        },

        setFormAsActive: async function (formId) {
            try {
                let request = await axios.post(route('api.forms.setActive', {'form': formId}));
                this.createOrEditDialog.dialogStatus = false;
                showSnackbar(this.snackbar, request.data.message, 'success');
                this.getAllForms();
            } catch (e) {
                showSnackbar(this.snackbar, prepareErrorText(e), 'alert');
            }
        },

        confirmDeleteForm: function (form) {
            this.deletedFormId = form.id;
            this.deleteFormDialog = true;
        },
        deleteForm: async function (formId) {
            try {
                let request = await axios.delete(route('api.forms.destroy', {form: formId}));
                this.deleteFormDialog = false;
                showSnackbar(this.snackbar, request.data.message, 'success');
                this.getAllForms();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'red', 3000);
            }

        },
        openFormDialog(method, model, form = null) {
            this.formMethod = method;
            if (method === 'edit') {
                this[model] = Form.copy(form);
            } else {
                this[model] = new Form();
            }

            if (model === 'studentForm') {
                this.createStudentFormDialog = true;
            }
            if (model === 'othersForm') {
                this.createOthersFormDialog = true;
            }
        },

        async openMigrateFormsDialog() {

            this.migrateFormsDialog = true
            let request = await axios.get(route('api.assessmentPeriods.index'));
            let assessmentPeriods = request.data;
            this.assessmentPeriodsMigrateList = assessmentPeriods.filter(assessmentPeriod => {
                return assessmentPeriod.active === 0;
            });
            console.log(this.assessmentPeriodsMigrateList);

        },

        getAllForms: async function (notify = false) {
            let request = await axios.get(route('api.forms.index'));
            this.forms = Form.createFormsFromArray(request.data);
            this.formatForms();
            if (notify) {
                showSnackbar(this.snackbar, 'Mostrando todos los formularios')
            }
        },
        getServiceAreas: async function () {
            let request = await axios.get(route('api.serviceAreas.index'));
            this.serviceAreas = request.data;
            this.serviceAreas.unshift({
                code: null,
                name: "Todas"
            });
        },
        copy: async function (formId) {
            try {
                await axios.post(route('api.forms.copy', {form: formId}));
                showSnackbar(this.snackbar, 'Formulario copiado exitosamente');
                await this.getAllForms();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'red', 3000);
            }

        },
        getUnits: async function () {
            let request = await axios.get(route('api.units.index'));
            console.log(request.data);
            this.units = request.data;
            this.units.unshift({
                identifier: null,
                name: "Todas"
            });
        },
        getRoles() {
            this.roles = Form.getPossibleRoles();
        },
        getTeachingLadders() {
            this.teachingLadders = Form.getPossibleTeachingLadders();
        },
        getDegrees() {
            this.degrees = Form.getPossibleDegrees();
        },

        getAssessmentPeriods: async function () {
            let request = await axios.get(route('api.assessmentPeriods.index'));
            this.assessmentPeriods = request.data;
            this.assessmentPeriods.unshift({
                id: null,
                name: "Todos"
            });
        },
        getCurrentAssessmentPeriodAcademicPeriods: async function () {
            let request = await axios.get(route('api.academicPeriods.index'), {
                params: {active: true}
            });
            this.academicPeriods = request.data;
            this.academicPeriods.unshift({
                id: null,
                name: "Todos"
            });
        },
        formatForms: function () {
            const forms = this.forms;
            this.studentsForms = [];
            this.othersForms = [];
            forms.forEach((form) => {
                if (form.type === 'estudiantes') {
                    //Decode the json string
                    this.studentsForms.push(form);
                } else {
                    this.othersForms.push(form);
                }
            });
        },
        createForm: async function (formModel) {
            if (this[formModel].hasEmptyProperties()) {
                showSnackbar(this.snackbar, 'Debes diligenciar todos los campos obligatorios', 'red', 2000);
                return;
            }
            if (formModel === 'studentForm') {
                this[formModel].type = 'estudiantes';
            }
            if (formModel === 'othersForm') {
                this[formModel].type = 'otros';
            }
            const endpoint = route('api.forms.store', {form: this[formModel].id});
            const axiosMethod = 'post';
            let data = this[formModel].toObjectRequest();
            console.log(data);
            try {
                let request = await axios[axiosMethod](endpoint, data);
                if (formModel === 'studentForm') {
                    this.createStudentFormDialog = false;
                }
                if (formModel === 'othersForm') {
                    this.createOthersFormDialog = false;
                }
                showSnackbar(this.snackbar, request.data.message, 'success', 2000);
                this.getAllForms();
                //Clear form information
                this[formModel] = new Form();

            } catch (e) {
                showSnackbar(this.snackbar, prepareErrorText(e), 'alert', 3000);
            }
        }
    },

    watch: {
        'studentForm.degree'(newDegree, oldAcademicPeriod) {
            if (newDegree === null) {
                this.studentForm.academicPeriodId = null;
                this.studentForm.serviceAreas = [null];
            }
        },
        'studentForm.academicPeriodId'(newAcademicPeriodId, oldAcademicPeriodId) {
            if (newAcademicPeriodId === null) {
                this.studentForm.serviceAreas = [null];
            }
        },

        'othersForm.assessmentPeriodId'(newAssessmentPeriodId, oldAssessmentPeriodId) {
            if (newAssessmentPeriodId === null) {
                this.othersForm.unitRole = null;
            }
        },

        'othersForm.unitRole'(newUnitRole, oldAcademicPeriod) {
            if (newUnitRole === null) {
                this.othersForm.teachingLadder = null;
            }
        },
        'othersForm.teachingLadder'(newTeachingLadder, oldAcademicPeriod) {
            if (newTeachingLadder === null) {
                this.othersForm.units = [null]
            }
        },
    },

}
</script>
