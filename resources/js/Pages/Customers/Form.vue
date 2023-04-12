<script setup>

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

    <Head title="Assistiti" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Assistiti', data.name !== null ? data.name + ' ' + data.surname : 'nuovo Assistito']" />

        </template>

        <ApplicationContainer>

            <h2 class="text-3xl mb-2">Dati Assistito</h2>
            <br>

            <form @submit.prevent="form.post(route(
                form.id ? 'customers.update' : 'customers.store',
                form.id ? form.id : ''
                ))">

                <ul class="nav nav-tabs" id="customerTab" role="tablist">
                    <li class="nav-item" role="presentation">

                        <button class="nav-link active w-[120px]"
                                id="home-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#anagrafica"
                                type="button"
                                role="tab"
                                aria-controls="anagrafica"
                                aria-selected="true" >
                            Anagrafica
                        </button>

                    </li>
                    <li class="nav-item" role="presentation">

                        <button class="nav-link w-[120px]"
                                id="profile-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#note"
                                type="button"
                                role="tab"
                                aria-controls="note"
                                aria-selected="false">
                            Note
                        </button>

                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active p-4"
                         id="anagrafica"
                         role="tabpanel"
                         aria-labelledby="home-tab">

                        <!-- ANAGRAFICA -->
                        <div class="row">
                            <div class="col">

                                <label for="surname" class="form-label">Cognome</label>
                                <input name="surname"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.surname" />
                                <div class="text-red-500"
                                     v-if="form.errors.surname">{{ __(form.errors.surname) }}</div>

                            </div>
                            <div class="col">

                                <label for="name" class="form-label">Nome</label>
                                <input name="name"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.name" />
                                <div class="text-red-500"
                                     v-if="form.errors.name">{{ __(form.errors.name) }}</div>

                            </div>
                            <div class="col-2">

                                <label for="family_number" class="form-label">Componenti famiglia</label>
                                <input name="family_number"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.family_number" />
                                <div class="text-red-500"
                                     v-if="form.errors.family_number">{{ __(form.errors.family_number) }}</div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-7">

                                <label for="address" class="form-label">Indirizzo</label>
                                <input name="address"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.address" />

                            </div>
                            <div class="col-2">

                                <label for="city" class="form-label">Città</label>
                                <input name="city"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.city" />

                            </div>
                            <div class="col-1">

                                <label for="provincia" class="form-label">Provincia</label>
                                <input name="provincia"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.provincia" />

                            </div>
                            <div class="col-2">

                                <label for="phone" class="form-label">Telefono</label>
                                <input name="phone"
                                       type="tel"
                                       class="form-control mb-4"
                                       v-model="form.phone" />

                            </div>
                        </div>

                        <div class="row">
                            <div class="col">

                                <label for="number" class="form-label">n. Assistito</label>
                                <input name="number"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.number" />
                                <div class="text-red-500"
                                     v-if="form.errors.number">{{ __(form.errors.number) }}</div>

                            </div>
                            <div class="col">

                                <label for="cod" class="form-label">n. Tessera</label>
                                <input name="cod"
                                       type="text"
                                       class="form-control mb-4 !border-gray-500"
                                       v-model="form.cod" />
                                <div class="text-red-500"
                                     v-if="form.errors.cod">{{ __('customers.cod.' + form.errors.cod) }}</div>

                            </div>
                            <div class="col">

                                <label for="points_renew" class="form-label">Punti da rinnovare a fine mese</label>
                                <input name="points_renew"
                                       type="text"
                                       class="form-control mb-4 !border-green-500"
                                       v-model="form.points_renew" />
                                <div class="text-red-500"
                                     v-if="form.errors.points_renew">{{ __(form.errors.points_renew) }}</div>

                            </div>
                            <div class="col">

                                <label for="points" class="form-label">Punti rimanenti per questo mese</label>
                                <input name="points"
                                       type="text"
                                       class="form-control mb-4 !border-sky-500"
                                       v-model="form.points" />
                                <div class="text-red-500"
                                     v-if="form.errors.points">{{ __(form.errors.points) }}</div>

                            </div>
                        </div>

                        <!-- END - ANAGRAFICA -->

                    </div>
                    <div class="tab-pane fade p-4"
                         id="note"
                         role="tabpanel"
                         aria-labelledby="profile-tab">

                        <!-- NOTE -->

                        <div class="row">
                            <div class="col">

                                <label for="note" class="form-label">Note</label>
                                <textarea id="note"
                                          name="note"
                                          class="form-control mb-4 h-[194px]"
                                          v-model="form.note"></textarea>

                            </div>
                            <div class="col">

                                <label for="note_alert"
                                       class="form-label text-red-500">
                                    Note Avviso
                                    <span class="text-xs">
                                        (note di avviso cassa)
                                    </span>
                                </label>
                                <textarea id="note_alert"
                                          name="note_alert"
                                          class="form-control mb-4 h-[194px] border !border-red-500 !text-red-500"
                                          v-model="form.note_alert"></textarea>

                            </div>
                        </div>

                        <!-- END - NOTE -->

                    </div>
                </div>

                <div class="text-right mt-8">

                    <Link :href="route('customers.index')"
                          class="btn btn-secondary w-[100px]">Annulla</Link>

                    <button type="submit"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

            <hr class="mt-10 mb-10">

            <h2 class="text-3xl mb-2">
                Ordini eseguiti
                <span class="text-lg">(ultimi 10 ordini)</span>
            </h2>

            <div class="border border-sky-200 rounded-md">

                <Table class="table-striped table-info !mb-0"
                       :data="{
                        filters: '',
                        routeSearch: '',
                        data: data.order,
                        structure: [{
                            class: 'text-left w-[20%]',
                            label: 'Data ordine',
                            field: 'date',
                            order: false,
                        }, {
                            class: 'text-left',
                            label: 'Riferimento',
                            field: 'reference',
                            order: false,
                        }, {
                            class: 'text-right w-[20%]',
                            label: 'Punti',
                            field: 'points',
                            order: false,
                        }],
                    }" />

            </div>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
