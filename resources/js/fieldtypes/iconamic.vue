<template>

    <div class="flex">
        <v-select
            ref="input"
            class="flex-1"
            :name="name"
            :clearable="config.clearable"
            :disabled="config.disabled || isReadOnly"
            :options="options"
            :placeholder="config.placeholder"
            :searchable="true"
            :multiple="false"
            :close-on-select="true"
            :value="this.value"
            :create-option="(value) => ({ value, label: value })"
            @input="vueSelectUpdated"
            @search:focus="$emit('focus')"
            @search:blur="$emit('blur')">
            <template slot="option" slot-scope="option">
                <span class="flex items-center">
                <span class="flex-none iconamic-is-svg block w-4 h-4" v-html="option.svg"></span>
                <span class="ml-2">{{ option.label }}</span>
                </span>
            </template>
            <template slot="selected-option" slot-scope="option">
                <span class="flex items-center">
                <span class="flex-none iconamic-is-svg block w-4 h-4" v-html="meta.icons[option.label]"></span>
                <span class="ml-2">{{ option.label }}</span>
                </span>
            </template>
        </v-select>
    </div>

</template>

<script>
export default {

    mixins: [Fieldtype],

    computed: {
        options() {
            let options = [];

            for (let svg in this.meta.icons) {
                options.push({
                    value: svg,
                    label: svg,
                    svg: this.meta.icons[svg]
                });
            }

            return options;
        }
    },

    methods: {
        focus() {
            this.$refs.input.focus();
        },

        vueSelectUpdated(value) {
            if (value) {
                this.update(value.value)
            } else {
                this.update(null);
            }
        }
    }

};
</script>
<style>
.iconamic-is-svg svg {
    width: 28px;
    height: 28px;
    object-fit: contain;
}
</style>
