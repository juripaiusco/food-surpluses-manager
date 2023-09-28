<script setup>

const props = defineProps({
    data: Object
});

</script>

<template>

    <div v-if="data" class="border-2 rounded-md p-6 mb-6">

        <div class="inline-flex items-center w-full">

            <div class="text-center align-middle w-1/2">

                <span class="text-3xl">
                    {{ data.count == 0 ? '-' : data.count }}
                </span>
                <br>
                {{ data.label }}

            </div>
            <div class="text-center align-middle w-1/2">

                <div class="w-[60px] h-[60px] m-auto">
                    <slot />
                </div>

            </div>

        </div>

    </div>

</template>
