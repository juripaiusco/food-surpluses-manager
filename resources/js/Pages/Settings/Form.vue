<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import {__} from "@/extComponents/Translations";
import { FormKitSchema } from '@formkit/vue'
import {ref, watch} from "vue";

const props = defineProps({

    data: Object,
    msg: String,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

form.schemaString = `[
    {
        "$formkit": "text",
        "name": "email",
        "label": "Email",
        "classes": {
            "input": "form-control mb-4",
            "label": "form-label"
        }
    }, {
        "$formkit": "password",
        "name": "password",
        "label": "Password",
        "classes": {
            "input": "form-control mb-4",
            "label": "form-label"
        }
    }
]`;

const parsedSchema = ref([])
const jsonError = ref(null)

// funzione che prova a parsare e, se ok, assicura che form.values abbia le chiavi
function parseAndSyncSchema(str) {
    try {
        parsedSchema.value = JSON.parse(str);
        jsonError.value = null;
    } catch (e) {
        parsedSchema.value = [];
        jsonError.value = e.message;
    }
}

// watch sulla stringa del form per parsare in realtime
watch(() => form.schemaString, (v) => parseAndSyncSchema(v), { immediate: true })

</script>

<template>

    <Head title="Impostazioni" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Impostazioni']" />

        </template>

        <ApplicationContainer>

            <div v-if="msg" class="alert alert-success">
                {{ msg }}
            </div>

            <form @submit.prevent="form.post(route('settings.update'))">

                <nav>
                    <div class="nav nav-underline nav-fill" id="nav-tab" role="tablist">

                        <button class="nav-link active"
                                id="nav-general-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-general"
                                type="button"
                                role="tab"
                                aria-controls="nav-general"
                                aria-selected="true">
                            Generale
                        </button>

                        <button class="nav-link"
                                id="nav-customers_mod_jobs-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-customers_mod_jobs"
                                type="button"
                                role="tab"
                                aria-controls="nav-customers_mod_jobs"
                                aria-selected="true">
                            Modulo Lavoro
                        </button>

                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">

                    <div class="tab-pane fade show active pt-4"
                         id="nav-general"
                         role="tabpanel"
                         aria-labelledby="nav-general-tab"
                         tabindex="0">

                        <h2 class="text-3xl mb-2">Cassa</h2>

                        <label for="shop_btn" class="form-label">
                            Inserisci il codice prodotto, separato da virgola "," (senza doppi apici).
                        </label>
                        <textarea id="shop_btn"
                                  name="shop_btn"
                                  class="form-control mb-4 h-[178px]"
                                  v-model="form.shop_btn"></textarea>

                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="shop_ctrl_points"
                                   id="shop_ctrl_points"
                                   true-value="1"
                                   false-value="0"
                                   v-model="form.shop_ctrl_points">
                            <label class="form-check-label" for="shop_ctrl_points">Controllo punti spesa</label>
                        </div>

                        <h2 class="text-3xl mt-8 mb-2">Report</h2>

                        <label for="report_email" class="form-label">
                            Email invio Report, inserisci le email separato da virgola "," (senza doppi apici).
                        </label>
                        <input id="report_email"
                               name="report_email"
                               type="text"
                               class="form-control"
                               v-model="form.report_email" />
                        <div class="text-red-500"
                             v-if="form.errors.report_email">{{ __(form.errors.report_email) }}</div>

                        <h2 class="text-3xl mt-8 mb-2">Export DB2Excel</h2>

                        <Link class="btn btn-info btn-lg"
                              :href="route('settings.db2excel')">
                            Export DB
                        </Link>

                    </div>

                    <div class="tab-pane fade show pt-4"
                         id="nav-customers_mod_jobs"
                         role="tabpanel"
                         aria-labelledby="nav-customers_mod_jobs-tab"
                         tabindex="0">

                        <h2 class="text-3xl mb-2">Modulo Lavoro - Impostazione campi</h2>

                        <br>

                        <div class="row">
                            <div class="col">

                                <h2 class="text-xl">Codice</h2>

                                <br>

                                <textarea name="json_form_code"
                                          id="json_form_code"
                                          class="form-control w-full !min-h-[200px]"
                                          v-model="form.schemaString"></textarea>

                            </div>
                            <div class="col">

                                <h2 class="text-xl">Anteprima</h2>

                                <br>

                                <FormKitSchema :schema="parsedSchema" />

                            </div>
                        </div>

                    </div>

                </div>

                <br>

                <div class="text-right mt-8">

                    <button type="submit"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
