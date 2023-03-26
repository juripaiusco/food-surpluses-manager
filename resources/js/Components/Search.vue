<script setup>

defineProps({
    placeholder: String,
    routeSearch: String,
    filters: Object
});

</script>

<template>

    <input name="s"
           :placeholder="placeholder"
           autocomplete="off"
           class="form-control"
           v-model="params.s"
           type="search" />

</template>

<script>
export default {
    data() {
        return {
            params: {
                s: this.filters.s,
            }
        }
    },
    watch: {
        params: {
            handler() {

                let params = this.params;

                if (this.filters.orderby && this.filters.ordertype) {
                    params.orderby = this.filters.orderby;
                    params.ordertype = this.filters.ordertype;
                }

                Object.keys(params).forEach(k => {
                    if (params[k] === '') {
                        delete params[k];
                    }
                })

                this.$inertia.get(this.route(this.routeSearch), params, { replace: true, preserveState: true});
            },
            deep: true,
        }
    }
}
</script>
