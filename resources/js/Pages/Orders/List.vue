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
    today: String,
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



                </div>
                <div class="w-1/4">

                    <Search placeholder="Cerca..."
                            route-search="orders.index"
                            :filters="filters" />

                </div>

            </div>

            <Table class="table-striped"
                   :data="{
                        filters: filters,
                        routeSearch: 'orders.index',
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
                            class: 'text-center w-[12%]',
                            label: 'Negozio',
                            field: 'retail.name',
                            order: false
                        }, {
                            class: 'text-left',
                            label: 'Cliente',
                            field: 'customer_name',
                        }, {
                            class: 'text-center w-[10%]',
                            label: 'Punti usati',
                            field: 'points',
                        }, {
                            class: 'w-[1%]',
                            btnCustom: true,
                            route: 'orders.edit',
                            fnc: function (d) {

                                let html = '';

                                if (parseInt(timeStampDate(d.date)) === parseInt(today)) {
                                    html += '<div class=\'btn btn-warning btn-sm\'>';
                                    html += '<svg class=\'w-4 h-4\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'1.5\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10\' /></svg>';
                                    html += '</div>';
                                }

                                return html;
                            }
                        }
                        , {
                            class: 'w-[1%]',
                            btnDel: true,
                            route: 'orders.destroy'
                        }, {
                            class: 'w-[1%]',
                            btnShow: true,
                            route: 'orders.show'
                        }]
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
                        Vuoi eliminare il l'ordine <span class="font-semibold">{{ modalData.reference }}</span> ?
                    </template>

                </ModalSimple>

            </Teleport>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>

<script>
export default {
    methods: {
        timeStampDate(dateString) {
            const date = new Date(dateString);
            // Then specify how you want your dates to be formatted
            let y = new Intl.DateTimeFormat('default', { year: 'numeric' }).format(date);
            let m = new Intl.DateTimeFormat('default', { month: '2-digit' }).format(date);
            let d = new Intl.DateTimeFormat('default', { day: '2-digit' }).format(date);
            /*let h = new Intl.DateTimeFormat('default', { hour: '2-digit' }).format(date);
            let i = new Intl.DateTimeFormat('default', { minute: '2-digit' }).format(date);
            let s = new Intl.DateTimeFormat('default', { second: '2-digit' }).format(date);*/

            return y + m + d;
        }
    }
}
</script>
