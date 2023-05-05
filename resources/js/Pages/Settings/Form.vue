<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import Form from "@/Pages/Customers/Form.vue";

const props = defineProps({

    data: Object,
    msg: String,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

</script>

<template>

    <Head title="Impostazioni" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Impostazioni']" />

        </template>

        <ApplicationContainer>

            <h2 class="text-3xl mb-2">Cassa</h2>

            <div v-if="msg" class="alert alert-success">
                {{ msg }}
            </div>

            <form @submit.prevent="form.post(route('settings.update'))">

                <label for="shop_btn" class="form-label">
                    Inserisci il codice prodotto, separato da virgola "," (senza doppi apici)
                </label>
                <textarea id="shop_btn"
                          name="shop_btn"
                          class="form-control mb-4 h-[178px]"
                          v-model="form.shop_btn"></textarea>

                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="shop_ctrl_points"
                           id="shop_ctrl_points"
                           true-value="1"
                           false-value="0"
                           v-model="form.shop_ctrl_points">
                    <label class="form-check-label" for="shop_ctrl_points">Controllo punti spesa</label>
                </div>

                <div class="text-right mt-8">

                    <button type="submit"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
