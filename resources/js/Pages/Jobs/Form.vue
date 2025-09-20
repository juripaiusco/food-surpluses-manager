<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {useForm} from "@inertiajs/vue3";
import Table from "@/Components/Table/Table.vue";
import {__} from "@/extComponents/Translations";
import {FormKitSchema} from "@formkit/vue";

const props = defineProps({

    data: Object,
    saveRedirect: String,

});

const dataForm = Object.fromEntries(Object.entries(props.data).map((v) => {
    return props.data ? v : '';
}));

const form = useForm(dataForm);

</script>

<template>

    <Head title="Mod. Lavoro" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Mod. Lavoro', data.name !== null ? data.name + ' ' + data.surname : 'nuovo Assistito']" />

        </template>

        <ApplicationContainer>

            <h2 class="text-3xl mb-2">Dati Assistito</h2>
            <br>

            <form @submit.prevent="form.post(route(
                form.id ? 'jobs.update' : 'jobs.store',
                form.id ? form.id : ''
                ))">

                <ul class="nav nav-tabs" id="customerTab" role="tablist">
                    <li class="nav-item" role="presentation">

                        <button class="nav-link active w-[140px]"
                                id="anagrafica-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#anagrafica"
                                type="button"
                                role="tab"
                                aria-controls="anagrafica-tab"
                                aria-selected="true" >
                            Anagrafica
                        </button>

                    </li>
                    <li class="nav-item" role="presentation">

                        <button class="nav-link w-[140px]"
                                id="note-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#note"
                                type="button"
                                role="tab"
                                aria-controls="note-tab"
                                aria-selected="false">
                            <span :class="{'!text-red-500': form.note_alert}">
                                Note
                                <span v-if="form.note_alert">*</span>
                            </span>
                        </button>

                    </li>
                    <li class="nav-item" role="presentation">

                        <button class="nav-link w-[140px]"
                                id="more-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#more"
                                type="button"
                                role="tab"
                                aria-controls="more-tab"
                                aria-selected="false">
                            Dati aggiuntivi
                        </button>

                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active p-4"
                         id="anagrafica"
                         role="tabpanel"
                         aria-labelledby="anagrafica">

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
                                     v-if="form.errors.cod">{{ __('jobs.cod.' + form.errors.cod) }}</div>

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

                        <div class="inline-flex">

                            <div class="form-check mr-10">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="view_reception"
                                       id="view_reception"
                                       true-value="1"
                                       false-value="0"
                                       v-model="form.view_reception">
                                <label class="form-check-label" for="view_reception">Visto Reception</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="active"
                                       id="active"
                                       true-value="1"
                                       false-value="0"
                                       v-model="form.active">
                                <label class="form-check-label" for="active">Attivo</label>
                            </div>

                        </div>

                        <hr class="my-10">

                        <h2 class="text-3xl mb-2">Mod. Lavoro</h2>
                        <br>

                        <FormKit
                            type="form"
                            v-model="form.customers_mod_jobs_values"
                            :actions="false">
                            <FormKitSchema :schema="JSON.parse(data.customers_mod_jobs_schema)" />
                        </FormKit>

                        <!-- END - ANAGRAFICA -->

                    </div>
                    <div class="tab-pane fade p-4"
                         id="note"
                         role="tabpanel"
                         aria-labelledby="note">

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
                    <div class="tab-pane fade p-4"
                         id="more"
                         role="tabpanel"
                         aria-labelledby="more">

                        <!-- MORE -->

                        <div class="row">
                            <div class="col">

                                <label for="name_delegato" class="form-label">Nome del Delegato</label>
                                <input name="name_delegato"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.name_delegato" />
                                <div class="text-red-500"
                                     v-if="form.errors.name_delegato">{{ __(form.errors.name_delegato) }}</div>

                                <label for="c_group" class="form-label">Gruppo</label>
                                <input name="c_group"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.c_group" />
                                <div class="text-red-500"
                                     v-if="form.errors.c_group">{{ __(form.errors.c_group) }}</div>

                                <label for="channel" class="form-label">Tipo canale</label>
                                <input name="channel"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.channel" />
                                <div class="text-red-500"
                                     v-if="form.errors.channel">{{ __(form.errors.channel) }}</div>

                            </div>
                            <div class="col">

                                <label for="char1" class="form-label">Campo 1</label>
                                <input name="char1"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.char1" />
                                <div class="text-red-500"
                                     v-if="form.errors.char1">{{ __(form.errors.char1) }}</div>

                                <label for="char2" class="form-label">Campo 2</label>
                                <input name="char2"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.char2" />
                                <div class="text-red-500"
                                     v-if="form.errors.char2">{{ __(form.errors.char2) }}</div>

                                <label for="char3" class="form-label">Campo 3</label>
                                <input name="char3"
                                       type="text"
                                       class="form-control mb-4"
                                       v-model="form.char3" />
                                <div class="text-red-500"
                                     v-if="form.errors.char3">{{ __(form.errors.char3) }}</div>

                            </div>
                            <div class="col">

                                <div class="alert alert-secondary !bg-gray-100 !pt-[18px]">

                                    <div class="row">
                                        <div class="col">

                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       v-model="form.b1"
                                                       true-value="1"
                                                       false-value="0"
                                                       name="b1"
                                                       id="b1">
                                                <label class="form-check-label" for="b1">B1</label>
                                            </div>

                                        </div>
                                        <div class="col">

                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       v-model="form.b2"
                                                       true-value="1"
                                                       false-value="0"
                                                       name="b2"
                                                       id="b2">
                                                <label class="form-check-label" for="b2">B2</label>
                                            </div>

                                        </div>
                                        <div class="col">

                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       v-model="form.b3"
                                                       true-value="1"
                                                       false-value="0"
                                                       name="b3"
                                                       id="b3">
                                                <label class="form-check-label" for="b3">B3</label>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <!-- END - MORE -->

                    </div>
                </div>

                <div class="text-right mt-8">

                    <a href="#"
                       onclick="window.history.back(); return false;"
                       class="btn btn-secondary w-[100px]">Annulla</a>

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
