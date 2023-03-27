<script setup>

import TableTh from "@/Components/Table/TableTh.vue";

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
                    :class="struct.class">

                    {{ d[struct.field] }}
                </td>

            </template>
        </tr>
        </tbody>
    </table>

</template>
