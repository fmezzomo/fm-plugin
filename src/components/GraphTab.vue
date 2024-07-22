<template>
  <div class="graph-tab">
    <h2>{{ __('Graph Tab', 'fm-plugin') }}</h2>
   <Bar
    v-if="isGraphDataLoaded"
    id="my-chart-id"
    :options="chartOptions"
    :data="chartData"
  />
    <p v-else>{{ __('Loading graph data...', 'fm-plugin') }}</p>
  </div>
</template>

<script>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)


export default {
  name: 'GraphTab',
  components: { Bar },
  
  data() {
    return {
      chartData: {
        labels: [],
        datasets: [
          {
            label: 'Graph Data',
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            data: [],
          },
        ],
      },
      chartOptions: {
        responsive: true
      },
      isGraphDataLoaded: false,
    };
  },
  created() {
    this.$store.dispatch('fetchData').then(() => {
      this.updateChartData();
    });
  },
  computed: {
    graphData() {
      return this.$store.state.dataGraph;
    },
  },
  watch: {
      immediate: true,
      handler: 'updateChartData',
  },
  methods: {
    updateChartData() {

      this.chartData.labels = this.graphData.map(item => this.formatDate(item.date));
      this.chartData.datasets[0].data = this.graphData.map(item => item.value);

      this.isGraphDataLoaded = true;
    },
    formatDate(date) {
        return new Date(date * 1000).toLocaleString();
    },
  },
};
</script>

<style scoped>
.graph-tab {
  padding: 20px;
}
</style>
