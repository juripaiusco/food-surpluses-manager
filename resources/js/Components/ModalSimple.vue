<script setup>

import {Link} from "@inertiajs/vue3";

const props = defineProps({
    data: Object,
    confirm: String,
    show: Boolean
});

</script>

<template>

    <Transition name="bounce">

        <div v-if="show"
             class="bg-black fixed w-full h-full top-0 left-0 bg-opacity-30">

            <div class="mt-[10%] w-1/3 bg-white border border-gray-500 p-6 m-auto rounded-lg shadow-md shadow-gray-500">

                <div class="inline-flex w-full">

                    <div class="w-1/2 text-xl font-semibold">

                        <slot name="title" />

                    </div>
                    <div class="w-1/2 text-right">

                        <button class="ml-auto"
                                @click="$emit('closeModal')">

                            <svg class="w-6 h-6"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>

                        </button>

                    </div>

                </div>

                <div class="text-center mt-8 mb-10">

                    <slot name="body" />

                </div>

                <div class="inline-flex w-full mt-1">
                    <div class="w-1/2">

                        <button class="btn btn-secondary w-full mr-2"
                                @click="$emit('closeModal')">No</button>

                    </div>
                    <div class="w-1/2">

                        <Link class="btn btn-danger w-full ml-2"
                              :href="confirm">
                            Sì, elimina
                        </Link>

                    </div>
                </div>

            </div>

        </div>

    </Transition>

</template>

<style>
.bounce-enter-active {
    animation: bounce-in 0.2s;
}
.bounce-leave-active {
    animation: bounce-in 0.2s reverse;
}
@keyframes bounce-in {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
</style>
