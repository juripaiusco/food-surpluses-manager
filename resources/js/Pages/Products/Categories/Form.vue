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

    <Head title="Categorie Prodotti" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Categorie Prodotto', data.name !== null ? data.name : 'nuova Categoria']" />

        </template>

        <ApplicationContainer>

            <form @submit.prevent="form.post(route(
                form.id ? 'products.categories.update' : 'products.categories.store',
                form.id ? form.id : ''
                ))">

                <div class="row">
                    <div class="col">

                        <label for="name" class="form-label">Nome</label>
                        <input id="name"
                               name="name"
                               type="text"
                               class="form-control"
                               v-model="form.name" />
                        <div class="text-red-500"
                             v-if="form.errors.name">{{ __(form.errors.name) }}</div>

                    </div>
                    <div class="col-2">

                        <label for="limit" class="form-label text-center">Limite</label>
                        <input id="limit"
                               name="limit"
                               type="text"
                               class="form-control text-center"
                               v-model="form.limit" />
                        <div class="text-red-500"
                             v-if="form.errors.limit">{{ __(form.errors.limit) }}</div>

                    </div>
                </div>

                <div class="text-right mt-8">

                    <Link :href="route('products.categories.index')"
                          class="btn btn-secondary w-[100px]">Annulla</Link>

                    <button type="submit"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
