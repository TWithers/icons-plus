import Fieldtype from './components/fieldtypes/IconsPlus.vue';

Statamic.booting(() => {
    Statamic.$components.register('icons_plus-fieldtype', Fieldtype)
});