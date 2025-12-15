<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import {Codemirror} from "vue-codemirror";
import {FormKitSchema} from "@formkit/vue";

const props = defineProps({

    data: Object,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

let save_redirect = ref(true);

// START - Gestione Anteprima FormKit --------------------------------------------------
const parsedSchema = ref([])
const jsonError = ref(null)

// funzione che prova a parsare e, se ok, assicura che form.values abbia le chiavi
function parseAndSyncSchema(str) {
    try {
        parsedSchema.value = JSON.parse(str)
        jsonError.value = null
    } catch (e) {
        // non azzerare del tutto
        parsedSchema.value = [{ $el: 'div', children: 'Errore JSON' }]
        jsonError.value = e.message
    }
}

// watch sulla stringa del form per parsare in realtime
watch(() => form.schema, (v) => parseAndSyncSchema(v), { immediate: true })

import { EditorView } from '@codemirror/view'

const cmExtensions = [
    json(),
    EditorView.theme({
        /*"&": {
            width: "100px",
            height: "100px"
        },*/
        ".cm-scroller": { overflow: "auto" }
    })
]

import {onMounted, onBeforeUnmount, ref, watch} from "vue"
import {json} from "@codemirror/lang-json";

let observer
let lastValidWidth = '100%'
let lastValidHeight = 600
const editorWrapper = ref(null)
const editorWrapperW = ref(null)
const editorWrapperH = ref(null)
const previewWrapper = ref(null)

onMounted(() => {
    if (previewWrapper.value) {
        observer = new ResizeObserver(entries => {
            let width = 0;
            let height = 0;
            for (const entry of entries) {
                if (!jsonError.value) { // aggiorna solo se JSON valido
                    width = entry.contentRect.width
                    height = entry.contentRect.height
                    lastValidWidth = width
                    lastValidHeight = height
                } else {
                    width = lastValidWidth
                    height = lastValidHeight
                }
            }

            // console.log('Nuova larghezza:', width)
            // console.log('Nuova altezza:', height)

            editorWrapperW.value = width
            editorWrapperH.value = height < 600 ? 600 : height
        })
        observer.observe(previewWrapper.value)
    }
})

onBeforeUnmount(() => {
    if (observer && previewWrapper.value) {
        observer.unobserve(previewWrapper.value)
    }
})
// END - Gestione Anteprima FormKit ----------------------------------------------------

</script>

<template>

    <Head title="Mod. Lav. Settings" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Mod. Lav. Settings', 'Sezioni' , form.title !== null ? form.title : 'nuova Sezione']" />

        </template>

        <ApplicationContainer>

            <h2 class="text-3xl mb-2">Dati Sezione</h2>

            <form @submit.prevent="form.post(route(
                form.id ? 'jobs_settings.sections.update' : 'jobs_settings.sections.store',
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

                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="dynamic"
                           id="dynamic"
                           true-value="1"
                           false-value="0"
                           v-model="form.dynamic">
                    <label class="form-check-label"
                           for="dynamic">
                        Sezione dinamica
                    </label>
                </div>

                <br>

                <h2 class="text-3xl mb-2">Modulo Lavoro - Impostazione campi</h2>

                <br>

                <div class="row">
                    <div class="col" ref="editorWrapper">

                        <h2 class="text-xl text-center">Codice</h2>

                        <br>

                        <Codemirror
                            :extensions="cmExtensions"
                            v-model="form.schema"
                            id="schema"
                            :style="{
                                        width: editorWrapperW + 'px',
                                        height: (editorWrapperH - 68) + 'px',
                                        border: '1px solid #dee2e6;',
                                        borderRadius: '4px'
                                    }"
                        />

                    </div>
                    <div class="col-6" ref="previewWrapper">

                        <h2 class="text-xl text-center">Anteprima</h2>

                        <br>

                        <FormKitSchema :schema="parsedSchema" />

                    </div>
                </div>

                <div class="text-right mt-8">

                    <Link :href="route('jobs_settings.index')"
                          class="btn btn-secondary w-[100px]">Annulla</Link>

                    <button type="submit"
                            @click="save_redirect = false"
                            class="btn btn-primary ml-2 w-[100px]">Aggiorna</button>

                    <button type="submit"
                            @click="save_redirect = true"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
