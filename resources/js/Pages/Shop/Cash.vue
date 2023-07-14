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
    s_product_fead: '',
    s_product_fead_amount: 1,
    s_product_feadno: '',
    s_product_feadno_amount: 1,
});

function searchCustomer (e) {

    if (e.target.value.length >= 8) {
        form.get(route('shop.index'));
    }

    return false;

}

const formConfirm = useForm({
    customer_id: props.data ? props.data.customer.id : null,
    products: usePage().props.shopProducts
});

function changeAmount (ref, addAmount) {

    let refAmount = parseInt(form[ref]);

    if (refAmount >= 1 && (refAmount + addAmount) > 0) {
        form[ref] = refAmount + addAmount;
    }

}

function productSelectReset (refToReset) {

    form[refToReset] = '';
    form.s_product_fead_amount = 1;
    form.s_product_feadno_amount = 1;

}

</script>

<template>

    <Head title="Cassa" />

    <AuthenticatedLayout>

        <template #header v-if="!data.customer.id || data.customer.active !== 1 || data.customer.view_reception !== 1">

            <ApplicationHeader :breadcrumb-array="['Cassa']" />

        </template>

        <ApplicationContainer class="!max-w-[calc(100%-60px)]">

            <form v-if="!data.customer.id || data.customer.active !== 1 || data.customer.view_reception !== 1">

                <div class="w-1/2 m-auto">

                    <input class="form-control form-control-lg text-center"
                           type="text"
                           placeholder="Inserisci codice assistito"
                           ref="s_customer"
                           name="s_customer"
                           v-model="form.s_customer"
                           @input="searchCustomer" />

                    <div v-if="data.s_customer"
                         class="mt-8 text-center text-red-500">

                        <div v-if="!data.customer.id"
                             class="alert alert-danger">
                            Assistito non trovato
                        </div>

                        <div v-if="data.customer.id && data.customer.active !== 1"
                             class="alert alert-danger">
                            <span class="font-semibold">Assistito NON ATTIVO</span>
                            <br>
                            L'assistito deve passare in accettazione ed essere attivato
                        </div>

                        <div v-if="data.customer.id && data.customer.active === 1 && data.customer.view_reception !== 1"
                             class="alert alert-danger">
                            <span class="font-semibold">Assistito NON VISTO IN RECEPTION</span>
                            <br>
                            L'assistito deve passare in accettazione ed essere attivato
                        </div>

                    </div>

                </div>

            </form>

            <div v-if="data.customer.id && data.customer.active === 1 && data.customer.view_reception === 1">

                <div v-if="data.error_limit"
                     class="alert alert-danger text-3xl text-center !border-8 !border-dashed">
                    Non possono essere passati più di
                    <span class="font-semibold">
                        {{ data.product.category.limit }}
                    </span> pezzi di
                    <span class="font-semibold">
                        {{ data.product.category.name }}
                    </span>
                </div>

                <div class="row">
                    <div class="col-8">

                        <form @submit.prevent="noSubmit">

                            <input class="form-control form-control-lg"
                                   type="text"
                                   placeholder="Inserisci codice prodotto"
                                   ref="s_product"
                                   name="s_product"
                                   v-model="form.s_product"
                                   @input="form.get(route('shop.index'/*, {
                                       scrollY: windowTop
                                   }*/))"
                                   @keyup.enter.prevent="noSubmit"
                                   :disabled="btn_shopStore_disabled" />

                        </form>

                        <div class="alert alert-secondary !bg-gray-50 mt-4">

                            <form @submit.prevent="form.get(route('shop.index'/*, {
                                       scrollY: windowTop
                                   }*/))">

                                <div class="row">
                                    <div class="col">

                                        <div class="row">
                                            <div class="col-1 !w-[40px] !pl-4 pt-2">F</div>
                                            <div class="col">

                                                <select ref="s_product_fead"
                                                        name="s_product_fead"
                                                        v-model="form.s_product_fead"
                                                        @change="productSelectReset('s_product_feadno')"
                                                        class="form-select"
                                                        :disabled="btn_shopStore_disabled" >
                                                    <option value=""
                                                            selected></option>
                                                    <option v-for="product in data.products_fead"
                                                            :value="product.cod">
                                                        {{ product.name }}
                                                    </option>
                                                </select>

                                            </div>
                                            <div class="col-1 !p-0 !pr-1">

                                                <button @click="changeAmount('s_product_fead_amount', -1)"
                                                        type="button"
                                                        class="btn btn-danger !w-full !h-full"
                                                        :disabled="btn_shopStore_disabled" >

                                                    <svg class="w-5 h-5 m-auto"
                                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                                    </svg>

                                                </button>

                                            </div>
                                            <div class="col-1 !p-0 !pl-1">

                                                <button @click="changeAmount('s_product_fead_amount', 1)"
                                                        type="button"
                                                        class="btn btn-success !w-full !h-full"
                                                        :disabled="btn_shopStore_disabled" >

                                                    <svg class="w-5 h-5 m-auto"
                                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>

                                                </button>

                                            </div>
                                            <div class="col-2">

                                                <input class="form-control text-center"
                                                       type="text"
                                                       placeholder="1"
                                                       ref="s_product_fead_amount"
                                                       name="s_product_fead_amount"
                                                       v-model="form.s_product_fead_amount"
                                                       :disabled="btn_shopStore_disabled" />

                                            </div>
                                        </div>

                                        <div class="row !mt-4">
                                            <div class="col-1 !w-[40px] !pl-4 pt-2">FN</div>
                                            <div class="col">

                                                <select ref="s_product_feadno"
                                                        name="s_product_feadno"
                                                        v-model="form.s_product_feadno"
                                                        @change="productSelectReset('s_product_fead')"
                                                        class="form-select"
                                                        :disabled="btn_shopStore_disabled" >
                                                    <option selected
                                                            value=""></option>
                                                    <option v-for="product in data.products_feadno"
                                                            :value="product.cod">
                                                        {{ product.name }}
                                                    </option>
                                                </select>

                                            </div>
                                            <div class="col-1 !p-0 !pr-1">

                                                <button @click="changeAmount('s_product_feadno_amount', -1)"
                                                        type="button"
                                                        class="btn btn-danger !w-full !h-full"
                                                        :disabled="btn_shopStore_disabled" >

                                                    <svg class="w-5 h-5 m-auto"
                                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                                    </svg>

                                                </button>

                                            </div>
                                            <div class="col-1 !p-0 !pl-1">

                                                <button @click="changeAmount('s_product_feadno_amount', 1)"
                                                        type="button"
                                                        class="btn btn-success !w-full !h-full"
                                                        :disabled="btn_shopStore_disabled" >

                                                    <svg class="w-5 h-5 m-auto"
                                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>

                                                </button>

                                            </div>
                                            <div class="col-2">

                                                <input class="form-control text-center"
                                                       type="text"
                                                       placeholder="1"
                                                       ref="s_product_feadno_amount"
                                                       name="s_product_feadno_amount"
                                                       v-model="form.s_product_feadno_amount"
                                                       :disabled="btn_shopStore_disabled" />

                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-2 !p-0 !pr-3">

                                        <button type="submit"
                                                class="btn btn-primary w-full h-full"
                                                :disabled="btn_shopStore_disabled" >

                                            Aggiungi

                                        </button>

                                    </div>
                                </div>


                            </form>

                            <div class="mt-4">

                                <div v-for="product in data.products_more_moved"
                                     class="w-1/4 p-1 inline-flex">

                                    <Link class="btn w-full !text-sm"
                                          :class="{
                                                'btn-warning': product.type === 'box',
                                                'btn-outline-primary': product.type === 'fead no',
                                                'btn-info !text-sky-900': product.type === 'fead',
                                                'disabled': params.points_count < 0
                                          }"
                                          :href="route('shop.index', {
                                              s_customer: form.s_customer,
                                              s_product: product.cod,
                                              /*scrollY: windowTop*/
                                          })">
                                        {{ product.name.length > 18 ? product.name.substring(0, 18) + ' ...' : product.name }}
                                    </Link>

                                </div>

                            </div>

                        </div>

                        <div v-if="usePage().props.shopProducts && usePage().props.shopProducts.length > 0"
                             class="alert alert-success mt-4">

                            <Table class="table-striped text-sm table-success"
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
                                        }]
                                    }"
                                    @openModal="(data, route) => {

                                        routeTo(route, data);

                                    }" />

                            <hr class="border-spacing-0.5 border-green-900">

                            <div class="text-right mt-4 pr-[34px]">
                                Totale
                                <span class="text-3xl font-bold ml-4">
                                    {{ params.points_products }}
                                </span>
                            </div>

                        </div>

                    </div>
                    <div class="col">

                        <div class="alert alert-success">

                            <table class="w-full">
                                <tr>
                                    <td class="align-middle">Cod. Cliente</td>
                                    <td class="align-middle text-right">
                                        <span class="text-2xl font-semibold">{{ data.customer.cod }}</span>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td class="align-top">Nome</td>
                                    <td class="align-middle text-right">
                                        <span class="text-2xl font-semibold">
                                            {{ data.customer.name }} {{ data.customer.surname }}
                                        </span>
                                        <br>
                                        Componenti {{ data.customer.family_number }}
                                    </td>
                                </tr> -->
                            </table>

                            <div class="text-right">
                                Componenti {{ data.customer.family_number }}
                            </div>

                            <hr class="mt-4 mb-4 border-green-800">

                            <table class="w-full">
                                <tr>
                                    <td class="align-center">Punti totali</td>
                                    <td class="align-middle text-right">
                                        <span class="text-3xl font-semibold">
                                        {{ data.customer.points_total }}
                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-center">Punti utilizzabili oggi</td>
                                    <td class="align-middle text-right">
                                        <span class="text-3xl font-semibold">
                                        {{ data.customer.points }}
                                    </span>
                                    </td>
                                </tr>
                            </table>

                        </div>

                        <div v-if="data.customer.note_alert ||
                                   data.customer.b1 ||
                                   data.customer.b2 ||
                                   data.customer.b3"
                             class="alert alert-danger whitespace-break-spaces">
                            {{ data.customer.note_alert }}

                            <hr v-if="data.customer.note_alert &&
                            (data.customer.b1 || data.customer.b2 || data.customer.b3)"
                                class="mt-4 mb-4 border-spacing-0.5 border-red-300">

                            {{ data.customer.b1 ? 'B1' : '' }}
                            {{ data.customer.b2 ? 'B2' : '' }}
                            {{ data.customer.b3 ? 'B3' : '' }}

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
                                <span v-if="data.is_first_order && (params.points_products < (data.customer.points * 80/100))"
                                      class="text-sm">
                                    ( almeno {{ data.customer.points * 80/100 }} )
                                </span>
                                {{ params.points_products }}
                            </div>
                        </div>
                        <div class="row border-t border-t-black pt-2 pb-2">
                            <div class="col">Punti rimanenti</div>
                            <div class="col text-right text-3xl font-semibold">
                                {{ params.points_count }}
                            </div>
                        </div>

                        <div v-if="data.is_first_order && (params.points_products < (data.customer.points * 80/100))"
                             class="alert alert-info mt-6 animate-pulse !border-4 !border-sky-400 !border-dashed">
                            Primo ordine del mese,
                            verranno scalati un minimo di
                            <span class="font-semibold text-xl">
                                {{ data.customer.points * 80/100 }}
                            </span>
                            punti.
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
import {router, useForm} from "@inertiajs/vue3";
import soundBeep from '@/../mp3/beep.mp3';
import soundError from '@/../mp3/error.mp3';
import soundError2 from '@/../mp3/error2.mp3';
import soundAlert from '@/../mp3/alert.mp3';

export default {
    data () {
        return {
            params: {
                points_customer: this.data.customer.points,
                points_products: 0,
                points_count: this.data.customer.points,
            },
            btn_shopStore_disabled: false,
            windowTop: 0
        }
    },
    methods: {
        routeTo (route, data) {

            let form = useForm({
                product: data,
                s_customer: this.$props.data.s_customer
            });

            form.get(route, {
                preserveScroll: true,
                preserveState: false,
            });

        },
        playSound (sound) {
            const audio = new Audio(sound);
            audio.play();
        },
        /*onScroll (e) {
            this.windowTop = window.top.scrollY
        },*/
        noSubmit () {
            return false;
        }
    },
    /*beforeDestroy() {
        window.removeEventListener("scroll", this.onScroll)
    },*/
    created() {
        /*window.addEventListener('beforeunload', (e) => {
            let out = 1;
            e.returnValue = out;
            return out;

        });*/
    },
    mounted () {

        // window.addEventListener("scroll", this.onScroll);

        if (this.data.s_customer === null ||
            !this.data.customer.id ||
            this.data.customer.active !== 1 ||
            this.data.customer.view_reception !== 1) {
            this.$refs.s_customer.focus();
        }

        if (this.data.customer.id &&
            this.data.customer.active === 1 &&
            this.data.customer.view_reception === 1) {
            this.$refs.s_product.focus();
        }

        if (this.data.s_customer && this.data.s_product && this.data.error_limit !== true) {
            /*router.get(this.create_url, { scrollY: this.data.scrollY }, {
                onSuccess: () => {

                    window.scroll(0, this.data.scrollY);

                }
            });*/
        }

        if (this.data.s_customer && this.data.product.id && this.data.error_limit !== true) {
            this.playSound(soundBeep);
        }

        if (this.data.error_limit === true) {
            this.playSound(soundError);
        }

        if (this.data.product.monitoring_buy === 1) {
            this.playSound(soundAlert);
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
                this.playSound(soundError);

            }

        }

    }
}
</script>
