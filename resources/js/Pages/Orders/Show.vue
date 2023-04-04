<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import Table from "@/Components/Table/Table.vue";

const props = defineProps({

    data: Object,
    customer: Object,
    products: Object,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

</script>

<template>

    <Head title="Ordini" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Ordini', data.name !== null ? data.reference : 'nuovo Ordine']" />

        </template>

        <ApplicationContainer>

            <div class="row">
                <div class="col-8">

                    <div class="alert alert-warning !border-8">

                        <h2 class="text-2xl mb-2">Intestazione Ordine</h2>

                        <Table class="table-striped"
                               :data="{
                                    filters: '',
                                    routeSearch: '',
                                    data: [data],
                                    structure: [{
                                        class: 'text-left',
                                        label: 'Data',
                                        field: 'date',
                                        order: false
                                    }, {
                                        class: 'text-left',
                                        label: 'Riferimento',
                                        field: 'reference',
                                        order: false
                                    }, {
                                        class: 'text-left',
                                        label: 'Negozio',
                                        field: 'retail.name',
                                        order: false
                                    }, {
                                        class: 'text-left',
                                        label: 'Volontario',
                                        field: 'user.name',
                                        order: false
                                    }],
                                }" />

                    </div>

                    <div class="alert alert-info !border-8">

                        <h2 class="text-2xl mb-2">Dati cliente</h2>

                        <Table class="table-striped"
                               :data="{
                                    filters: '',
                                    routeSearch: '',
                                    data: [customer],
                                    structure: [{
                                        class: 'text-left',
                                        label: 'n.A.',
                                        field: 'number',
                                        order: false
                                    }, {
                                        class: 'text-left',
                                        label: 'n.T.',
                                        field: 'cod',
                                        order: false
                                    }, {
                                        class: 'text-left',
                                        label: 'Nome',
                                        field: 'name',
                                        order: false
                                    }, {
                                        class: 'text-left',
                                        label: 'Cognome',
                                        field: 'surname',
                                        order: false
                                    }, {
                                        class: 'text-center',
                                        label: 'Componenti',
                                        field: 'family_number',
                                        order: false
                                    }],
                                }" />

                    </div>

                </div>
                <div class="col">

                    <div class="border-[12px] border-sky-400 bg-sky-200 rounded-md !pt-[60px] h-[calc(100%-16px)]">

                        <div class="text-[120px] text-center font-bold text-sky-400">

                            -{{ data.points }}
                            <div class="text-sm mt-[-10px]">punti consumati</div>

                        </div>

                    </div>

                </div>
            </div>

            <div class="alert alert-success !border-8">

                <h2 class="text-3xl mb-2">Prodotti consegnati</h2>

                <Table class="table-striped"
                       :data="{
                            filters: '',
                            routeSearch: '',
                            data: products,
                            structure: [{
                                class: 'text-left',
                                label: 'Codice',
                                field: 'cod',
                                order: false
                            }, {
                                class: 'text-left',
                                label: 'Tipo',
                                field: 'type',
                                order: false
                            }, {
                                class: 'text-left',
                                label: 'Nome',
                                field: 'name',
                                order: false
                            }, {
                                class: 'text-right',
                                label: 'Punti',
                                field: 'points',
                                order: false
                            }],
                        }" />

            </div>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
