<script setup>
import {Head, router} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import Table from "@/Components/Table/Table.vue";
import Search from "@/Components/Search.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import ModalSimple from "@/Components/ModalSimple.vue";
import Pagination from "@/Components/Pagination.vue";
import {__date} from "@/extComponents/Date";

defineProps({
    data: Object,
    filters: Object,
    today: String,
    date_today: String,
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

                <div class="w-1/2">



                </div>
                <div class="w-1/4 mr-4">

                    <a class="btn btn-primary w-full"
                       :href="route('orders.download')"
                       target="_blank">
                        Download ultima spesa
                    </a>

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
                            fnc: function (d) {
                                return __date(d.date);
                            }
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
                            route: 'orders.edit_alert',
                            fnc: function (d) {

                                let html = '';

                                if (parseInt(timeStampDate(d.date)) === parseInt(today)) {
                                    html += '<div class=\'btn btn-warning btn-sm\'>';
                                    html += '<svg class=\'w-4 h-4\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'1.5\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z\' /></svg>';
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
