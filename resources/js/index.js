import IconamicField from './fieldtypes/iconamic.vue';

Statamic.booting(() => {
    Statamic.$components.register('iconamic-fieldtype', IconamicField);
});
