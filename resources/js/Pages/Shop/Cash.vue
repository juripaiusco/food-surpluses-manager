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

            <ApplicationHeader :breadcrumb-array="['Cassa']" />

        </template>

        <ApplicationContainer>

            <form v-if="!data.customer.id">

                <div class="w-1/2 m-auto">

                    <input class="form-control form-control-lg text-center"
                           type="text"
                           placeholder="Inserisci codice assistito"
                           ref="s_customer"
                           name="s_customer"
                           v-model="form.s_customer"
                           @input="form.get(route('shop.index'))" />

                </div>

            </form>

            <div v-if="data.customer.id">

                <div class="row">
                    <div class="col-8">

                        <form>

                            <input class="form-control form-control-lg"
                                   type="text"
                                   placeholder="Inserisci codice assistito"
                                   ref="s_product"
                                   name="s_product"
                                   v-model="form.s_product"
                                   @input="form.get(route('shop.index'))" />

                        </form>

                    </div>
                    <div class="col">

                        Cod. Cliente <span class="text-2xl font-semibold">{{ data.customer.cod }}</span>

                        <div class="alert alert-success mt-4">

                            <div class="row">
                                <div class="col-8 align-middle">

                                    <span class="text-2xl font-semibold">
                                        {{ data.customer.name }} {{ data.customer.surname }}
                                    </span>
                                    <br>
                                    Componenti {{ data.customer.family_number }}

                                </div>
                                <div class="col align-middle text-right">

                                    Punti

                                    <br>

                                    <span class="text-3xl font-semibold">
                                        {{ data.customer.points }}
                                    </span>

                                </div>
                            </div>

                        </div>

                        <div class="row pt-2 pb-2">
                            <div class="col">Punti cliente</div>
                            <div class="col text-right">
                                {{ params.points_customer }}
                            </div>
                        </div>
                        <div class="row border-t border-t-black pt-2 pb-2">
                            <div class="col">Punti prodotto</div>
                            <div class="col text-right">
                                {{ params.points_products }}
                            </div>
                        </div>
                        <div class="row border-t border-t-black pt-2 pb-2">
                            <div class="col">Punti rimanenti</div>
                            <div class="col text-right text-3xl font-semibold">
                                {{ params.points_count }}
                            </div>
                        </div>

                        <button class="btn btn-success btn-lg w-full mt-6">Termina Ordine</button>

                    </div>
                </div>

            </div>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>

<script>
export default {
    data () {
        return {
            params: {
                points_customer: this.data.customer.points,
                points_products: 0,
                points_count: this.data.customer.points,
            }
        }
    },
    mounted() {

        if (this.data.s_customer === null || (this.data.s_customer && !this.data.customer.id)) {
            this.$refs.s_customer.focus();
        }

        if (this.data.customer.id) {
            this.$refs.s_product.focus();
        }

    }
}
</script>
