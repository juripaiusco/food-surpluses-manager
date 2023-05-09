<script setup xmlns="http://www.w3.org/1999/html">

import {Head, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import Table from "@/Components/Table/Table.vue";
import {__} from "@/extComponents/Translations";
import Pagination from "@/Components/Pagination.vue";
import Search from "@/Components/Search.vue";

const props = defineProps({

    data: Object,
    products: Object,
    filters: Object,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

</script>

<template>

    <Head title="Box Prodotti" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Box Prodotto', data.name !== null ? data.name : 'nuova Scatola']" />

        </template>

        <ApplicationContainer>

            <div class="row">
                <div class="col-7">

                    <div class="alert alert-secondary !bg-gray-100 !border-4 !border-dashed">

                        <h2 class="text-3xl mb-2">Prodotti da inserire</h2>

                        <label for="cod" class="form-label">Cerca il prodotto da inserire nalla box</label>
                        <Search placeholder="Cerca..."
                                :route-search="form.id ? route('products.box.edit', form.id) : 'products.box.create'"
                                :filters="filters" />

                        <br>

                        <Table class="table-striped"
                               :data="{
                                    filters: filters,
                                    routeSearch: form.id ? route('products.box.edit', form.id) : 'products.box.create',
                                    preserveState: true,
                                    data: products.data,
                                    structure: [{
                                        class: 'text-left w-[5%]',
                                        label: 'Codice',
                                        field: 'cod',
                                    }, {
                                        class: 'text-left w-[12%]',
                                        label: 'Tipo',
                                        field: 'type',
                                    }, {
                                        class: 'text-left',
                                        label: 'Nome',
                                        field: 'name',
                                    }, {
                                        class: 'text-center w-[10%]',
                                        label: 'Punti',
                                        field: 'points',
                                    }, {
                                        class: 'w-[1%]',
                                        btnCustom: true,
                                        //route: form.id ? route('products.box.edit', form.id) : 'products.box.create',
                                        route: 'products.box.addToBox',
                                        emit: 'boxAddTo',
                                        fnc: function (d) {

                                            let html = '';

                                            html += '<div class=\'btn btn-primary\'>';
                                            html += '<svg class=\'w-3 h-3\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'1.5\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M12 4.5v15m7.5-7.5h-15\' /></svg>';
                                            html += '</div>';

                                            return html;
                                        }
                                    }],
                                }"
                               @boxAddTo="(data, route) => {

                                    routeTo(route, data);

                          }"/>

                        <Pagination class="mt-6"
                                    :links="products.links"
                                    :preserveState="true" />

                    </div>

                </div>
                <div class="col">

                    <form @submit.prevent="form.post(route(
                        form.id ? 'products.box.update' : 'products.box.store',
                        form.id ? form.id : ''
                    ))">

                        <div class="alert alert-warning">

                            <h2 class="text-3xl mb-2">Scatola</h2>

                            <div class="row">
                                <div class="col-4">

                                    <label for="cod" class="form-label">Codice</label>
                                    <input id="cod"
                                           name="cod"
                                           type="text"
                                           class="form-control"
                                           v-model="form.cod" />
                                    <div class="text-red-500"
                                         v-if="form.errors.cod">{{ __('products.cod.' + form.errors.cod) }}</div>

                                </div>
                                <div class="col">

                                    <label for="name" class="form-label">Nome della scatola</label>
                                    <input id="name"
                                           name="name"
                                           type="text"
                                           class="form-control"
                                           v-model="form.name" />
                                    <div class="text-red-500"
                                         v-if="form.errors.name">{{ __(form.errors.name) }}</div>

                                </div>
                            </div>

                            <br>

                            box {{ usePage().props.boxProducts }}

                        </div>

                        <div class="text-right mt-8">

                            <Link :href="route('products.box.index')"
                                  class="btn btn-secondary w-[100px]">Annulla</Link>

                            <button type="submit"
                                    class="btn btn-success ml-2 w-[100px]">Salva</button>

                        </div>

                    </form>

                </div>
            </div>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>

<script>
import {useForm} from "@inertiajs/vue3";

export default {
    data () {
        return {}
    },
    methods: {
        routeTo (route, data) {

            let form = useForm({
                product: data,
                boxAddTo: true
            });

            form.get(route, {
                preserveScroll: true,
                preserveState: false,
            });

        }
    }
}
</script>
