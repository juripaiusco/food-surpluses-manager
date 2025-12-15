<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import {Codemirror} from "vue-codemirror";
import {FormKitSchema} from "@formkit/vue";

const props = defineProps({

    data: Object,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

</script>

<template>

    <Head title="Mod. Lav. Settings" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Mod. Lav. Settings', 'Report' , form.title !== null ? form.title : 'nuovo Report']" />

        </template>

        <ApplicationContainer>

            <h2 class="text-3xl mb-2">Dati Report</h2>

            <form @submit.prevent="form.post(route(
                form.id ? 'jobs_settings.reports.update' : 'jobs_settings.reports.store',
                [
                    form.id ? form.id : '',
                    { 'redirect': save_redirect }
                ]
                ), {
                    preserveScroll: true
                })">

                <label for="title" class="form-label">Titolo</label>

                <input name="title"
                       type="text"
                       class="form-control mb-4"
                       v-model="form.title" />

                <br>

                <h2 class="text-3xl mb-2">Filtri</h2>

                <br>

                inserire qui filtri

                <div class="text-right mt-8">

                    <Link :href="route('jobs_settings.reports.index')"
                          class="btn btn-secondary w-[100px]">Annulla</Link>

                    <button type="submit"
                            @click="save_redirect = true"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
