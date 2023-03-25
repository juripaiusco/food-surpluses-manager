<script setup>

import TableTh from "@/Components/Table/TableTh.vue";

defineProps({
    data: Object,
    filters: Object,
});

</script>

<template>

    <table class="table">
        <thead>
        <tr>
            <template v-for="struct in data.structure">

                <TableTh :struct="struct"
                         :filters="filters" />

            </template>
        </tr>
        </thead>
        <tbody>
        <tr v-for="d in data.data">
            <template v-for="struct in data.structure">

                <td :class="struct.class">
                    {{ d[struct.field] }}
                </td>

            </template>
        </tr>
        </tbody>
    </table>

</template>
