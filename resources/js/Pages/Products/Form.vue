<script setup xmlns="http://www.w3.org/1999/html">

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";
import Table from "@/Components/Table/Table.vue";
import {__} from "../../extComponents/Translations";

const props = defineProps({

    data: Object,
    categories: Object,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

</script>

<template>

    <Head title="Prodotti" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Prodotti', data.name !== null ? data.name : 'nuovo Prodotto']" />

        </template>

        <ApplicationContainer>

            <form @submit.prevent="form.post(route(
                form.id ? 'products.update' : 'products.store',
                form.id ? form.id : ''
                ))">

                <div class="row">
                    <div class="col-7">

                        <h2 class="text-3xl mb-2">Dati Prodotto</h2>

                        <div class="row mb-4">
                            <div class="col">

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

                                <label for="type" class="form-label">Tipo</label>
                                <select class="form-select"
                                        v-model="form.type">
                                    <option selected>Seleziona tipologia</option>
                                    <option value="fead">Fead</option>
                                    <option value="fead no">Fead NO</option>
                                </select>
                                <div class="text-red-500"
                                     v-if="form.errors.type">{{ __(form.errors.type) }}</div>

                            </div>
                            <div class="col">

                                <label for="type" class="form-label">Categoria</label>
                                <select class="form-select"
                                        v-model="form.category_id">
                                    <option value=""
                                            selected>Nessuna categoria</option>
                                    <option v-for="category in categories"
                                            :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <div class="text-red-500"
                                     v-if="form.errors.category_id">{{ __(form.errors.category_id) }}</div>

                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-8">

                                <label for="name" class="form-label">Nome</label>
                                <input id="name"
                                       name="name"
                                       type="text"
                                       class="form-control"
                                       v-model="form.name" />
                                <div class="text-red-500"
                                     v-if="form.errors.name">{{ __(form.errors.name) }}</div>

                            </div>
                            <div class="col text-center">

                                <label for="points" class="form-label text-center">Punti</label>
                                <input id="points"
                                       name="points"
                                       type="text"
                                       class="form-control text-center"
                                       v-model="form.points" />
                                <div class="text-red-500"
                                     v-if="form.errors.points">{{ __(form.errors.points) }}</div>

                            </div>
                        </div>

                        <label for="description" class="form-label">Descrizione</label>
                        <textarea id="description"
                                  name="description"
                                  class="form-control mb-4 h-[178px]"
                                  v-model="form.description"></textarea>

                        <div class="form-check">

                            <input type="checkbox"
                                   class="form-check-input"
                                   id="monitoring_buy"
                                   true-value="1"
                                   false-value="0"
                                   v-model="form.monitoring_buy" />

                            <label class="form-check-label text-sm"
                                   :for="'monitoring_buy'">
                                In cassa, al momento dell'acquisto, mostra se l'articolo è già stato acquistato.
                                <br>
                                Verrà mostrata una piccola icona prima del nome prodotto.
                            </label>

                        </div>

                        <div class="row border border-gray-300 !mt-4 pt-4 pb-4 rounded-md bg-gray-100">
                            <div class="col">

                                <div class="input-group">
                                    <span class="input-group-text">Kg.</span>
                                    <input id="kg"
                                           name="kg"
                                           type="text"
                                           class="form-control text-center"
                                           placeholder="es. 0.5"
                                           v-model="form.kg" />
                                </div>
                                <div class="text-red-500"
                                     v-if="form.errors.kg">{{ __(form.errors.kg) }}</div>

                            </div>
                            <div class="col">

                                <div class="input-group">
                                    <span class="input-group-text">Q.tà</span>
                                    <input id="amount"
                                           name="amount"
                                           type="text"
                                           class="form-control text-center"
                                           placeholder="es. 2"
                                           v-model="form.amount" />
                                </div>
                                <div class="text-red-500"
                                     v-if="form.errors.amount">{{ __(form.errors.amount) }}</div>

                            </div>
                            <div class="col-12 text-center text-xs pt-4">

                                Queste sono le quantità che vengono scalata al momento dell'acquisto.

                            </div>
                        </div>

                        <div class="text-right mt-8">

                            <Link :href="route('products.index')"
                                  class="btn btn-secondary w-[100px]">Annulla</Link>

                            <button type="submit"
                                    class="btn btn-success ml-2 w-[100px]">Salva</button>

                        </div>

                    </div>
                    <div class="col">

                        <h2 class="text-3xl mb-2">Presenti in magazzino</h2>

                        <div class="border border-green-500 rounded-md bg-green-300 text-green-900">

                            <div class="inline-flex w-full">
                                <div class="w-1/2 text-center p-4">

                                    Kg.

                                    <div class="text-3xl mt-3">
                                        {{ data.kg_total === 0 ? '/' : data.kg_total }}
                                    </div>

                                </div>
                                <div class="w-1/2 text-center p-4">

                                    Q.tà

                                    <div class="text-3xl mt-3">
                                        {{ data.amount_total }}
                                    </div>

                                </div>
                            </div>

                        </div>

                        <h2 class="text-3xl mb-2 mt-8">
                            Movimentazioni <span class="text-lg">(ultime 10)</span>
                        </h2>

                        <div class="text-xs border border-gray-300 rounded-md">

                            <table class="table table-hover table-striped table-sm !mb-0">

                                <thead>

                                <tr>
                                    <th class="!pt-[5px] !pb-[5px]">Data</th>
                                    <th class="!pt-[5px] !pb-[5px]">Utente</th>
                                    <th class="!pt-[5px] !pb-[5px]">Cliente</th>
                                    <th class="!pt-[5px] !pb-[5px]">Ordine</th>
                                    <th class="!pt-[5px] !pb-[5px]">Kg.</th>
                                    <th class="!pt-[5px] !pb-[5px]">Q.tà</th>
                                </tr>

                                </thead>

                                <tbody>

                                <tr v-for="d in data.store">
                                    <td class="text-center !pt-2 !pb-2"
                                        :class="{
                                            '!bg-red-300 !text-red-900': d.amount <= 0,
                                            '!bg-green-300 !text-green-900': d.amount > 0
                                        }">
                                        {{ d.date }}
                                    </td>
                                    <td class="text-center !pt-2 !pb-2"
                                        :class="{
                                            '!bg-red-300 !text-red-900': d.amount <= 0,
                                            '!bg-green-300 !text-green-900': d.amount > 0
                                        }">
                                        {{ d.user.name }}
                                    </td>
                                    <td class="text-center !pt-2 !pb-2"
                                        :class="{
                                            '!bg-red-300 !text-red-900': d.amount <= 0,
                                            '!bg-green-300 !text-green-900': d.amount > 0
                                        }">
                                        {{ d.customer_id === null ? '' : d.customer.cod }}
                                    </td>
                                    <td class="text-center !pt-2 !pb-2"
                                        :class="{
                                            '!bg-red-300 !text-red-900': d.amount <= 0,
                                            '!bg-green-300 !text-green-900': d.amount > 0
                                        }">
                                        {{ d.order === null ? '' : d.order.reference }}
                                    </td>
                                    <td class="text-center !pt-2 !pb-2"
                                        :class="{
                                            '!bg-red-300 !text-red-900': d.amount <= 0,
                                            '!bg-green-300 !text-green-900': d.amount > 0
                                        }">
                                        {{ d.kg === null ? '' : d.kg.toFixed(2) }}
                                    </td>
                                    <td class="text-center !pt-2 !pb-2"
                                        :class="{
                                            '!bg-red-300 !text-red-900': d.amount <= 0,
                                            '!bg-green-300 !text-green-900': d.amount > 0
                                        }">
                                        {{ d.amount }}
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
