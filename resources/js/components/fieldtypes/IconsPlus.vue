<template>
  <div class="flex icons-plus">

    <div class="flex flex-col gap-2 justify-center items-center" v-if="value">
      <div class="flex w-full justify-end">
        <button @click="update(null)" class="text-red-500"><img src="https://api.iconify.design/bx/x.svg?color=%23ba3329" alt="delete" class="h-4 w-4"></button>
      </div>
      <img :src="value.iconUrl" class="w-24 h-24" v-if="value.iconUrl"/>
      <div v-else v-html="value.iconSvg" class="w-24 h-24"></div>
      <div class="text-center text-sm">{{ value.label }}</div>
    </div>

    <Combobox
        v-show="! value"
        ref="input"
        class="w-full"
        clearable
        ignore-filter
        :disabled="config.disabled || isReadOnly"
        :options="results"
        :placeholder="__(config.placeholder || 'Search...')"
        :searchable="true"
        :model-value="null"
        :multiple="false"
        @update:model-value="comboboxUpdated"
        @search="onSearch">
      <template #no-options>
        <div v-if="loading" class="py-3 text-center italic text-gray-600 text-sm">
          Loading icons...
        </div>
        <div v-else-if="searchTerm.length === 0" class="py-3 text-center">
          Type to search for icons!
        </div>
        <div v-else class="py-3 text-center">
          No results exist!
        </div>
      </template>
      <template #option="option">
        <img :src="option.src" class="size-7" />
        <span v-text="option.label" />
      </template>
    </Combobox>
  </div>
</template>

<script>
import { FieldtypeMixin as Fieldtype } from '@statamic/cms';
import { Combobox } from '@statamic/cms/ui';

function debounce(fn, wait) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn(...args), wait);
  };
}

export default {

  components: { Combobox },

  mixins: [Fieldtype],

  data() {
    return {
      results: [],
      searchTerm: '',
      loading: false,
    }
  },

  created() {
    this.debouncedSearch = debounce((search) => {
      const query = new URLSearchParams();
      query.set('query', search);
      query.set('limit', 999);
      if (this.config.icon_group_restrictions.length) {
        query.set('prefixes', this.config.icon_group_restrictions.join(','));
      }

      fetch(`https://api.iconify.design/search?${query.toString()}`)
        .then(res => res.json())
        .then(json => {
          this.results = json.icons.map(i => ({
            src: `https://api.iconify.design/${i.split(':')[0]}/${i.split(':')[1]}.svg`,
            value: i,
            label: i,
          }));
          this.loading = false;
        });
    }, 350);
  },

  methods: {
    onSearch(search) {
      this.searchTerm = search;

      if (! search.length) {
        this.loading = false;
        this.results = [];
        return;
      }

      this.loading = true;
      this.debouncedSearch(search);
    },

    focus() {
      this.$refs.input.focus();
    },

    comboboxUpdated(value) {
      if (! value) {
        this.update(null);
        return;
      }

      const option = this.results.find(o => o.value === value);

      if (! option) {
        return;
      }

      if (this.config.storage_options === 'both' || this.config.storage_options === 'url') {
        this.update({
          iconPrefix: value.split(":")[0],
          iconName: value.split(":")[1],
          iconUrl: option.src,
          iconSvg: null,
          label: value
        });
      }

      if (this.config.storage_options === 'both' || this.config.storage_options === 'svg') {
        fetch(`${option.src}?width=unset&height=unset`).then(res => res.text()).then(svg => {
          this.update({
            iconPrefix: value.split(":")[0],
            iconName: value.split(":")[1],
            iconUrl: (this.config.storage_options === 'both' ? option.src : null),
            iconSvg: svg,
            label: value
          });
        });
      }
    },
  }
};
</script>
