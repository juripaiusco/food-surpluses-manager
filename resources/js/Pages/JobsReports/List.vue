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

        structureTable[i].class = 'text-left';

    }

    structureTable[structureTable.length] = {
        class: 'w-[1%]',
        btnEdit: true,
        route: 'jobs_listen.edit'
    };

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

                    <Link v-for="report in reports" :href="route('jobs_reports.index', {
                        'rid': report.id
                    })"
                          class="btn btn-outline-primary mr-4">
                        {{ report.title }}
                    </Link>

                </div>
                <div class="w-1/4">

                    <Search placeholder="Cerca..."
                            route-search="jobs_listen.index"
                            :filters="filters" />

                </div>

            </div>

            <small v-if="report.id">
                {{ report.description }}

                <br>

                <strong>
                    Sono stati trovati {{ data?.length }} risultat{{ (data?.length > 1 || data?.length === 0) ? 'i' : 'o' }}
                </strong>
            </small>

            <br><br>

            <Table class="table-striped"
                   :data="{
                        filters: filters,
                        routeSearch: 'jobs_settings.reports.index',
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
