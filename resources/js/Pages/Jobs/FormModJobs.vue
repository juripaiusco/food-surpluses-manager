<script setup>
import {defaultConfig, FormKitSchema} from "@formkit/vue";
import {createAutoHeightTextareaPlugin} from '@formkit/addons'
import {watch, nextTick, onBeforeUnmount, onMounted, ref} from "vue";
import {v4 as uuidv4} from 'uuid'

const config = defaultConfig({
    plugins: [
        createAutoHeightTextareaPlugin(),
    ],
})

const props = defineProps({
    form: Object,
});

/**
 * Array reattivo che contiene gli schemi JSON per ogni tab.
 * Ogni elemento è un array di schemi (per supportare l'aggiunta dinamica).
 * @type {Ref<UnwrapRef<*[]>, UnwrapRef<*[]> | *[]>}
 * -------------------------------------------------------------------------------
 */

/**
 * Normalizza i group vuoti:
 * se un campo è un array vuoto, lo converte in oggetto
 */
function normalizeEmptyGroups(obj) {
    if (!obj || typeof obj !== 'object') return obj;

    for (const key in obj) {
        if (Array.isArray(obj[key]) && obj[key].length === 0) {
            // conterrà sempre un oggetto vuoto, non array
            obj[key] = {};
        } else if (typeof obj[key] === 'object') {
            normalizeEmptyGroups(obj[key]);
        }
    }

    return obj;
}

const dynamicSchemas = ref([]);

watch(
    // 1️⃣ Watch per inizializzare i dynamicSchemas solo se i values sono vuoti
    () => props.form.customers_mod_jobs_schema.map(d => d.schema),
    (newSchemas) => {
        dynamicSchemas.value = props.form.customers_mod_jobs_schema.map(d => JSON.parse(d.schema))
    },
    { deep: true, immediate: true }
)

// Osserva i valori del form e corregge in automatico
// immediate: true -> corregge anche eventuali "[]" già presenti nei dati caricati da server
watch(
    () => props.form.customers_mod_jobs_values,
    (newVal) => {
        normalizeEmptyGroups(newVal);
    },
    { deep: true, immediate: true }
);

/**
 * Rimuove il suffisso "_N" da un nome di gruppo dinamico per ottenere il nome base.
 */
function getBaseName(name) {
    return name.replace(/_\d+$/, '');
}

/**
 * Rinumera in sequenza (base, _1, _2, ...) le schede rimaste in dynamicSchemas[index]
 * e sposta i relativi valori in form.customers_mod_jobs_values sulle nuove chiavi,
 * eliminando le vecchie chiavi ormai orfane. Evita che una cancellazione lasci
 * dati "appesi" a una chiave che non ha più uno schema associato.
 */
function renumberSchemas(index) {
    const list = dynamicSchemas.value[index]
    if (!list.length) return

    const baseName = getBaseName(list[0].name)
    const values = props.form.customers_mod_jobs_values
    const oldNames = list.map(s => s.name)

    list.forEach((s, i) => {
        s.name = i === 0 ? baseName : `${baseName}_${i}`
    })

    // raccoglie prima i valori dalle vecchie chiavi (evita perdite in caso di sovrapposizione)
    const moved = {}
    oldNames.forEach((oldName, i) => {
        if (Object.prototype.hasOwnProperty.call(values, oldName)) {
            moved[list[i].name] = values[oldName]
        }
    })

    oldNames.forEach(oldName => { delete values[oldName] })
    Object.assign(values, moved)

    props.form.customers_mod_jobs_schema[index].schema = JSON.stringify(list)
}


// inizializza un array vuoto per ogni tab
onMounted(() => {
    dynamicSchemas.value = props.form.customers_mod_jobs_schema.map(data => {
        return JSON.parse(data.schema);
    });
});

function addSchema(index, schemaJson) {
    try {
        let schema = JSON.parse(schemaJson)

        // se lo schema è un array, prendi il primo elemento
        if (Array.isArray(schema)) schema = schema[0]

        // deep clone
        const newSchema = JSON.parse(JSON.stringify(schema))
        const list = dynamicSchemas.value[index]

        // usa il massimo suffisso "_N" già presente + 1, non la lunghezza dell'array
        // (dopo una cancellazione la lunghezza non riflette gli indici già usati)
        const baseName = getBaseName(schema.name)
        const maxIndex = list.reduce((max, s) => {
            const m = /_(\d+)$/.exec(s.name)
            return m ? Math.max(max, parseInt(m[1], 10)) : max
        }, 0)

        // rinomina il gruppo principale
        newSchema.name = `${baseName}_${maxIndex + 1}`

        // aggiungi id univoco
        newSchema._id = uuidv4()

        // aggiungi al tab corrente
        list.push(newSchema)

        // aggiorna nel form principale
        props.form.customers_mod_jobs_schema[index].schema = JSON.stringify(list)

        nextTick(() => resizeTextareas())
    } catch (err) {
        console.error("Errore parsing schema:", err)
    }
}

function removeSchema(index, id) {
    try {
        const list = dynamicSchemas.value[index]
        const i = list.findIndex(s => s._id === id)
        if (i !== -1) list.splice(i, 1)

        // rinumera in sequenza e sposta i valori sulle chiavi corrette,
        // così eliminare la riga base non lascia orfani i dati delle righe successive
        renumberSchemas(index)

        nextTick(() => resizeTextareas());
    } catch (err) {
        console.error("Errore parsing schema:", err);
    }
}

/** ------------------------------------------------------------------------------- **/

/**
 * Ridimensiona i textarea presenti nel container specificato (o nell'intero documento se non specificato).
 * @param container
 * -------------------------------------------------------------------------------
 */

// funzione che forza il resize dei textarea presenti nel container (o nell'intero documento)
function resizeTextareas(container = document) {
    const areas = (container || document).querySelectorAll('textarea');
    areas.forEach(el => {
        // skip elementi non visibili (display:none o simili)
        if (!(el.offsetWidth || el.offsetHeight || el.getClientRects().length)) return;

        // reset dell'altezza per permettere il corretto calcolo di scrollHeight
        el.style.height = 'auto';
        el.style.overflow = 'hidden';

        // imposta l'altezza sullo scrollHeight
        const h = el.scrollHeight;
        // if (h) el.style.height = h + 'px';

        // dispatch input in modo che eventuali listener (plugin) reagiscano
        el.dispatchEvent(new Event('input', { bubbles: true }));
    });
}

let shownHandler = null;

onMounted(async () => {
    await nextTick();

    // piccolo delay per lasciare FormKit inizializzare gli input
    setTimeout(() => {
        // ridimensiona solo i textarea nelle tab attive (se usi tab bootstrap)
        document.querySelectorAll('.tab-pane.show.active, .tab-pane.active').forEach(p => resizeTextareas(p));
        // e ridimensiona anche eventuali textarea fuori dalle tab
        resizeTextareas(document);
    }, 60);

    // gestore per quando si apre una tab (Bootstrap fires 'shown.bs.tab')
    shownHandler = (e) => {
        // e.target è il button/tab; recupera il selector della pane target (data-bs-target)
        const selector = e?.target?.getAttribute && e.target.getAttribute('data-bs-target');
        const pane = selector ? document.querySelector(selector) : null;
        // lascia il tempo a Bootstrap di mostrare la pane, poi ridimensiona
        setTimeout(() => { resizeTextareas(pane || document); }, 60);
    };

    // attacca il listener a tutti i trigger di tab/pill (pieno supporto per data-bs-toggle="pill" o "tab")
    document.querySelectorAll('[data-bs-toggle="pill"], [data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', shownHandler);
    });
});

onBeforeUnmount(() => {
    // pulizia listener
    document.querySelectorAll('[data-bs-toggle="pill"], [data-bs-toggle="tab"]').forEach(btn => {
        if (shownHandler) btn.removeEventListener('shown.bs.tab', shownHandler);
    });
});

/** ------------------------------------------------------------------------------- **/

</script>

<template>

    <div class="container-fluid min-vh-100">
        <div class="row flex-nowrap align-items-stretch h-100">

            <!-- Nav verticale -->
            <div class="col-auto !p-0 border-end vertical-tabs h-100">
                <div
                    class="nav flex-column nav-pills w-full"
                    id="v-tabs"
                    role="tablist"
                    aria-orientation="vertical"
                >
                    <button
                        v-for="(data, index) in form.customers_mod_jobs_schema"
                        :key="index"
                        class="nav-link w-full dark:!bg-gray-800"
                        :class="{ active: index === 0 }"
                        :id="`v-tab-${index}`"
                        data-bs-toggle="pill"
                        :data-bs-target="`#v-pane-${index}`"
                        type="button"
                        role="tab"
                        :aria-controls="`v-pane-${index}`"
                        :aria-selected="index === 0"
                    >
                        <span :class="{ '!text-red-500 !font-bold': data.error === true }">
                            {{ data.title }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Contenuto -->
            <div class="col !p-0 h-100">
                <div class="tab-content h-100" id="v-tabs-content">
                    <div
                        v-for="(data, index) in form.customers_mod_jobs_schema"
                        :key="index"
                        class="tab-pane fade dark:!bg-gray-800"
                        :class="{ 'show active': index === 0 }"
                        :id="`v-pane-${index}`"
                        role="tabpanel"
                        :aria-labelledby="`v-tab-${index}`"
                    >
                        <div class="p-4 tab-pane-inner">
                            <FormKit
                                type="form"
                                v-model="form.customers_mod_jobs_values"
                                :config="config"
                                :actions="false"
                                :id="`formkit-tab-${index}`"
                            >

                                <div v-for="(schema, sIndex) in dynamicSchemas[index]"
                                     :key="schema._id">

                                    <hr v-if="data.dynamic === '1' && sIndex > 0"
                                        class="my-4">

                                    <div class="flex flex-row">
                                        <div class="flex-col w-full">

                                            <FormKitSchema :schema="[schema]" />

                                        </div>
                                        <div v-if="data.dynamic === '1'"
                                             class="w-10 flex-col text-right ml-4 pt-[30px]">

                                            <button type="button"
                                                    class="btn btn-danger btn-sm"
                                                    v-if="sIndex < (dynamicSchemas[index].length - 1)"
                                                    @click="removeSchema(index, schema._id)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-primary btn-sm"
                                                    v-if="sIndex === (dynamicSchemas[index].length - 1)"
                                                    @click="addSchema(index, data.schema)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                            </button>

                                        </div>
                                    </div>

                                </div>

                            </FormKit>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Force full height for nav column and content */
.vertical-tabs,
.nav.flex-column,
.tab-content,
.tab-pane {
    height: 100%;
}

/* Il contenuto vero e proprio scrolla singolarmente */
.tab-pane .tab-pane-inner {
    height: 100%;
    overflow-y: auto;      /* scroll interno per il contenuto della singola tab */
    -webkit-overflow-scrolling: touch;
}
.vertical-tabs {
    min-height: 100%;
    width: 220px;
}

/* Stile tab */
.nav-pills .nav-link {
    border-radius: 8px 0 0 8px;
    color: #495057;
    text-align: left;
    padding: 10px 15px;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    background-color: transparent;
}

.nav-pills .nav-link.active {
    background-color: #f8f8f8;
    color: #0d6efd;
    font-weight: 500;
    border: 1px solid #eee;
    border-right-color: transparent; /* 👈 elimina il bordo destro */
    position: relative;
    z-index: 2; /* 👈 sovrappone il tab al contenuto */
}

.tab-pane.active {
    background-color: #f8f8f8;
    border-radius: 0 8px 8px 0;
    border: 1px solid #eee;
    margin-left: -1px; /* 👈 unisce visivamente al tab attivo */
    position: relative;
    z-index: 1;
}

.nav-pills .nav-link:hover {
    background-color: #f1f3f5;
}
.nav-pills .nav-link.active:hover {
    background-color: #f8f8f8;
}
</style>
