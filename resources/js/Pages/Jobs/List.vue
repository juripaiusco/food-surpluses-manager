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

    <Head title="Mod. Lavoro" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Mod. Lavoro', 'Lista']" />

        </template>

        <ApplicationContainer>

            <!-- <div class="inline-flex w-full mb-6">

                <div class="w-3/4">

                    <Link :href="route('jobs.create')"
                          class="btn btn-outline-primary">
                        Nuovo Negozio
                    </Link>

                </div>
                <div class="w-1/4">

                    <Search placeholder="Cerca..."
                            route-search="jobs.index"
                            :filters="filters" />

                </div>

            </div> -->

            <!-- <Table class="table-striped"
                   :data="{
                        filters: filters,
                        routeSearch: 'jobs.index',
                        data: data.data,
                        structure: [{
                            class: 'text-left w-[20%]',
                            label: 'Nome',
                            field: 'name',
                        }, {
                            class: 'text-left',
                            label: 'Indirizzo',
                            field: 'address',
                        }, {
                            class: 'w-[1%]',
                            btnEdit: true,
                            route: 'jobs.edit'
                        }, {
                            class: 'w-[1%]',
                            btnDel: true,
                            route: 'jobs.destroy'
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

                    <template #title>Elimina Negozio</template>
                    <template #body>
                        Vuoi eliminare il negozio <span class="font-semibold">{{ modalData.name }}</span> ?
                    </template>

                </ModalSimple>

            </Teleport> -->

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
