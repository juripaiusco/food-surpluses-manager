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
})

</script>

<template>

    <Head title="Ordini" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Ordini', 'Attenzione']" />

        </template>

        <ApplicationContainer>

            <h1 class="text-3xl text-center text-red-600 mb-4">Attenzione!</h1>

            <div class="text-xl text-center">
                Stai recuperando un ordine.
                <br><br>
                Questo significa che l'ordine
                <span class="font-semibold">{{ data.reference }}</span>, verrà riaperto in cassa
                <br>
                per poi essere eliminato e sostituito con il nuovo ordine.
                <br><br>
                Quindi se non terminerai il prossimo ordine, tutta la spesa verrà persa.
            </div>

            <br>

            <div class="row">
                <div class="col"></div>
                <div class="col-2">
                    <a href="#"
                       onclick="window.history.back(); return false;"
                       class="btn btn-secondary w-full">Annulla</a>
                </div>
                <div class="col-2">
                    <Link :href="route('orders.edit', data.id)"
                          class="btn btn-success w-full">Apri ordine</Link>
                </div>
                <div class="col"></div>
            </div>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
