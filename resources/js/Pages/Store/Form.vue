<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import {__} from "@/extComponents/Translations";

const props = defineProps({

    product: Object,
    data: Object,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);
const formStore = useForm({
    date: dataForm.date,
    kg: null,
    amount: null,
    cod: props.product ? props.product.cod : null,
});

</script>

<template>

    <Head title="Magazzino" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Magazzino']" />

        </template>

        <ApplicationContainer>

            <form>

                <div class="w-1/2 m-auto">

                    <input class="form-control form-control-lg text-center"
                           type="text"
                           placeholder="Inserisci codice prodotto"
                           ref="s"
                           name="s"
                           v-model="form.s"
                           @input="form.get(route('store.index'))" />

                </div>

            </form>

            <form @submit.prevent="formStore.post(route('store.store', product.id));">

                <div v-if="(!product && form.s)"
                     class="mt-10 text-center text-3xl">
                    Prodotto non trovato
                </div>

                <div v-if="(product && product.cod)"
                     class="mt-10 text-center">

                    <h1 class="text-3xl">
                        {{ product.cod }}
                        <br>
                        {{ product.name }}
                    </h1>

                    <div class="text-center text-xl mt-8">
                        Inserisci qui sotto le quantità del prodotto ricercato
                    </div>

                    <br>

                    <div class="alert alert-info">

                        <div class="row">
                            <div class="col">

                                <label class="form-label">Data</label>
                                <input type="text"
                                       class="form-control text-center mt-2"
                                       name="date"
                                       v-model="formStore.date" />

                            </div>
                            <div class="col">

                                <label class="form-label">Kg totali da inserire</label>
                                <input type="text"
                                       class="form-control text-center mt-2"
                                       placeholder="kg. prodotto"
                                       ref="kg"
                                       id="kg"
                                       name="kg"
                                       :disabled="product.type === 'fead no'"
                                       v-model="formStore.kg" />

                            </div>
                            <div class="col">

                                <label class="form-label">Q.tà totale da inserire</label>
                                <input type="text"
                                       class="form-control text-center mt-2"
                                       placeholder="q.tà prodotto"
                                       ref="amount"
                                       name="amount"
                                       v-model="formStore.amount" />
                                <div class="text-red-500"
                                     v-if="formStore.errors.amount">{{ __(formStore.errors.amount) }}</div>

                            </div>
                        </div>

                        <input type="hidden"
                               name="cod"
                               v-model="formStore.cod" />

                    </div>

                    <br>

                    <div class="w-1/3 ml-auto pl-3 pr-3 text-center">

                        <button class="btn btn-success btn-lg w-full">Salva</button>

                        <div class="mt-4 text-sm">Oppure premi INVIO</div>

                    </div>

                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>

<script>
export default {
    data () {
        return {}
    },
    mounted() {
        if (this.data.s !== '') {
            this.$refs.s.focus();
        }
        if (this.product && this.product.cod) {

            if (this.product.type === 'fead no') {
                this.$refs.amount.focus();
            } else {
                this.$refs.kg.focus();
            }
        }
    }
}
</script>
