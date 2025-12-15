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
    filters: Object,
    modalShow: false,
    modalData: Object,
    modalConfirm: Object,
})

let modalShow = ref(props.modalShow);
let modalData = ref(props.modalData);
let modalConfirm = ref(props.modalConfirm);

</script>

<template>

    <Head title="Mod. Lav. Settings" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Mod. Lav. Settings', 'Report', 'Lista']" />

        </template>

        <ApplicationContainer>

            <Link :href="route('jobs_settings.index')"
                  class="btn btn-outline-primary mb-4">
                Sezioni
            </Link>

            <Link :href="route('jobs_settings.reports.index')"
                  class="btn btn-primary mb-4 ml-4">
                Report
            </Link>

            <div class="inline-flex w-full mb-6">

                <div class="w-3/4">

                    <Link :href="route('jobs_settings.reports.create')"
                          class="btn btn-outline-primary">
                        Nuovo Report
                    </Link>

                </div>
                <div class="w-1/4">

                    <Search placeholder="Cerca..."
                            route-search="jobs_settings.reports.index"
                            :filters="filters" />

                </div>

            </div>

            <Table class="table-striped"
                   :data="{
                        filters: filters,
                        routeSearch: 'jobs_settings.reports.index',
                        data: data.data,
                        structure: [{
                            class: 'text-left w-[90%]',
                            label: 'Report',
                            field: 'title',
                        }, {
                            class: 'w-[1%]',
                            btnEdit: true,
                            route: 'jobs_settings.sections.edit'
                        }, {
                            class: 'w-[1%]',
                            btnDel: true,
                            route: 'jobs_settings.sections.destroy'
                        }],
                    }"
                   @openModal="(data, route) => {
                       modalData = data;
                       modalConfirm = route;
                       modalShow = true;
                   }" />

            <Pagination class="mt-6"
                        :links="data.links" />

            <Teleport to="body">

                <ModalSimple :show="modalShow"
                             :data="modalData"
                             :confirm="modalConfirm"
                             @closeModal="modalShow = false">

                    <template #title>Elimina Sezione</template>
                    <template #body>
                        Vuoi eliminare la sezione <span class="font-semibold">{{ modalData.title }}</span> ?
                    </template>

                </ModalSimple>

            </Teleport>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
