import IconamicField from './fieldtypes/iconamic.vue';
import IconamicIndexField from './fieldtypes/iconamic_index.vue';

Statamic.booting(() => {
    Statamic.$components.register('iconamic-fieldtype', IconamicField);
    Statamic.$components.register('iconamic-fieldtype-index', IconamicIndexField);
});
