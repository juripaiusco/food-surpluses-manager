<script setup>
import { FormKitSchema } from "@formkit/vue";

const props = defineProps({
    form: Object,
});
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
                        v-for="(data, index) in form.job_settings"
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
                        v-for="(data, index) in form.job_settings"
                        :key="index"
                        class="tab-pane fade dark:!bg-gray-800"
                        :class="{ 'show active': index === 0 }"
                        :id="`v-pane-${index}`"
                        role="tabpanel"
                        :aria-labelledby="`v-tab-${index}`"
                    >
                        <div class="p-4">
                            <FormKit
                                type="form"
                                v-model="form.customers_mod_jobs_values"
                                :actions="false"
                            >
                                <FormKitSchema :schema="JSON.parse(data.schema)" />
                            </FormKit>
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
