<script setup>

import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ApplicationHeader from "@/Components/ApplicationHeader.vue";
import ApplicationContainer from "@/Components/ApplicationContainer.vue";
import {Link} from "@inertiajs/vue3";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({

    data: Object,
    modules_array: Object,
    retails_data: Object,

});

const form = useForm({

    id: props.data ? props.data.id : null,
    name: props.data ? props.data.name : null,
    email: props.data ? props.data.email : null,
    password: props.data ? props.data.password : null,
    modules: Object.keys(props.modules_array).map((v) => {
        return {[v]: props.data ? props.data['mod_' + v] : ''};
    }).reduce((json, value, key) => {
        json[Object.keys(value)] = Object.values(value)[0] === 'true' ? true : false;
        return json;
    }, {}),
    retails: props.retails_data.map((v) => {
        return {[v.id]: props.data ? props.data['retail_' + v.id] : ''};
    }).reduce((json, value, key) => {
        json[Object.keys(value)] = Object.values(value)[0] === 'true' ? true : false;
        return json;
    }, {})

});

</script>

<template>

    <Head title="Volontari" />

    <AuthenticatedLayout>

        <template #header>

            <ApplicationHeader :breadcrumb-array="['Volontari', data ? data.name : 'nuovo Volontario']" />

        </template>

        <ApplicationContainer>

            <h2 class="text-3xl mb-2">Dati Volontario</h2>

            <form @submit.prevent="form.post(route(
                form.id ? 'users.update' : 'users.store',
                form.id ? form.id : ''
                ))">

                <div class="row mb-4">
                    <div class="col">

                        <label for="name" class="form-label">Nome</label>

                        <input name="name"
                               type="text"
                               class="form-control"
                               v-model="form.name" />

                    </div>
                    <div class="col">

                        <label for="email" class="form-label">Email</label>

                        <input name="email"
                               type="text"
                               class="form-control"
                               v-model="form.email" />

                    </div>
                </div>

                <div :class="{'hidden': data}">

                    <label for="password" class="form-label">Password</label>

                    <input name="password"
                           type="password"
                           class="form-control"
                           v-model="form.password" />

                </div>

                <h2 class="text-3xl mb-2 mt-8">Moduli attivi</h2>

                <div class="row">

                    <div class="col col-lg-3 mb-2"
                         v-for="(mod, k) in modules_array">

                        <div class="form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   v-model="form.modules[k]"
                                   :id="k"
                                   :name="'modules[' + k + ']'" />
                            <label class="form-check-label"
                                   :for="k">{{ mod.title }}</label>
                        </div>

                    </div>

                </div>

                <h2 class="text-3xl mb-2 mt-8">
                    Negozi <span class="text-lg">(dove l'utente opera)</span>
                </h2>

                <div class="row">

                    <div class="col col-lg-3 mb-2"
                         v-for="(retail) in retails_data">

                        <div class="form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   v-model="form.retails[retail.id]"
                                   :id="'retail_' + retail.id"
                                   :name="'retails[' + retail.id + ']'" />
                            <label class="form-check-label"
                                   :for="'retail_' + retail.id">{{ retail.name }}</label>
                        </div>

                    </div>

                </div>

                <div class="text-right mt-8">

                    <Link :href="route('users.index')"
                          class="btn btn-secondary w-[100px]">Annulla</Link>

                    <button type="submit"
                            class="btn btn-success ml-2 w-[100px]">Salva</button>

                </div>

            </form>

        </ApplicationContainer>

    </AuthenticatedLayout>

</template>
