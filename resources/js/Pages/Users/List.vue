<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import Table from "@/Components/Table/Table.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";

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

        <ApplicationContainer>

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

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
