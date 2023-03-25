<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import ItemData from "@/Components/Dashboard/ItemData.vue";

defineProps({
    products: '',
    customers: '',
    orders: '',
    points: '',
    orders_today: '',
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="container mt-8 p-3 bg-white dark:bg-gray-800 shadow-sm rounded-lg">

            <div class="p-6">

                <div class="row">
                    <div class="col">

                        <ItemData class="border-yellow-400" :data="{
                                    label: 'Prodotti',
                                    count: '95'
                                }">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>

                        </ItemData>

                    </div>
                    <div class="col">

                        <ItemData class="border-blue-400" :data="{
                                    label: 'Tessere',
                                    count: '217'
                                }">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>

                        </ItemData>

                    </div>
                    <div class="col">

                        <ItemData class="border-gray-400" :data="{
                                    label: 'Ordini',
                                    count: '1220'
                                }">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>

                        </ItemData>

                    </div>
                    <div class="col">

                        <ItemData class="border-green-400" :data="{
                                    label: 'Punti',
                                    count: '571950'
                                }">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>

                        </ItemData>

                    </div>
                </div>

                <div class="border border-gray-100 text-black rounded-md">

                    <div class="card">

                        <div class="card-header">
                            <span>Ordini di oggi</span>
                        </div>
                        <div class="card-body">

                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-left w-[15%]">Data</th>
                                    <th class="text-left w-[15%]">Rif.</th>
                                    <th class="text-left">Cliente</th>
                                    <th class="text-left w-[15%]">Punti</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="order in orders_today">
                                    <td>{{ order.date }}</td>
                                    <td>{{ order.reference }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
