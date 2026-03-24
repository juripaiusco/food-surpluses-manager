<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import {Codemirror} from "vue-codemirror";
import { EditorView } from '@codemirror/view'
import { sql } from '@codemirror/lang-sql'
import { oneDark } from '@codemirror/theme-one-dark'

const cmExtensions = [
    sql(),
    EditorView.theme({
        ".cm-scroller": { overflow: "auto" }
    })
]

const props = defineProps({

    data: Object,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

function schemaFilterAdd(operator) {

    form.schema.filter.push({
        add_operator: operator,
        field: '',
        operator: '',
        value: ''
    });

}

function schemaTableFieldAdd() {

    form.schema.table.push({
        field: '',
        label: ''
    });

}

function schemaTableFieldDel(index) {

    form.schema.table.splice(index, 1);

}

function schemaTableFilterdDel(index) {

    form.schema.filter.splice(index, 1);

}

function schemaTableFieldEdit(index, direction) {

    const newIndex = direction === 'up' ? index - 1 : index + 1;

    // Controlla che non esca dai limiti dell'array
    if (newIndex < 0 || newIndex >= form.schema.table.length) return;

    // Rimuove l'elemento dalla posizione attuale e lo inserisce in quella nuova
    const item = form.schema.table.splice(index, 1)[0];
    form.schema.table.splice(newIndex, 0, item);
}

</script>

<template>

    <Head title="Mod. Lav. Settings" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Mod. Lav. Settings', 'Report' , form.title !== null ? form.title : 'nuovo Report']" />

        </template>

        <ApplicationContainer>

            <h2 class="text-3xl mb-2">Dati Report</h2>

            <form @submit.prevent="form.post(route(
                form.id ? 'jobs_settings.reports.update' : 'jobs_settings.reports.store',
                [
                    form.id ? form.id : '',
                    { 'redirect': save_redirect }
                ]
                ), {
                    preserveScroll: true
                })">

                <label for="title" class="form-label">Titolo</label>

                <input name="title"
                       type="text"
                       class="form-control mb-4"
                       v-model="form.title" />

                <label for="description" class="form-label">Descrizione</label>
                <textarea name="description"
                          class="form-control mb-4"
                          v-model="form.description"></textarea>

                <br>

                <h2 class="text-3xl mb-2">Query</h2>

                <Codemirror
                    :extensions="cmExtensions"
                    v-model="form.query"
                    id="query"
                    :style="{
                        width: '100%',
                        border: '1px solid #dee2e6;',
                        borderRadius: '4px'
                    }"
                />

                <br><br>

                <div v-if="form.query === '' || form.query === null">

                    <h2 class="text-3xl mb-2">Filtri</h2>

                    <label>
                        Puoi aggiungere condizioni usando AND e OR in sequenza.
                        Non è possibile creare gruppi di condizioni con parentesi o logiche annidate.
                    </label>

                    <div v-for="(schema, index) in form.schema.filter"
                         :key="index"
                         class="mt-2 mb-4">

                        <div class="row">
                            <div class=""
                                 :class="{
                                    'col-1 text-xl uppercase text-center pt-[3px]' :
                                    form.schema.filter[index].add_operator
                                }">
                                {{ form.schema.filter[index].add_operator }}
                            </div>
                            <div class="col">

                                <select class="form-select"
                                        v-model="form.schema.filter[index]['field']">
                                    <option value="">Seleziona campo</option>

                                    <optgroup v-for="section in form.report_fields"
                                              :label="section.name" >

                                        <option v-for="field in section.fields"
                                                :value="field.name">
                                            {{ field.label }} ({{ field.name }})
                                        </option>

                                    </optgroup>

                                </select>

                            </div>
                            <div class="col-2">

                                <select class="form-select"
                                        v-model="form.schema.filter[index]['operator']">
                                    <option>Seleziona operatore</option>
                                    <option value="like">contiene</option>
                                    <option value="=">=</option>
                                    <option value=">">></option>
                                    <option value="<"><</option>
                                </select>

                            </div>
                            <div class="col-4">

                                <input class="form-control"
                                       v-model="form.schema.filter[index]['value']">

                            </div>
                            <div class="col-2">

                                <div class="inline-flex items-center h-full">

                                    <button v-if="index > 0"
                                            type="button"
                                            class="btn btn-danger btn-sm w-full h-full mr-2"
                                            @click="schemaTableFilterdDel(index)">
                                        -
                                    </button>

                                    <button v-if="index >= (form.schema.filter.length - 1)"
                                            type="button"
                                            class="btn btn-primary w-full mr-2"
                                            @click="schemaFilterAdd('and')">
                                        AND
                                    </button>

                                    <button v-if="index >= (form.schema.filter.length - 1)"
                                            type="button"
                                            class="btn btn-primary w-full mr-2"
                                            @click="schemaFilterAdd('or')">
                                        OR
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                    <br>

                </div>

                <h2 class="text-3xl mb-2">Ordinamento di default</h2>

                <input v-model="form.schema.order"
                       class="form-control">

                <br>

                <h2 class="text-3xl mb-2">Campi da mostrare</h2>

                <br>

                <div v-for="(schema, index) in form.schema.table"
                     :key="index"
                     class="mb-4">

                    <div class="row">
                        <div class="col">

                            <div v-if="index < 1"
                                 class="mb-2 font-semibold text-gray-700 text-center">
                                Field
                            </div>

                            <select v-if="form.query === '' || form.query === null"
                                    v-model="form.schema.table[index]['field']"
                                    class="form-select">
                                <option value="">Seleziona campo</option>

                                <optgroup v-for="section in form.report_fields"
                                          :label="section.name" >

                                    <option v-for="field in section.fields"
                                            :value="field.name">
                                        {{ field.label }} ({{ field.name }})
                                    </option>

                                </optgroup>

                            </select>

                            <input v-else
                                   v-model="form.schema.table[index]['field']"
                                   class="form-control">

                        </div>
                        <div class="col">

                            <div v-if="index < 1"
                                 class="mb-2 font-semibold text-gray-700 text-center">
                                Label
                            </div>

                            <input class="form-control"
                                   v-model="form.schema.table[index]['label']">

                        </div>
                        <div class="col">

                            <div v-if="index < 1"
                                 class="mb-2 font-semibold text-gray-700 text-center">
                                Class
                            </div>

                            <input class="form-control"
                                   v-model="form.schema.table[index]['class']">

                        </div>
                        <div class="col-1">

                            <div v-if="index < 1"
                                 class="h-[15px]">
                            </div>

                            <div class="inline-flex items-center h-full">

                                <button type="button"
                                        class="btn btn-danger btn-sm w-full mr-1"
                                        @click="schemaTableFieldDel(index)">
                                    -
                                </button>

                                <button v-if="index > 0"
                                        type="button"
                                        class="btn btn-secondary btn-sm w-full mr-1"
                                        @click="schemaTableFieldEdit(index, 'up')">
                                    ▲
                                </button>

                                <button v-if="index < form.schema.table.length - 1"
                                        type="button"
                                        class="btn btn-secondary btn-sm w-full mr-1"
                                        @click="schemaTableFieldEdit(index, 'down')">
                                    ▼
                                </button>

                                <button type="button"
                                        class="btn btn-primary btn-sm w-full mr-1"
                                        v-if="index >= (form.schema.table.length - 1)"
                                        @click="schemaTableFieldAdd()">
                                    +
                                </button>

                            </div>

                        </div>
                    </div>

                </div>

                <div class="text-right mt-8">

                    <Link :href="route('jobs_settings.reports.index')"
                          class="btn btn-secondary w-[100px]">Annulla</Link>

                    <button type="submit"
                            @click="save_redirect = true"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
