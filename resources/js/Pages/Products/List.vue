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

    <Head title="Prodotti" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Prodotti', 'Lista']" />

        </template>

        <ApplicationContainer>

            <div class="inline-flex w-full mb-6">

                <div class="w-3/4">

                    <Link :href="route('products.create')"
                          class="btn btn-outline-primary">
                        Nuovo Prodotto
                    </Link>

                </div>
                <div class="w-1/4">

                    <Search placeholder="Cerca..."
                            route-search="products.index"
                            :filters="filters" />

                </div>

            </div>

            <Table class="table-striped"
                   :data="{
                        filters: filters,
                        routeSearch: 'products.index',
                        data: data.data,
                        structure: [{
                            class: 'text-center w-[5%]',
                            label: 'Control.',
                            field: 'monitoring_buy',
                        }, {
                            class: 'text-left w-[5%]',
                            label: 'Codice',
                            field: 'cod',
                        }, {
                            class: 'text-left w-[10%]',
                            label: 'Tipo',
                            field: 'type',
                        }, {
                            class: 'text-left',
                            label: 'Nome',
                            field: 'name',
                        }, {
                            class: 'text-center w-[10%]',
                            label: 'Kg.',
                            field: 'kg_total',
                        }, {
                            class: 'text-center w-[10%]',
                            label: 'Q.tà',
                            field: 'amount_total',
                        }, {
                            class: 'text-center w-[10%]',
                            label: 'Punti',
                            field: 'points',
                        }, {
                            class: 'w-[1%]',
                            btnEdit: true,
                            route: 'products.edit'
                        }, {
                            class: 'w-[1%]',
                            btnDel: true,
                            route: 'products.destroy'
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

                    <template #title>Elimina Prodotto</template>
                    <template #body>
                        Vuoi eliminare il prodotto <span class="font-semibold">{{ modalData.name }}</span> ?
                    </template>

                </ModalSimple>

            </Teleport>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
