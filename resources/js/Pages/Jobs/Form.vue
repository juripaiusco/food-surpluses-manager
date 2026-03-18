<script setup>

import {Head, Link} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {useForm} from "@inertiajs/vue3";
import Table from "@/Components/Table/Table.vue";
import {__} from "@/extComponents/Translations";
import FormModJobs from "@/Pages/Jobs/FormModJobs.vue";
import {computed, ref, watch} from "vue";
import {isArray} from "es-toolkit/compat";
import ItemData from "@/PagesComponents/Dashboard/ItemData.vue";
import Box from "@/Components/Box.vue";

const props = defineProps({

    data: Object,
    error: String,
    saveRedirect: String,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

let save_redirect = ref(true);

watch(
    () => form.customers_mod_jobs_values,
    (newVal) => {
        validateHasError()
    },
    { deep: true }
);

function markRequiredFields(nodeArray, values) {
    if (!Array.isArray(nodeArray)) {
        return false;
    }

    let sectionHasError = false;

    nodeArray.forEach(node => {
        if (node.validation && node.validation.includes('required')) {
            const value = values[node.name];
            const currentInputClass = node.classes?.input || '';
            const errorClass = '!border !border-red-500';

            if (
                value === undefined ||
                value === null ||
                (typeof value === 'string' && value.trim() === '') ||
                (Array.isArray(value) && value.length === 0)
            ) {
                sectionHasError = true;

                node.classes = node.classes || {};
                node.classes.input = `${currentInputClass} ${errorClass}`.trim();
            } else {
                node.classes = node.classes || {};
                node.classes.input = currentInputClass.replace(errorClass, '').trim();
            }
        }

        // Ricorsione sicura
        if (Array.isArray(node.children)) {
            if (markRequiredFields(node.children, values)) {
                sectionHasError = true;
            }
        }
    });

    return sectionHasError;
}

async function validateHasError() {

    let hasErrorGlobal = false;

    form.customers_mod_jobs_schema.forEach(section => {
        let schema = JSON.parse(section.schema);

        // Controllo ricorsivo dei campi richiesti
        const hasError = markRequiredFields(schema, form.customers_mod_jobs_values);

        // Aggiorna titolo se ci sono errori
        if (hasError) {
            if (!section.title.includes('*')) {
                section.title = `${section.title} *`;
            }
            section.error = hasError;
            hasErrorGlobal = true;
        } else {
            section.title = section.title.replace(/\s\*$/, '');
            section.error = '';
        }

        // Aggiorna lo schema nel form reattivo
        section.schema = JSON.stringify(schema);
    });

    return hasErrorGlobal;
}

async function submit() {
    // Verifico se sono stati compilati tutti i campi
    /*if (!await validateHasError()) {

        await form.post(route(
            form.id ? 'jobs_listen.update' : 'jobs_listen.store',
            form.id ? form.id : ''
        ))

    }*/

    form.post(route(
        form.id ? 'jobs_listen.update' : 'jobs_listen.store',
        [
            form.id ? form.id : '',
            { 'redirect': save_redirect.value }
        ]
    ))
}

function safeNumber(value) {
    const n = parseFloat(value);
    return isNaN(n) ? 0 : n;
}

function sumByPrefix(data, prefix, field) {
    let sum = 0;

    Object.keys(data).forEach(key => {
        if (key === prefix || key.startsWith(prefix + '_')) {

            const obj = data[key];

            if (obj && typeof obj === 'object') {
                sum += safeNumber(obj[field]);
            }
        }
    });

    return sum;
}

function get_last_isee(data) {

    let prefix = 'mod_jobs_isee';
    let iseeArray = [];

    Object.keys(data.customers_mod_jobs_values).forEach(key => {
        if (key === prefix || key.startsWith(prefix + '_')) {
            let scadenza = data.customers_mod_jobs_values[key]['mod_jobs_isee_data_scadenza'];
            let isee = data.customers_mod_jobs_values[key]['mod_jobs_isee_isee'];

            if (scadenza) {
                iseeArray.push({ scadenza, isee });
            }
        }
    });

    if (iseeArray.length === 0) return null;

    // Ordina per data crescente e prendi l'ultimo
    iseeArray.sort((a, b) => new Date(a.scadenza) - new Date(b.scadenza));

    return iseeArray[iseeArray.length - 1].isee;
}

const incomeListFiltered = computed(() => {
    return get_income_list(form)[0].list.filter(field => field.value > 0);
});
const outcomeListFiltered = computed(() => {
    return get_income_list(form)[1].list.filter(field => field.value > 0);
});

function capitalizeWords(text) {
    if (!text) return '';
    return text
        .split(/\s+/)         // separa per spazi
        .map(word => word[0]?.toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
}

function get_income_list(data) {

    let income_fields = [
        'mod_jobs_doc_stipendio_importo',
        'mod_jobs_doc_rdi_importo',
        'mod_jobs_doc_invalidita_importo',
        'mod_jobs_doc_indennita_accompagnatoria_importo',
        'mod_jobs_doc_cassa_integrazione_importo',
        'mod_jobs_doc_naspi_importo',
        'mod_jobs_doc_assegno_unico',

        // campo speciale (familiari)
        {
            type: 'group',
            prefix: 'mod_jobs_famiglia_comp',
            field: 'mod_jobs_famiglia_comp_importo',
            map: (obj) => ({
                label: capitalizeWords(`${obj.mod_jobs_famiglia_comp_tipo_entrate}`)
                    + "\n" + capitalizeWords(`${obj.mod_jobs_famiglia_comp_nome} ${obj.mod_jobs_famiglia_comp_cognome}`),
                value: safeNumber(obj.mod_jobs_famiglia_comp_importo)
            })
        }
    ];

    let outcome_fields = [
        'mod_jobs_famiglia_abitazione_importo',

        {
            type: 'group',
            prefix: 'mod_jobs_famiglia_comp',
            field: 'mod_jobs_famiglia_comp_spese',
            map: (obj) => ({
                label: capitalizeWords(`${obj.mod_jobs_famiglia_comp_tipo_uscite}`)
                    + "\n" + capitalizeWords(`${obj.mod_jobs_famiglia_comp_nome} ${obj.mod_jobs_famiglia_comp_cognome}`),
                value: safeNumber(obj.mod_jobs_famiglia_comp_spese)
            })
        }
    ];

    let income = [{
        name: 'income',
        label: 'Entrate',
        sum: 0,
        list: []
    }, {
        name: 'outcome',
        label: 'Uscite',
        sum: 0,
        list: []
    }];

    // ENTRATE
    income_fields.forEach(field => {

        if (typeof field === 'string') {
            income[0].sum += safeNumber(data.customers_mod_jobs_values[field]);

            data.customers_mod_jobs_schema.forEach(section => {
                income[0].list.push(...findFieldsByName(
                    JSON.parse(section.schema),
                    field,
                    data.customers_mod_jobs_values[field]
                ));
            });
        }

        if (typeof field === 'object' && field.type === 'group') {
            income[0].sum += sumByPrefix(data.customers_mod_jobs_values, field.prefix, field.field);

            Object.keys(data.customers_mod_jobs_values).forEach(key => {

                if (key === field.prefix || key.startsWith(field.prefix + '_')) {

                    const obj = data.customers_mod_jobs_values[key];

                    if (!obj || typeof obj !== 'object') return;

                    const mapped = field.map(obj);

                    if (mapped.value > 0) {
                        income[0].list.push(mapped);
                    }
                }
            });
        }

    });

    // USCITE
    outcome_fields.forEach(field => {

        if (typeof field === 'string') {
            income[1].sum += safeNumber(data.customers_mod_jobs_values[field]);

            data.customers_mod_jobs_schema.forEach(section => {
                income[1].list.push(...findFieldsByName(
                    JSON.parse(section.schema),
                    field,
                    data.customers_mod_jobs_values[field]
                ));
            });
        }

        if (typeof field === 'object' && field.type === 'group') {
            income[1].sum += sumByPrefix(data.customers_mod_jobs_values, field.prefix, field.field);

            Object.keys(data.customers_mod_jobs_values).forEach(key => {

                if (key === field.prefix || key.startsWith(field.prefix + '_')) {

                    const obj = data.customers_mod_jobs_values[key];

                    if (!obj || typeof obj !== 'object') return;

                    const mapped = field.map(obj);

                    if (mapped.value > 0) {
                        income[1].list.push(mapped);
                    }
                }
            });
        }

    });

    return income;
}

function getFamilyList(data, prefix) {
    let list = [];

    Object.keys(data).forEach(key => {

        if (key === prefix || key.startsWith(prefix + '_')) {

            const obj = data[key];

            if (!obj || typeof obj !== 'object') return;

            const nome = obj.mod_jobs_famiglia_comp_nome || '';
            const cognome = obj.mod_jobs_famiglia_comp_cognome || '';
            const lavoro = obj.mod_jobs_famiglia_comp_tipo_entrate || '';

            const value = safeNumber(obj.mod_jobs_famiglia_comp_importo);

            if (value > 0) {
                list.push({
                    name: key,
                    label: `${nome} ${cognome}\n${lavoro}`.trim(),
                    value: value
                });
            }
        }
    });

    return list;
}

function findFieldsByName(schema, fieldName, value, results = []) {

    schema.forEach(item => {

        // match diretto
        if (item.name === fieldName) {
            results.push({
                name: item.name,
                label: item.label,
                value: value,
            });
        }

        // se ha figli (annidati)
        if (item.children && Array.isArray(item.children)) {
            findFieldsByName(item.children, fieldName, value, results);
        }

        // caso FormKit: nested dentro schema
        if (item.schema && Array.isArray(item.schema)) {
            findFieldsByName(item.schema, fieldName, value, results);
        }

    });

    return results;
}

function formatCurrency(value) {
    const number = parseFloat(value);

    return new Intl.NumberFormat('it-IT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
        useGrouping: true
    }).format(isNaN(number) ? 0 : number);
}

</script>

<template>

    <Head title="Mod. Lavoro" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Mod. Lavoro', data.name !== null ? data.name + ' ' + data.surname : 'nuovo Assistito']" />

        </template>

        <ApplicationContainer>

            <div v-if="error" class="alert alert-danger">
                {{ error }}
            </div>

            <h2 class="text-3xl mb-2">Dati Assistito</h2>
            <br>

            <form @submit.prevent="submit">

                <ul class="nav nav-tabs" id="customerTab" role="tablist">
                    <li class="nav-item" role="presentation">

                        <button class="nav-link active w-[240px]"
                                id="anagrafica-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#anagrafica"
                                type="button"
                                role="tab"
                                aria-controls="anagrafica-tab"
                                aria-selected="true" >
                            Anagrafica reception
                        </button>

                    </li>
                    <li class="nav-item" role="presentation">

                        <button class="nav-link w-[240px]"
                                id="note-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#note"
                                type="button"
                                role="tab"
                                aria-controls="note-tab"
                                aria-selected="false">
                            <span :class="{'!text-red-500': form.note_alert}">
                                Note
                                <span v-if="form.note_alert">*</span>
                            </span>
                        </button>

                    </li>
                    <li class="nav-item" role="presentation">

                        <button class="nav-link w-[240px]"
                                id="more-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#more"
                                type="button"
                                role="tab"
                                aria-controls="more-tab"
                                aria-selected="false">
                            Dati aggiuntivi
                        </button>

                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active p-4"
                         id="anagrafica"
                         role="tabpanel"
                         aria-labelledby="anagrafica">

                        <!-- ANAGRAFICA -->
                        <div class="row">
                            <div class="col">

                                <label for="surname" class="form-label">Cognome</label>
                                <input name="surname"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.surname" />
                                <div class="text-red-500"
                                     v-if="form.errors.surname">{{ __(form.errors.surname) }}</div>

                            </div>
                            <div class="col">

                                <label for="name" class="form-label">Nome</label>
                                <input name="name"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.name" />
                                <div class="text-red-500"
                                     v-if="form.errors.name">{{ __(form.errors.name) }}</div>

                            </div>
                            <div class="col-2">

                                <label for="family_number" class="form-label">Componenti famiglia</label>
                                <input name="family_number"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.family_number" />
                                <div class="text-red-500"
                                     v-if="form.errors.family_number">{{ __(form.errors.family_number) }}</div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-7">

                                <label for="address" class="form-label">Indirizzo residenza</label>
                                <input name="address"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.address" />

                            </div>
                            <div class="col-2">

                                <label for="city" class="form-label">Città</label>
                                <input name="city"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.city" />

                            </div>
                            <div class="col-1">

                                <label for="provincia" class="form-label">Provincia</label>
                                <input name="provincia"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.provincia" />

                            </div>
                            <div class="col-2">

                                <label for="phone" class="form-label">Telefono</label>
                                <input name="phone"
                                       type="tel"
                                       class="form-control mb-4"
                                       v-model="form.phone" />

                            </div>
                        </div>

                        <div class="row">
                            <div class="col">

                                <label for="number" class="form-label">n. Assistito</label>
                                <input name="number"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.number" />
                                <div class="text-red-500"
                                     v-if="form.errors.number">{{ __(form.errors.number) }}</div>

                            </div>
                            <div class="col">

                                <label for="cod" class="form-label">n. Tessera</label>
                                <input name="cod"
                                       type="text"
                                       class="form-control mb-4 !border-gray-500"
                                       v-model="form.cod" />
                                <div class="text-red-500"
                                     v-if="form.errors.cod">{{ __('jobs_listen.cod.' + form.errors.cod) }}</div>

                            </div>
                            <div class="col">

                                <label for="points_renew" class="form-label">Punti da rinnovare a fine mese</label>
                                <input name="points_renew"
                                       type="text"
                                       class="form-control mb-4 !border-green-500"
                                       v-model="form.points_renew" />
                                <div class="text-red-500"
                                     v-if="form.errors.points_renew">{{ __(form.errors.points_renew) }}</div>

                            </div>
                            <div class="col">

                                <label for="points" class="form-label">Punti rimanenti per questo mese</label>
                                <input name="points"
                                       type="text"
                                       class="form-control mb-4 !border-sky-500"
                                       v-model="form.points" />
                                <div class="text-red-500"
                                     v-if="form.errors.points">{{ __(form.errors.points) }}</div>

                            </div>
                        </div>

                        <div class="inline-flex">

                            <div class="form-check mr-10">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="view_reception"
                                       id="view_reception"
                                       true-value="1"
                                       false-value="0"
                                       v-model="form.view_reception">
                                <label class="form-check-label" for="view_reception">Visto Reception</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="active"
                                       id="active"
                                       true-value="1"
                                       false-value="0"
                                       v-model="form.active">
                                <label class="form-check-label" for="active">Attivo</label>
                            </div>

                        </div>

                        <hr class="my-10">

                        <FormModJobs :form="form" />

                        <!-- <h2 class="text-3xl mb-2">Mod. Lavoro</h2>
                        <br>

                        <div v-for="(data, index) in form.job_settings">

                            <h2 class="text-3xl mb-2">{{ data.title }}</h2>
                            <FormKit
                                type="form"
                                v-model="form.customers_mod_jobs_values"
                                :actions="false">
                                <FormKitSchema :schema="JSON.parse(data.schema)" />
                            </FormKit>

                        </div>-->

                        <!-- END - ANAGRAFICA -->

                    </div>
                    <div class="tab-pane fade p-4"
                         id="note"
                         role="tabpanel"
                         aria-labelledby="note">

                        <!-- NOTE -->

                        <div class="row">
                            <div class="col">

                                <label for="note" class="form-label">Note</label>
                                <textarea id="note"
                                          name="note"
                                          class="form-control mb-4 h-[194px]"
                                          v-model="form.note"></textarea>

                            </div>
                            <div class="col">

                                <label for="note_alert"
                                       class="form-label text-red-500">
                                    Note Avviso
                                    <span class="text-xs">
                                        (note di avviso cassa)
                                    </span>
                                </label>
                                <textarea id="note_alert"
                                          name="note_alert"
                                          class="form-control mb-4 h-[194px] border !border-red-500 !text-red-500"
                                          v-model="form.note_alert"></textarea>

                            </div>
                        </div>

                        <!-- END - NOTE -->

                    </div>
                    <div class="tab-pane fade p-4"
                         id="more"
                         role="tabpanel"
                         aria-labelledby="more">

                        <!-- MORE -->

                        <div class="row">
                            <div class="col">

                                <label for="name_delegato" class="form-label">Nome del Delegato</label>
                                <input name="name_delegato"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.name_delegato" />
                                <div class="text-red-500"
                                     v-if="form.errors.name_delegato">{{ __(form.errors.name_delegato) }}</div>

                                <label for="c_group" class="form-label">Gruppo</label>
                                <input name="c_group"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.c_group" />
                                <div class="text-red-500"
                                     v-if="form.errors.c_group">{{ __(form.errors.c_group) }}</div>

                                <label for="channel" class="form-label">Tipo canale</label>
                                <input name="channel"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.channel" />
                                <div class="text-red-500"
                                     v-if="form.errors.channel">{{ __(form.errors.channel) }}</div>

                            </div>
                            <div class="col">

                                <label for="char1" class="form-label">Campo 1</label>
                                <input name="char1"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.char1" />
                                <div class="text-red-500"
                                     v-if="form.errors.char1">{{ __(form.errors.char1) }}</div>

                                <label for="char2" class="form-label">Campo 2</label>
                                <input name="char2"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.char2" />
                                <div class="text-red-500"
                                     v-if="form.errors.char2">{{ __(form.errors.char2) }}</div>

                                <label for="char3" class="form-label">Campo 3</label>
                                <input name="char3"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.char3" />
                                <div class="text-red-500"
                                     v-if="form.errors.char3">{{ __(form.errors.char3) }}</div>

                            </div>
                            <div class="col">

                                <div class="alert alert-secondary !bg-gray-100 !pt-[18px]">

                                    <div class="row">
                                        <div class="col">

                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       v-model="form.b1"
                                                       true-value="1"
                                                       false-value="0"
                                                       name="b1"
                                                       id="b1">
                                                <label class="form-check-label" for="b1">B1</label>
                                            </div>

                                        </div>
                                        <div class="col">

                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       v-model="form.b2"
                                                       true-value="1"
                                                       false-value="0"
                                                       name="b2"
                                                       id="b2">
                                                <label class="form-check-label" for="b2">B2</label>
                                            </div>

                                        </div>
                                        <div class="col">

                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       v-model="form.b3"
                                                       true-value="1"
                                                       false-value="0"
                                                       name="b3"
                                                       id="b3">
                                                <label class="form-check-label" for="b3">B3</label>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <!-- END - MORE -->

                    </div>
                </div>

                <div class="text-right mt-8">

                    <Link :href="data.saveRedirect"
                          class="btn btn-secondary w-[100px]">
                        Annulla
                    </Link>
                    <!-- <a href="#"
                       onclick="window.history.back(); return false;"
                       class="btn btn-secondary w-[100px]">Annulla</a> -->

                    <button type="submit"
                            @click="save_redirect = false"
                            class="btn btn-primary ml-2 w-[100px]">Aggiorna</button>

                    <button type="submit"
                            @click="save_redirect = true"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

            <hr class="mt-10 mb-10">

            <h2 class="text-3xl mb-2">
                Riepilogo Entrate/Uscite
            </h2>

            <div class="row">

                <div class="col">

                    <Box class="border-green-400 text-green-800">

                        <div class="text-center align-middle w-full">

                            <span class="text-3xl font-bold">
                                &euro; {{ formatCurrency(get_income_list(form)[0].sum * 12) }}
                            </span>
                            <br>
                            entrate all'anno

                            <table class="table table-sm text-xs mt-4">
                                <tbody>
                                <tr v-for="field in incomeListFiltered" :key="field.id">
                                    <td class="text-left whitespace-break-spaces lowercase capitalize">{{ field.label }}</td>
                                    <td class="text-right">
                                        <span v-if="field.value > 0">
                                            &euro; {{ formatCurrency(field.value) }}
                                        </span>
                                        <span v-else>-</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </div>

                    </Box>

                </div>

                <div class="col">

                    <Box class="border-red-400 text-red-800">

                        <div class="text-center align-middle w-full">

                            <span class="text-3xl font-bold">
                                &euro; {{ formatCurrency(get_income_list(form)[1].sum * 12) }}
                            </span>
                            <br>
                            uscite all'anno

                            <table class="table table-sm text-xs mt-4">
                                <tbody>
                                <tr v-for="field in outcomeListFiltered" :key="field.id">
                                    <td class="text-left whitespace-break-spaces">{{ field.label }}</td>
                                    <td class="text-right">
                                        <span v-if="field.value > 0">
                                            &euro; {{ formatCurrency(field.value) }}
                                        </span>
                                        <span v-else>-</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </div>

                    </Box>

                </div>
                <div class="col">

                    <Box class="border-blue-400 text-blue-800">

                        <div class="text-center align-middle w-full">

                            <span class="text-3xl font-bold">
                                &euro; {{ formatCurrency((
                                get_income_list(form)[0].sum -
                                get_income_list(form)[1].sum
                                ) * 12) }}
                            </span>
                            <br>
                            saldo annuale

                            <hr class="mt-4 mb-4">

                            <span class="text-3xl font-bold">

                                &euro; {{ formatCurrency(get_last_isee(form)) }}
                            </span>
                            <br>
                            ultimo ISEE

                        </div>

                    </Box>

                </div>

            </div>

            <hr class="mt-10 mb-10">

            <h2 class="text-3xl mb-2">
                Ordini eseguiti
                <span class="text-lg">(ultimi 10 ordini)</span>
            </h2>

            <div class="border border-sky-200 rounded-md">

                <Table class="table-striped table-info !mb-0"
                       :data="{
                        filters: '',
                        routeSearch: '',
                        data: data.order,
                        structure: [{
                            class: 'text-left w-[20%]',
                            label: 'Data ordine',
                            field: 'date',
                            order: false,
                        }, {
                            class: 'text-left',
                            label: 'Riferimento',
                            field: 'reference',
                            order: false,
                        }, {
                            class: 'text-right w-[20%]',
                            label: 'Punti',
                            field: 'points',
                            order: false,
                        }],
                    }" />

            </div>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
