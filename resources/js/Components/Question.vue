<template>
    <v-card
        class="align-self-end mt-3 mb-15"
        outlined
        rounded="lg"
        elevation="2"
    >
        <v-card-text>
            <v-row>
                <v-col cols="6">
                    <v-select
                        color="primario"
                        v-model="type"
                        :items="questionModel.getPossibleTypes()"
                        :value="(questionType)=>questionType.value"
                        :item-text="(questionType)=>questionType.placeholder"
                        label="Selecciona el tipo de pregunta"
                        @change="notifyParent"
                    ></v-select>
                </v-col>
                <v-col cols="6">
                    <v-select v-if="type !== 'abierta'"
                              color="primario"
                              v-model="competence"
                              :items="competences"
                              :item-value="(competence) => ({ id: competence.id, name: competence.name })"
                              :item-text="(competence) => competence.name"
                              label="Selecciona una competencia"
                              @change="onCompetenceChange"
                    ></v-select>
                </v-col>
                <v-col cols="6" v-if="type !== 'abierta' && competence">
                    <v-select
                        color="primario"
                        v-model="selectedAttribute"
                        :items="competenceAttributes"
                        item-text="name"
                        item-value="name"
                        label="Selecciona un atributo de la competencia"
                        @change="notifyParent"
                    ></v-select>
                </v-col>
                <v-col cols="12">
                    <v-text-field
                        label="Pregunta"
                        required
                        v-model="name"
                        @change="notifyParent"
                    ></v-text-field>
                </v-col>
            </v-row>
            <!--Question options-->
            <div v-if="type  === 'abierta'">
                <OpenQuestionOption
                    v-for="(optionLabel, optionKey) in ['Positivo', 'Constructivo']"
                    :key="optionLabel"
                    :initialPlaceholder="optionLabel"
                    :index="optionKey"
                    @deleted="removeQuestionOption"
                    @questionOptionUpdated="updateQuestionOption"
                />
            </div>

            <!--Question options-->
            <div v-if="type  !== 'abierta'">
                <QuestionOption v-for="(option, optionKey) in options" :key="option.placeholder"
                                :initialValue="option.value" :initialPlaceholder="option.placeholder"
                                :index="optionKey"
                                @deleted="removeQuestionOption"
                                @questionOptionUpdated="updateQuestionOption"
                />
            </div>

        </v-card-text>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn v-if="question.type  !== 'abierta'" large icon v-bind="attrs" v-on="on" @click="addAnotherOption()">
                        <v-icon>
                            mdi-plus
                        </v-icon>
                    </v-btn>
                </template>
                <span>Añadir otra opción de respuesta</span>
            </v-tooltip>

            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn icon large v-bind="attrs" v-on="on"
                           @click="$emit('copyQuestion', {name,competence,type,options})">
                        <v-icon>
                            mdi-content-copy
                        </v-icon>
                    </v-btn>
                </template>
                <span>Copiar pregunta</span>
            </v-tooltip>
            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn icon large v-bind="attrs" v-on="on"
                           @click="$emit('deleteQuestion', baseIndex)">
                        <v-icon>
                            mdi-delete
                        </v-icon>
                    </v-btn>
                </template>
                <span>Borrar pregunta</span>
            </v-tooltip>
        </v-card-actions>
    </v-card>

</template>

<script>
import Question from "@/models/Question";
import QuestionOption from "./QuestionOption";
import OpenQuestionOption from "./OpenQuestionOption";

export default {
    name: "Question",
    components: {
        QuestionOption, OpenQuestionOption
    },
    props: {
        question: Object,
        baseIndex: Number,
    },
    data() {
        return {
            competences: [],
            selectedAttribute:'',
            questionModel: new Question(),
            finalOptions: {},
            deleteQuestionDialog: false,
            name: '',
            competence: '',
            type: '',
            competenceAttributes: '',
            options: []
        }
    },

    watch: {
        type(newType) {
            if (newType === 'abierta') {
                this.options = [
                    { value: '', placeholder: 'Positivo' },
                    { value: '', placeholder: 'Constructivo' }
                ];
                this.competence = {id: 'null', name:'General', attribute: null};
            }
        }
    },

    async created() {

        await this.getCompetences();
        const question = JSON.parse(JSON.stringify(this.question));
        console.log(question,'LA PUTA PREGUNTA YA PARSEADA')

        this.name = question.name;
        this.competence = question.competence;
        this.type = question.type;
        this.options = question.options;
        this.finalOptions = question.options;

        if (this.type === 'abierta') {
            this.options = [
                {value: '', placeholder: 'Positivo'},
                {value: '', placeholder: 'Constructivo'}
            ];
            this.competence = {id: 'null', name:'General', attribute: null};
        }

        else if (this.competence) {
            this.updateCompetenceAttributes();
            console.log(this.competence.attribute, 'atributo que ya existe de la puta pregunta');
            this.selectedAttribute = this.competence.attribute;
        }

    },
    methods: {

        onCompetenceChange(newCompetence) {
            this.competence = newCompetence;
            this.notifyParent();
            this.updateCompetenceAttributes();
        },

        async getCompetences() {
            let request = await axios.get(route('api.competences.index'));
            this.competences = request.data
        },

        updateCompetenceAttributes() {
            if (this.competence) {
                const selectedCompetence = this.competences.find(c => c.id === this.competence.id);
                this.competenceAttributes = selectedCompetence ? selectedCompetence.attributes : [];
            } else {
                this.competenceAttributes = [];
            }
        },

        updateQuestionOption({index, value, placeholder}) {
            this.finalOptions[index].value = value;
            this.finalOptions[index].placeholder = placeholder;
            this.notifyParent();
        },
        notifyParent() {

            if (this.type !== 'abierta') {
            this.competence = {
                ...this.competence,
                attribute: this.selectedAttribute
            };
            }

            this.$emit('questionUpdated', {
                question: new Question(this.type, this.name, this.options, this.competence),
                index: this.baseIndex
            })
        },

        removeQuestionOption(optionKey) {
            this.options.splice(optionKey, 1)
        },
        addAnotherOption() {
            this.options.push({});
        },

    },
}

</script>

