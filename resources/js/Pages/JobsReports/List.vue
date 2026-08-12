<script setup>
import {Head, router} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import Table from "@/Components/Table/Table.vue";
import Search from "@/Components/Search.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {ref, computed} from "vue";
import {Combobox, ComboboxButton, ComboboxInput, ComboboxOption, ComboboxOptions} from "@headlessui/vue";

const props = defineProps({
    data: Object,
    report: Object,
    reportSchema: Object,
    reports: Object,
    filters: Object,
    modalShow: false,
    modalData: Object,
    modalConfirm: Object,
})

let modalShow = ref(props.modalShow);
let modalData = ref(props.modalData);
let modalConfirm = ref(props.modalConfirm);
let structureTable = props.reportSchema.table;

if (props.reportSchema?.table) {

    for(let i = 0; i < structureTable.length; i++) {

        if (!structureTable[i].class) {
            structureTable[i].class = 'text-left';
        }

    }

    if (props.data[0]?.id) {
        structureTable[structureTable.length] = {
            class: 'w-[1%]',
            btnEdit: true,
            route: 'jobs_listen.edit'
        };
    }

    if (props.reportSchema?.drilldown?.report_id) {
        const drilldown = props.reportSchema.drilldown;
        structureTable[structureTable.length] = {
            class: 'w-[1%]',
            btnCustom: true,
            filters: {},
            fnc: () => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>',
            hrefFnc: (d) => route('jobs_reports.index', {
                id: drilldown.report_id,
                s: d[drilldown.field]
            })
        };
    }

}

const query = ref('')

const filteredReports = computed(() => {

    if (query.value === '') {
        return props.reports;
    }

    const q = query.value.toLowerCase();

    return props.reports.filter((r) => r.title?.toLowerCase().includes(q) || r.description?.toLowerCase().includes(q));

})

function reportSelect(report) {

    const reportSelectedSchema = JSON.parse(report.schema);

    router.get(route('jobs_reports.index', {
        id: report.id,
        orderby: reportSelectedSchema?.order?.split(' ')[0],
        ordertype: reportSelectedSchema?.order?.split(' ')[1]?.toLowerCase()
    }))

}

</script>

<template>

    <Head title="Mod. Lavoro Report" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Mod. Lav. Report', 'Lista']" />

        </template>

        <ApplicationContainer>

            <div class="inline-flex w-full mb-6">

                <div class="w-3/4 mr-2">

                    <Combobox :model-value="report" @update:model-value="reportSelect" by="id">
                        <ComboboxButton as="div" class="relative w-full cursor-pointer">

                            <ComboboxInput
                                class="form-control pr-8 cursor-pointer"
                                :display-value="(r) => r?.title || ''"
                                placeholder="Seleziona il report o cerca per titolo o descrizione..."
                                autocomplete="off"
                                @change="query = $event.target.value" />

                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </span>

                            <ComboboxOptions class="absolute bg-white border w-full mt-1 z-10 text-gray-800 max-h-72 overflow-auto">

                                <div v-if="filteredReports.length === 0" class="p-2 text-sm text-gray-500">
                                    Nessun report trovato.
                                </div>

                                <ComboboxOption
                                    v-for="r in filteredReports"
                                    :key="r.id"
                                    :value="r"
                                    v-slot="{ active, selected }"
                                >
                                    <div :class="[
                                        'p-2 cursor-pointer flex items-center justify-between',
                                        selected ? 'bg-blue-50 font-semibold text-blue-900' : (active ? 'bg-gray-100' : '')
                                    ]">
                                        <div>
                                            <div class="font-medium">{{ r.title }}</div>
                                            <div class="text-sm text-gray-500">{{ r.description }}</div>
                                        </div>
                                        <svg v-if="selected" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-blue-600 shrink-0 ml-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div>
                                </ComboboxOption>

                            </ComboboxOptions>

                        </ComboboxButton>
                    </Combobox>

                    <div class="mt-2 ml-2 text-sm">
                        {{ report.description }}
                    </div>

                </div>
                <div class="w-1/4">

                     <Search placeholder="Cerca..."
                            :route-search="route('jobs_reports.index', report.id)"
                            :filters="filters"
                            :disabled="!report?.id" />

                </div>

            </div>

            <div class="inline-flex items-center">

                <div class="mr-4">

                    <a v-if="report.id"
                          class="btn btn-success"
                          :href="route('jobs_reports.export', {
                              id: report.id,
                              s: filters?.s,
                              orderby: filters?.orderby,
                              ordertype: filters?.ordertype
                          })">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>

                    </a>

                </div>

                <div>

                    <small v-if="report.id">
                        <strong>
                            Sono stati trovati {{ data?.length }} risultat{{ (data?.length > 1 || data?.length === 0) ? 'i' : 'o' }}
                        </strong>
                    </small>

                </div>

            </div>

            <br><br>

            <span class="!text-green-500"></span>
            <span class="!text-red-500"></span>
            <span class="!text-blue-500"></span>

            <div v-if="report.query !== null">

                <Table class="table-striped"
                       :data="{
                            filters: filters,
                            routeSearch: route('jobs_reports.index', report.id),
                            data: data,
                            structure: structureTable,
                    }" />

            </div>

            <Table v-if="report.query === null" class="table-striped"
                   :data="{
                        filters: filters,
                        routeSearch: route('jobs_reports.index', report.id),
                        data: data,
                        structure: structureTable,
                    }"
                   @openModal="(data, route) => {
                       modalData = data;
                       modalConfirm = route;
                       modalShow = true;
                   }" />

            <!-- <Pagination class="mt-6"
                        :links="data.links" /> -->

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
