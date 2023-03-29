<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import Table from "@/Components/Table/Table.vue";
import Search from "@/Components/Search.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import ModalSimple from "@/Components/ModalSimple.vue";

defineProps({
    users: Object,
    filters: Object,
    modalShow: false,
    modalData: Object,
    modalConfirm: Object,
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

                    <Link :href="route('users.create')"
                          class="btn btn-outline-primary">
                        Nuovo Volontario
                    </Link>

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
                            field: 'retails_list',
                        }, {
                            class: 'w-[1%]',
                            btnEdit: true,
                            route: 'users.edit'
                        }, {
                            class: 'w-[1%]',
                            btnDel: true,
                            route: 'users.destroy'
                        }],
                    }"
                   @openModal="(data, route) => {
                       modalData = data;
                       modalConfirm = route;
                       modalShow = true;
                   }" />

            <Teleport to="body">

                <ModalSimple :show="modalShow"
                             :data="modalData"
                             :confirm="modalConfirm"
                             @closeModal="modalShow = false">

                    <template #title>Elimina Volontario</template>
                    <template #body>
                        Vuoi eliminare il volontario <span class="font-semibold">{{ modalData.name }}</span> ?
                    </template>

                </ModalSimple>

            </Teleport>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
