<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";

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
        title: ''
    });

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

                <h2 class="text-3xl mb-2">Filtri</h2>

                <br>

                <label>Campo da filtrare</label>

                <div v-for="(schema, index) in form.schema.filter"
                     :key="index"
                     class="mb-4">

                    <div class="row">
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
                        <div class="col-1">

                            <button type="button"
                                    class="btn btn-primary w-full"
                                    v-if="index >= (form.schema.filter.length - 1)"
                                    @click="schemaFilterAdd('and')">
                                AND
                            </button>

                        </div>

                        <div class="col-1">

                            <button type="button"
                                    class="btn btn-primary w-full"
                                    v-if="index >= (form.schema.filter.length - 1)"
                                    @click="schemaFilterAdd('or')">
                                OR
                            </button>

                        </div>

                    </div>

                </div>

                <br>

                <h2 class="text-3xl mb-2">Campi da mostrare</h2>

                <br>

                <div v-for="(schema, index) in form.schema.table"
                     :key="index"
                     class="mb-4">

                    <div class="row">
                        <div class="col">

                            <select class="form-select"
                                    v-model="form.schema.table[index]['field']">
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
                        <div class="col">

                            <input class="form-control"
                                   v-model="form.schema.table[index]['title']">

                        </div>
                        <div class="col-1">

                            <button type="button"
                                    class="btn btn-primary w-full"
                                    v-if="index >= (form.schema.table.length - 1)"
                                    @click="schemaTableFieldAdd()">
                                +
                            </button>

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
