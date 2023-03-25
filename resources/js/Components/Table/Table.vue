<script setup>

defineProps({
    data: Object
});

</script>

<template>

    <table class="table">
        <thead>
        <tr>
            <template v-for="h in data.dataHeader">

                <th :class="h.class">
                    {{ h.label }}
                </th>

            </template>
        </tr>
        </thead>
        <tbody>
        <tr v-for="d in data.dataValue">
            <template v-for="field in data.dataBody">

                <td :class="field.class">
                    {{ d[field.field] }}
                </td>

            </template>
        </tr>
        </tbody>
    </table>

</template>
