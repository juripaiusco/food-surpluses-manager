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
import ItemData from "@/PagesComponents/Dashboard/ItemData.vue";

defineProps({
    data_reports_customers: Object,
    data_reports_procuts: Object,
    date_today: String,
    filters: Object,
})

</script>

<template>

    <Head title="Report" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Report']" />

        </template>

        <ApplicationContainer>

            <div class="inline-flex w-full mb-6">

                <div class="w-3/4">

                    <span class="text-3xl font-semibold">
                        Report del {{ filters.s === null ? date_today : filters.s }}
                    </span>

                </div>
                <div class="w-1/4">

                    <Search :placeholder="'Cerca es.: ' + date_today"
                            class="text-center"
                            route-search="report.index"
                            :filters="filters" />

                    <div v-if="filters.s !== null">

                        <Link class="btn btn-success w-full mt-2"
                              :href="route('report.sendmail', [{ s: filters.s }])">
                            Invia report via mail
                        </Link>

                    </div>

                </div>

            </div>

            <div class="row mb-4">
                <div class="col">

                    <ItemData class="border-sky-400" :data="{
                                    label: 'Famiglie aiutate',
                                    count: data_reports_customers.family
                                }">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>

                    </ItemData>

                </div>
                <div class="col">

                    <ItemData class="border-blue-400" :data="{
                                    label: 'Persone aiutate',
                                    count: data_reports_customers.family_number
                                }">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                        </svg>

                    </ItemData>

                </div>
            </div>

            <h2 class="text-2xl mb-4">Prodotti distribuiti</h2>

            <Table class="table-striped"
                   :data="{
                        filters: filters,
                        routeSearch: 'report.index',
                        data: data_reports_procuts,
                        structure: [{
                            class: 'text-center w-[10%]',
                            label: 'Cod',
                            field: 'product.cod',
                            order: false,
                        }, {
                            class: 'text-left',
                            label: 'Prodotto',
                            field: 'product.name',
                            order: false,
                        }, /*{
                            class: 'text-center w-[10%]',
                            label: 'Punti',
                            field: 'product.points',
                            order: false,
                        }, */{
                            class: 'text-center w-[10%]',
                            label: 'Kg.',
                            field: 'kg',
                            order: false,
                        }, {
                            class: 'text-center w-[10%]',
                            label: 'Q.tà',
                            field: 'amount',
                            order: false,
                        }],
                    }" />

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
