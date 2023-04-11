<script setup>

import {Head, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import Table from "@/Components/Table/Table.vue";

const props = defineProps({

    data: Object,
    create_url: String,

});

const form = useForm({
    s_customer: props.data ? props.data.s_customer : null,
    s_product: null,
});

const formConfirm = useForm({
    customer_id: props.data ? props.data.customer.id : null,
    products: usePage().props.shopProducts
});

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
                                   placeholder="Inserisci codice prodotto"
                                   ref="s_product"
                                   name="s_product"
                                   v-model="form.s_product"
                                   @input="form.get(route('shop.index'))" />

                        </form>

                        <div v-if="usePage().props.shopProducts && usePage().props.shopProducts.length > 0"
                             class="alert alert-success mt-4">

                            <Table class="table-striped text-sm"
                                   :data="{
                                        filters: '',
                                        routeSearch: '',
                                        data: usePage().props.shopProducts.slice().reverse(),
                                        structure: [{
                                            class: 'w-[1%]',
                                            btnDel: true,
                                            route: 'shop.remove'
                                        }, {
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
                                            label: 'Kg',
                                            field: 'kg',
                                            order: false
                                        }, {
                                            class: 'text-right',
                                            label: 'Q.tà',
                                            field: 'amount',
                                            order: false
                                        }, {
                                            class: 'text-right',
                                            classData: 'font-bold text-2xl',
                                            label: 'Punti',
                                            field: 'points',
                                            order: false
                                        }, {
                                            class: 'text-left',
                                            label: '',
                                            field: 'monitoring_buy',
                                            order: false,
                                            fnc: function (d) {

                                                let html = '';

                                                if (d.monitoring_buy === 1) {
                                                    html = '<div class=\'w-2 h-2 bg-red-600 rounded-full m-auto animate-ping\'></div>';
                                                }

                                                return html;

                                            }
                                        }],
                                    }"
                                    @openModal="(data, route) => {

                                        routeTo(route, data);

                                    }" />

                        </div>

                    </div>
                    <div class="col">

                        <div class="text-right">
                            Cod. Cliente: <span class="text-2xl font-semibold">{{ data.customer.cod }}</span>
                        </div>

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

                        <div v-if="data.customer.note_alert"
                             class="alert alert-danger whitespace-break-spaces">
                            {{ data.customer.note_alert }}
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

                        <form @submit.prevent="formConfirm.post(route('shop.store'))">

                            <button type="submit"
                                    class="btn btn-success btn-lg w-full mt-6"
                                    :disabled="btn_shopStore_disabled"
                                    ref="submitShopStore" >Termina Ordine</button>

                        </form>

                    </div>
                </div>

            </div>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>

<script>
import {useForm} from "@inertiajs/vue3";

export default {
    data () {
        return {
            params: {
                points_customer: this.data.customer.points,
                points_products: 0,
                points_count: this.data.customer.points,
            },
            btn_shopStore_disabled: false
        }
    },
    methods: {
        routeTo(route, data) {

            let form = useForm({
                product: data,
                s_customer: this.$props.data.s_customer
            });

            form.get(route);

        }
    },
    mounted() {

        if (this.data.s_customer === null || (this.data.s_customer && !this.data.customer.id)) {
            this.$refs.s_customer.focus();
        }

        if (this.data.customer.id) {
            this.$refs.s_product.focus();
        }

        if (this.data.s_customer && this.data.s_product) {
            window.location.href = this.create_url;
        }

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        let shopProducts = usePage().props.shopProducts;

        // Calcolo dei punti cassa
        if (shopProducts) {

            shopProducts.forEach((d) => {
                this.params.points_products += d.points;
            });

            this.params.points_count = this.params.points_customer - this.params.points_products;

            // Verifico che il punteggio sia superiore a ZERO
            if (this.params.points_count < 0) {

                this.btn_shopStore_disabled = true;

            }

        }

    }
}
</script>
