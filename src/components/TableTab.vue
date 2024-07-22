<template>
  <div v-if="dataTable">
    <h2 class="wp-heading-inline">{{  __(dataTable.title, 'fm-plugin') }}</h2>
    <table class="wp-list-table widefat fixed striped">
      <thead>
        <tr>
          <th v-for="header in dataTable.headers" :key="header">{{ __(header, 'fm-plugin') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, index) in displayedRows" :key="row.id + '-' + index">
          <td>{{ row.id }}</td>
          <td>{{ row.url }}</td>
          <td>{{ row.title }}</td>
          <td>{{ row.pageviews }}</td>
          <td>{{ formatDate(row.date) }}</td>
        </tr>
      </tbody>
    </table>
    <h2 class="wp-heading-inline">{{ __('Emails', 'fm-plugin') }}</h2>
    <table class="wp-list-table widefat fixed striped">
      <tbody>
        <tr v-for="(email, index) in emails" :key="index">
          <td>{{ email }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div v-else>{{ __('Loading graph data...', 'fm-plugin') }}</div>
</template>

<script>

import { mapState } from 'vuex';

export default {
  name: 'TableTab',

  computed: {
    ...mapState(['dataTable', 'emails', 'numRows', 'showHumanDate']),

    displayedRows() {
      return this.dataTable?.data?.rows?.slice(0, this.numRows) || [];
    },
  },

  methods: {

    formatDate(date) {
      if (this.showHumanDate) {
        return new Date(date * 1000).toLocaleString();
      } else {
        return date;
      }
    },
  },
};
</script>
