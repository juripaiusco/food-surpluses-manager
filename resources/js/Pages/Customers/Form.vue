<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({

    data: Object,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

</script>

<template>

    <Head title="Assistiti" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Assistiti', data.id === '' ? data.name : 'nuovo Assistito']" />

        </template>

        <ApplicationContainer>

            <h2 class="text-3xl mb-2">Dati Assistito</h2>

            <form @submit.prevent="form.post(route(
                form.id ? 'customers.update' : 'customers.store',
                form.id ? form.id : ''
                ))">

                <label for="name" class="form-label">Nome</label>

                <input name="name"
                       type="text"
                       class="form-control mb-4"
                       v-model="form.name" />

                <div class="row">
                    <div class="col-2">

                        <label for="name" class="form-label">CAP</label>

                        <input name="name"
                               type="text"
                               class="form-control"
                               v-model="form.zip" />

                    </div>
                    <div class="col-6">

                        <label for="name" class="form-label">Indirizzo</label>

                        <input name="name"
                               type="text"
                               class="form-control"
                               v-model="form.address" />

                    </div>
                    <div class="col-4">

                        <label for="name" class="form-label">Città</label>

                        <input name="name"
                               type="text"
                               class="form-control"
                               v-model="form.city" />

                    </div>
                </div>

                <div class="text-right mt-8">

                    <Link :href="route('customers.list')"
                          class="btn btn-secondary w-[100px]">Annulla</Link>

                    <button type="submit"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
