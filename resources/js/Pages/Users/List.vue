<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import Table from "@/Components/Table/Table.vue";
import Search from "@/Components/Search.vue";
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

            <div class="inline-flex w-full mb-6">

                <div class="w-3/4">

                    <button class="btn btn-primary">Nuovo Volontario</button>

                </div>
                <div class="w-1/4">

                    <Search placeholder="Cerca..."
                            route-search="users.list"
                            :filters="filters" />

                </div>

            </div>

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
                        }, {
                            class: 'text-left text-sm',
                            label: 'Moduli attivi',
                            field: 'modules_list',
                        }, {
                            class: 'text-left',
                            label: 'Negozi',
                            field: '',
                        }],
                    }" />

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
