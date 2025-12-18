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
    reports: Object,
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

    <Head title="Mod. Lavoro Report" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Mod. Lav. Report', 'Lista']" />

        </template>

        <ApplicationContainer>

            <div class="inline-flex w-full mb-6">

                <div class="w-3/4">

                    <Link v-for="report in reports" :href="route('jobs_reports.index', {
                        'rid': report.id
                    })"
                          class="btn btn-outline-primary">
                        {{ report.title }}
                    </Link>

                </div>
                <div class="w-1/4">

                    <Search placeholder="Cerca..."
                            route-search="jobs_listen.index"
                            :filters="filters" />

                </div>

            </div>

            {{ data }}

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
