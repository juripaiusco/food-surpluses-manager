<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";

defineProps({

    data: Object

});

</script>

<template>

    <Head title="Volontari" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Volontari', 'nuovo Volontario']" />

        </template>

        <ApplicationContainer>

            <h2 class="text-3xl mb-2">Dati Volontario</h2>

            <div class="row mb-8">
                <div class="col">

                    <label for="name" class="form-label">Nome</label>

                    <input name="name"
                           type="text"
                           class="form-control"
                           :v-model="data ? data.name : ''" />

                </div>
                <div class="col">

                    <label for="email" class="form-label">Email</label>

                    <input name="email"
                           type="text"
                           class="form-control" />

                </div>
            </div>


            <h2 class="text-3xl mb-2">Moduli attivi</h2>

            <h2 class="text-3xl mb-2">Negozi <span class="text-lg">(dove l'utente opera)</span></h2>

            <div class="text-right">

                <Link :href="route('users.list')"
                      class="btn btn-secondary">Annulla</Link>

                <button class="btn btn-success ml-2">Salva</button>

            </div>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
