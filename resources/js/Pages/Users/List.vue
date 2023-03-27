<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import Table from "@/Components/Table/Table.vue";

defineProps({
    users: Object,
    filters: Object
})

</script>

<template>

    <Head title="Volontari" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Volontari', 'Lista']" />

        </template>

        <div class="container mt-8 p-3 bg-white dark:bg-gray-800 dark:text-white shadow-sm rounded-lg">

            <div class="p-6">

                <Table class="table-striped"
                       :data="{
                                filters: filters,
                                routeSearch: 'users.list',
                                data: users.data,
                                structure: [{
                                    class: 'text-left',
                                    label: 'Nome',
                                    field: 'name',
                                }, {
                                    class: 'text-left',
                                    label: 'Email',
                                    field: 'email',
                                }],
                            }" />

            </div>

        </div>

    </AuthenticatedLayout>

</template>
