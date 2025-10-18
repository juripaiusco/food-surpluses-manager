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

            <div class="inline-flex w-full mb-6">

                <div class="w-1/4">

                    <Link :href="route('jobs.create')"
                          class="btn btn-outline-primary">
                        Nuovo Assistito
                    </Link>

                </div>
                <div class="w-1/4 text-right pr-3">

                    <Link :href="route('jobs.index',
                            {
                                'filters': filters.filters === 'no-order-3-months' ? '' : 'no-order-3-months'
                            }
                          )"
                          class="btn btn-outline-info"
                          :class="{
                            'btn-info !text-white': filters.filters === 'no-order-3-months'
                          }" >
                        filtra no ordini +3 mesi
                    </Link>

                </div>
                <div class="w-1/4 pr-3">

                    <Search placeholder="Cerca per numero fascicolo"
                            route-search="jobs.index"
                            var-search="number"
                            :filters="filters" />

                </div>
                <div class="w-1/4">

                    <Search placeholder="Cerca..."
                            route-search="jobs.index"
                            :filters="filters" />

                </div>

            </div>

            <Table class="table-striped"
                   :data="{
                        filters: filters,
                        routeSearch: 'jobs.index',
                        data: data.data,
                        structure: [{
                            class: 'w-[1%]',
                            fnc: function (d) {

                                let html = '';

                                if (d.note_alert !== null) {
                                    className = 'text-white bg-red-500 rounded-full';

                                    html += '<svg class=\'p-1 m-auto w-6 h-6 ' + className + '\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'1.5\' stroke=\'currentColor\'>';
                                    html += '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0\' />';
                                    html += '</svg>';
                                }

                                return html;
                            }
                        }, {
                            class: 'text-center w-[5%]',
                            label: 'n.A.',
                            field: 'number',
                            fnc: function (d) {

                                if (d.active === 0) {
                                    return '<span class=\'text-gray-400\'>' + d.number + '</span>';
                                }

                                return d.number;
                            }
                        }, {
                            class: 'text-center w-[5%]',
                            label: 'n.T.',
                            field: 'cod',
                            fnc: function (d) {

                                if (d.active === 0) {
                                    return '<span class=\'text-gray-400\'>' + d.cod + '</span>';
                                }

                                return d.cod;
                            }
                        }, {
                            class: 'text-left w-[20%]',
                            label: 'Assistito',
                            field: 'customer_name',
                            fnc: function (d) {

                                if (d.active === 0) {
                                    return '<span class=\'text-gray-400\'>' + d.customer_name + '</span>';
                                }

                                return d.customer_name;
                            }
                        }, {
                            class: 'text-left',
                            label: 'Indirizzo',
                            field: 'address',
                            fnc: function (d) {

                                if (d.active === 0) {
                                    return '<span class=\'text-gray-400\'>' + d.address + '</span>';
                                }

                                return d.address;
                            }
                        }, {
                            class: 'text-left',
                            label: 'Telefono',
                            field: 'phone',
                            fnc: function (d) {

                                if (d.active === 0) {
                                    return '<span class=\'text-gray-400\'>' + d.phone + '</span>';
                                }

                                return d.phone;
                            }
                        }, {
                            class: 'text-center w-[10%]',
                            label: 'Componenti',
                            field: 'family_number',
                            fnc: function (d) {

                                if (d.active === 0) {
                                    return '<span class=\'text-gray-400\'>' + d.family_number + '</span>';
                                }

                                return d.family_number;
                            }
                        }, {
                            class: 'text-center w-[10%]',
                            classData: 'text-[11px]',
                            label: 'Punti',
                            field: 'points',
                            fnc: function (d) {

                                let html = '';
                                let percentage = d.points / d.points_renew * 100;
                                let classNameWrapper = '';
                                let classNameBar = '';

                                classNameWrapper = '!bg-sky-200 !border-sky-400';
                                classNameBar = '!bg-sky-400';

                                if (d.active === 0) {
                                    classNameWrapper = '!bg-gray-200 !border-gray-400';
                                    classNameBar = '!bg-gray-300';
                                }

                                html += d.points + ' / ' + d.points_renew;
                                html += '<div class=\'progress mt-1 m-auto w-[65%] border ' + classNameWrapper + '\' style=\'height: 6px;\'>';
                                html += '<div class=\'progress-bar ' + classNameBar + '\' style=\'width: ' + percentage + '%;\'></div>';
                                html += '</div>';

                                return html;
                            }
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

                    <template #title>Elimina Assistito</template>
                    <template #body>
                        Vuoi eliminare il l'assistito <span class="font-semibold">{{ modalData.name }}</span> ?
                    </template>

                </ModalSimple>

            </Teleport>

            <template v-if="data.total === 1">

                <hr class="mt-8 mb-4">

                <div v-if="data?.data[0]?.note_alert"
                     class="alert alert-danger">
                    ATTENZIONE:
                    <br>
                    {{ data.data[0].note_alert }}
                </div>

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
