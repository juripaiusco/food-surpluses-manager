<script setup>

import TableTh from "@/Components/Table/TableTh.vue";
import {Link} from "@inertiajs/vue3";

defineProps({
    data: Object,
});

</script>

<template>

    <table class="table">
        <thead>
        <tr>
            <template v-for="struct in data.structure">

                <TableTh :struct="struct"
                         :data="data" />

            </template>
        </tr>
        </thead>
        <tbody>
        <tr v-for="d in data.data">
            <template v-for="struct in data.structure">

                <td class="align-middle"
                    :class="struct.class, {
                        'text-center': (struct.btnEdit === true || struct.btnDel === true)
                    }">

                    {{ d[struct.field] }}

                    <template v-if="struct.btnEdit === true || struct.btnDel === true">

<<<<<<< HEAD
                            <Link class="btn btn-sm"
                                  :href="struct.btnDel === true ? '#' : route(struct.route, d.id)"
                                  :data-bs-toggle="struct.btnDel === true ? 'modal' : ''"
                                  :data-bs-target="struct.btnDel === true ? '#deleteModal' : ''"
                                  @click="struct.btnDel === true ? passDataToModal(struct, d) : ''"
                                  :class="{
=======
                        <Link class="btn btn-sm"
                              :href="struct.btnDel === true ? '#' : route(struct.route, d.id)"
                              :class="{
>>>>>>> parent of 7e911bd (Delete modal request)
                                'btn-warning': struct.btnEdit === true,
                                'btn-danger': struct.btnDel === true
                              }">

                            <svg v-if="struct.btnEdit === true"
                                 class="w-4 h-4"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
<<<<<<< HEAD
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi eliminare?
                    </div>
                    <div class="modal-footer">
                        <div class="inline-flex w-full">
                            <div class="w-1/2 mr-1">

                                <button type="button"
                                        class="btn btn-secondary w-full"
                                        data-bs-dismiss="modal">
                                    No
                                </button>

                            </div>
                            <div class="w-1/2 ml-1">

<!--                                <Link class="btn btn-danger w-full"
                                      :href="structModal.route ? route(structModal.route, dataModal.id) : '#'">
                                    Sì, elimina
                                </Link>-->

                                <button class="btn btn-danger w-full"
                                        @click="">
                                    Sì, elimina
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
=======
>>>>>>> parent of 7e911bd (Delete modal request)

                            <svg v-if="struct.btnDel === true"
                                 class="w-4 h-4"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>

                        </Link>

                    </template>

                </td>

            </template>
        </tr>
        </tbody>
    </table>

</template>

<script>
export default {
    data () {
        return {
            structModal: Object,
            dataModal: Object,
        }
    },
    methods: {
        passDataToModal(struct, d) {
            this.dataModal = d;
            this.structModal = struct;
        },
        modalHide() {
            let deleteModal = document.getElementById('deleteModal');
        }
    }
}
</script>
