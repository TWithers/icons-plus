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
    <v-select
        v-show="! value"
        ref="input"
        class="w-full"
        clearable
        :name="name"
        :disabled="config.disabled || isReadOnly"
        :options="paginated"
        :placeholder="__(config.placeholder || 'Search...')"
        :searchable="true"
        :value="value"
        :multiple="false"
        :close-on-select="true"
        :filterable="false"
        @input="vueSelectUpdated"
        @open="onOpen"
        @close="onClose"
        @search="onSearch"
        @search:focus="$emit('focus')"
        @search:blur="$emit('blur')">
      <template v-slot:no-options class="col-span-3 text-center p-3">
        <div v-show="searchTerm.length === 0" class="py-3">
          Type to search for icons!
        </div>
        <div v-show="searchTerm.length !== 0" class="py-3">
          No results exist!
        </div>

      </template>
      <template v-slot:option="option">
        <div class="flex items-center justify-center flex-col">
          <img :src='option.src' class="w-12 h-12"/>
          {{ option.label }}
        </div>
      </template>
      <template v-slot:list-footer>
        <li v-show="hasNextPage" ref="loadMore" class="loader col-span-3 text-center italic text-gray-600 text-sm p-3" style="grid-column: span 3 / span 3;">
          Loading more icons...
        </li>
      </template>
    </v-select>
  </div>
</template>

<script>

export default {

  mixins: [Fieldtype],
  data() {
    return {
      observer: null,
      options: [],
      results: [],
      limit: 30,
      searchTerm: '',
    }
  },

  computed: {
    selectedOption() {
      return this.results?.find(option => option.value === this.value);
    },
    paginated() {
      return this.results.slice(0, this.limit);
    },
    hasNextPage() {
      return this.paginated.length < this.results.length;
    }

  },
  mounted() {
    this.observer = new IntersectionObserver(this.infiniteScroll);
  },

  methods: {
    async onSearch(search, loading) {
      this.searchTerm = search;
      if (search.length) {
        loading(true);
        this.search(loading, search, this);
      }
    },
    async onOpen() {
      if (this.hasNextPage) {
        await this.$nextTick()
        this.observer.observe(this.$refs.loadMore)
      }
    },
    onClose() {
      this.observer.disconnect()
    },
    search: _.debounce(async (loading, search, vm) => {
      vm.keyword = null;
      const query = new URLSearchParams();
      query.set('query', search);
      query.set('limit', 999);
      if (vm.config.icon_group_restrictions.length){
        query.set('prefixes', vm.config.icon_group_restrictions.join(','));
      }

      return fetch(
          `https://api.iconify.design/search?${query.toString()}`
      ).then(async (res) => {
        res.json().then(json => {
          vm.results = json.icons.map(i => ({
            src: `https://api.iconify.design/${i.split(':')[0]}/${i.split(':')[1]}.svg`,
            value: i,
            label: i,
            icon: i,
          }));
        }).then(async () => {
          if (vm.hasNextPage) {
            await vm.$nextTick()
            vm.observer.observe(vm.$refs.loadMore)
          }
        });
        loading(false);
      });
    }, 350),
    async infiniteScroll([{ isIntersecting, target }]) {
      if (isIntersecting) {
        const ul = target.offsetParent
        const scrollTop = target.offsetParent.scrollTop
        this.limit += 30
        await this.$nextTick()
        ul.scrollTop = scrollTop
      }
    },
    focus() {
      this.$refs.input.focus();
    },

    vueSelectUpdated(value) {
      if (value) {
        if (this.config.storage_options === 'both' || this.config.storage_options === 'url') {
          this.update({
            iconPrefix: value.value.split(":")[0],
            iconName: value.value.split(":")[1],
            iconUrl: value.src,
            iconSvg: null,
            label: value.value
          });
        }

        if (this.config.storage_options === 'both' || this.config.storage_options === 'svg') {
          fetch(`${value.src}?width=unset&height=unset`).then(res => res.text()).then(svg => {
            this.update({
              iconPrefix: value.value.split(":")[0],
              iconName: value.value.split(":")[1],
              iconUrl: (this.config.storage_options === 'both' ? value.src : null),
              iconSvg: svg,
              label: value.value
            });
          });
        }
      } else {
        this.update(null);
      }
    },
  }
};
</script>
<style lang="postcss">
.icons-plus .vs__dropdown-menu {
  @apply grid grid-cols-3 gap-2;
}
</style>
