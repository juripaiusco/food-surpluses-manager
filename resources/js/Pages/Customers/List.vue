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

    <Head title="Assistiti" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Assistiti', 'Lista']" />

        </template>

        <ApplicationContainer>

            <div class="inline-flex w-full mb-6">

                <div class="w-3/4">

                    <Link :href="route('customers.create')"
                          class="btn btn-outline-primary">
                        Nuovo Assistito
                    </Link>

                </div>
                <div class="w-1/4">

                    <Search placeholder="Cerca..."
                            route-search="customers.index"
                            :filters="filters" />

                </div>

            </div>

            <Table class="table-striped"
                   :data="{
                        filters: filters,
                        routeSearch: 'customers.index',
                        data: data.data,
                        structure: [{
                            class: 'w-[1%]',
                            btnCustom: true,
                            route: 'customers.active',
                            fnc: function (d) {

                                let html = '';
                                let className = '';

                                if (d.active === 1) {
                                    className = 'text-white bg-green-500 rounded-full';
                                }

                                html += '<svg class=\'w-6 h-6 ' + className + '\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'1.5\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z\' /></svg>';

                                return html;
                            }
                        }, {
                            class: 'text-center w-[5%]',
                            label: 'n.A.',
                            field: 'number',
                        }, {
                            class: 'text-center w-[5%]',
                            label: 'n.T.',
                            field: 'cod',
                        }, {
                            class: 'text-left w-[20%]',
                            label: 'Assistito',
                            field: 'customer_name',
                        }, {
                            class: 'text-left',
                            label: 'Indirizzo',
                            field: 'address',
                        }, {
                            class: 'text-center w-[10%]',
                            label: 'Componenti',
                            field: 'family_number',
                        }, {
                            class: 'text-center w-[10%]',
                            classData: 'text-[11px]',
                            label: 'Punti',
                            field: 'points',
                            fnc: function (d) {

                                let html = '';
                                let percentage = d.points / d.points_renew * 100;

                                html += d.points + ' / ' + d.points_renew;
                                html += '<div class=\'progress mt-1 m-auto w-[65%] !bg-sky-200 border border-sky-400\' style=\'height: 6px;\'>';
                                html += '<div class=\'progress-bar !bg-sky-400\' style=\'width: ' + percentage + '%;\'></div>';
                                html += '</div>';

                                return html;
                            }
                        }, {
                            class: 'w-[1%]',
                            btnEdit: true,
                            route: 'customers.edit'
                        }, {
                            class: 'w-[1%]',
                            btnDel: true,
                            route: 'customers.destroy'
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

                    <template #title>Elimina Assistito</template>
                    <template #body>
                        Vuoi eliminare il l'assistito <span class="font-semibold">{{ modalData.name }}</span> ?
                    </template>

                </ModalSimple>

            </Teleport>

            <template v-if="data.total === 1">

                <hr class="mt-8 mb-4">

                <h2 class="text-3xl mb-2">
                    Ordini eseguiti da
                    <span class="font-semibold">
                        {{ data.data[0].name }} {{ data.data[0].surname }}
                    </span>
                    <span class="text-lg">
                        (ultimi 10 ordini)
                    </span>
                </h2>

                <div class="border border-sky-200 rounded-md">

                    <Table class="table-striped table-info !mb-0"
                           :data="{
                        filters: '',
                        routeSearch: '',
                        data: data.data[0].order,
                        structure: [{
                            class: 'text-left w-[20%]',
                            label: 'Data ordine',
                            field: 'date',
                            order: false,
                        }, {
                            class: 'text-left',
                            label: 'Riferimento',
                            field: 'reference',
                            order: false,
                        }, {
                            class: 'text-right w-[20%]',
                            label: 'Punti',
                            field: 'points',
                            order: false,
                        }],
                    }" />

                </div>

            </template>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
