<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import Table from "@/Components/Table/Table.vue";
import Search from "@/Components/Search.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import ModalSimple from "@/Components/ModalSimple.vue";
import Pagination from "@/Components/Pagination.vue";
import {ref} from "vue";

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

                <div class="w-3/4">

                    <Link v-for="report in reports"
                          :href="route('jobs_reports.index', {
                              id: report.id,
                              orderby: JSON.parse(report.schema)?.order?.split(' ')[0],
                              ordertype: JSON.parse(report.schema)?.order?.split(' ')[1]?.toLowerCase()
                          })"
                          class="btn btn-outline-primary mr-4">
                        {{ report.title }}
                    </Link>

                </div>
                <div class="w-1/4">

                     <Search placeholder="Cerca..."
                            :route-search="route('jobs_reports.index', report.id)"
                            :filters="filters" />

                </div>

            </div>

            <div class="inline-flex items-center">

                <div class="mr-4">

                    <a v-if="report.id"
                          class="btn btn-success"
                          :href="route('jobs_reports.export', report.id)">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>

                    </a>

                </div>

                <div>

                    <small v-if="report.id">
                        {{ report.description }}

                        <br>

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
