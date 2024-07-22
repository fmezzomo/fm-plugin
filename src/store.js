import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    numRows: 5,
    showHumanDate: true,
    dataTable: [],
    dataGraph: [],
    emails: [],
  },

  mutations: {
    setNumRows(state, numRows) {
      state.numRows = numRows;
    },
    setShowHumanDate(state, showHumanDate) {
      state.showHumanDate = showHumanDate;
    },
    setEmails(state, emails) {
      state.emails = emails;
    },
    setDataTable(state, table) {
      state.dataTable = table;
    },
    async setDataGraph(state, graph) {
      state.dataGraph = graph;
    },
  },

  getters: {
    numRows: state => state.numRows,
    showHumanDate: state => state.showHumanDate,
    emails: state => state.emails,
  },

  actions: {
    async fetchData({ commit }) {
        await fetch(fmPluginData.api_url + '/data')
        .then(response => response.json())
        .then(data => {

            let newGraph = Object.keys(data.graph).map(key => ({
                date: data.graph[key].date,
                value: data.graph[key].value
              }));

            commit('setDataTable', data.table);
            commit('setDataGraph', newGraph);
        })
        .catch(error => {
          console.error('Error fetching table data:', error);
        });
    },
    fetchSettings({ commit }) {
      fetch(fmPluginData.api_url + '/all-settings')
        .then(response => response.json())
        .then(settings => {
            commit('setNumRows', settings.numRows);
            commit('setShowHumanDate', settings.showHumanDate);

            if(settings.emails.lenght == 0) {
                settings.emails.push(fmPluginData.admin_email);
            }

            commit('setEmails', settings.emails);
        })
        .catch(error => {
            console.error('Error fetching settings:', error);
        });
    },
    updateSettings({ commit }, data) {

      // Clean all empty emails
      // In case email list is empty then add Admin Email
      const newListEmails = data.emails.filter(Boolean);
      if(newListEmails.length == 0) {
        newListEmails.push(fmPluginData.admin_email);
      }
      data.emails = newListEmails;

      const headers = new Headers({
        'Content-Type': 'application/json',
      });

      return fetch(fmPluginData.api_url + '/settings', {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(data),
      })
        .then(response => response.json())
        .then(result => {
          alert(result.message);
          
          if(result.success) {
            commit('setNumRows', data.numRows);
            commit('setShowHumanDate', data.showHumanDate);
            commit('setEmails', data.emails);
          }

          return result;
        })
        .catch(error => {
            alert('Error fetching settings:', error);
        });
    },
  },
});