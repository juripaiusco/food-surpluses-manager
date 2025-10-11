<script setup>
import {defaultConfig, FormKitSchema} from "@formkit/vue";
import { createAutoHeightTextareaPlugin } from '@formkit/addons'
import {nextTick, onBeforeUnmount, onMounted, ref} from "vue";

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

const dynamicSchemas = ref([]);

// inizializza un array vuoto per ogni tab
onMounted(() => {
    dynamicSchemas.value = props.form.customers_mod_jobs_schema.map(data => {
        const schema = JSON.parse(data.schema);
        // se il JSON è un array di array (vecchia versione), lo appiattiamo
        return Array.isArray(schema[0]) ? schema.flat() : schema;
    });
});

function addSchema(index, schemaJson) {
    try {
        const schema = JSON.parse(schemaJson);

        // aggiungi lo schema in modo "piatto"
        dynamicSchemas.value[index].push(schema);

        // salva in formato array
        props.form.customers_mod_jobs_schema[index].schema = JSON.stringify(dynamicSchemas.value[index]);

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

    <div class="container-fluid">
        <div class="row flex-nowrap">

            <!-- Nav verticale -->
            <div class="col-auto !p-0 border-end vertical-tabs">
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
                        {{ data.title }}
                    </button>
                </div>
            </div>

            <!-- Contenuto -->
            <div class="col !p-0">
                <div class="tab-content" id="v-tabs-content">
                    <div
                        v-for="(data, index) in form.customers_mod_jobs_schema"
                        :key="index"
                        class="tab-pane fade dark:!bg-gray-800 !min-h-[400px]"
                        :class="{ 'show active': index === 0 }"
                        :id="`v-pane-${index}`"
                        role="tabpanel"
                        :aria-labelledby="`v-tab-${index}`"
                    >
                        <div class="p-4">
                            <FormKit
                                type="form"
                                v-model="form.customers_mod_jobs_values"
                                :config="config"
                                :actions="false"
                            >


<!--                                <FormKitSchema :schema="JSON.parse(data.schema)" />-->

                                <FormKitSchema v-for="(schema, sIndex) in dynamicSchemas[index]"
                                               :key="sIndex"
                                               :schema="schema" />
                            </FormKit>

                            <div v-if="data.dynamic === '1'"
                                 class="text-right">
                                <button type="button"
                                        class="btn btn-primary"
                                        @click="addSchema(index, data.schema)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
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
