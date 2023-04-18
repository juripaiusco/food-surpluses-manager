<script setup xmlns="http://www.w3.org/1999/html">

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import Table from "@/Components/Table/Table.vue";
import {__} from "@/extComponents/Translations";

const props = defineProps({

    data: Object,

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

            <form @submit.prevent="form.post(route(
                form.id ? 'products.box.update' : 'products.box.store',
                form.id ? form.id : ''
                ))">

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

                <div class="text-right mt-8">

                    <Link :href="route('products.box.index')"
                          class="btn btn-secondary w-[100px]">Annulla</Link>

                    <button type="submit"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
