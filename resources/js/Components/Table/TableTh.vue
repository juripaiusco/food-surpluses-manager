<script setup>

defineProps({
    struct: Object,
    filters: Object,
})

</script>

<template>

    <th :class="struct.class">

        <div class="inline-flex cursor-pointer">

            <div class="mr-2">
                {{ struct.label }}
            </div>

<!--            <div class="w-[12px] h-[12px]"
                 :class="{ invisible: params.orderby != field }">
                <svg :class="{ 'text-gray-300': params.ordertype === 'desc' }"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
                <svg :class="{ 'text-gray-300': params.ordertype === 'asc' }"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </div>-->

        </div>

    </th>

</template>

<script>

export default {
    data() {
        return {
            params: {
                /*orderby: this.filters.orderby,
                ordertype: this.filters.ordertype,*/
            }
        }
    },
}

</script>
