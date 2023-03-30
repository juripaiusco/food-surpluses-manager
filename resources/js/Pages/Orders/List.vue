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

defineProps({
    data: Object,
    filters: Object,
    modalShow: false,
    modalData: Object,
    modalConfirm: Object,
})

</script>

<template>

    <Head title="Ordini" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Ordini', 'Lista']" />

        </template>

        <ApplicationContainer>

            <div class="inline-flex w-full mb-6">

                <div class="w-3/4">

                    <Link :href="route('orders.create')"
                          class="btn btn-outline-primary">
                        Nuovo Ordine
                    </Link>

                </div>
                <div class="w-1/4">

                    <Search placeholder="Cerca..."
                            route-search="orders.list"
                            :filters="filters" />

                </div>

            </div>

            <Table class="table-striped"
                   :data="{
                        filters: filters,
                        routeSearch: 'orders.list',
                        data: data.data,
                        structure: [{
                            class: 'text-center w-[180px]',
                            label: 'Data',
                            field: 'date',
                        }, {
                            class: 'text-center w-[5%]',
                            label: 'Riferimento',
                            field: 'reference',
                        }, {
                            class: 'text-left',
                            label: 'Negozio',
                            field: 'retail_id',
                        }, {
                            class: 'text-left',
                            label: 'Cliente',
                            field: 'customer_id',
                        }, {
                            class: 'text-center w-[10%]',
                            label: 'Punti',
                            field: 'points',
                        }, {
                            class: 'w-[1%]',
                            btnEdit: true,
                            route: 'orders.edit'
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

                    <template #title>Elimina Ordine</template>
                    <template #body>
                        Vuoi eliminare il l'ordine <span class="font-semibold">{{ modalData.name }}</span> ?
                    </template>

                </ModalSimple>

            </Teleport>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
