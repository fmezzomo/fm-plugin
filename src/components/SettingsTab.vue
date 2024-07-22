<template>
  <div>
    <h2 class="wp-heading-inline">{{ __('Settings Tab', 'fm-plugin') }}</h2>
    <form @submit.prevent="saveSettings">

      <div id="fm-setting-row" class="fm-setting-row">
				<div class="fm-setting-label">
					<label>{{ __('Number of Rows', 'fm-plugin') }}</label>
				</div>
				<div class="fm-setting-field">
          <input type="number" id="numRows" v-model="localNumRows" min="1" max="5" />
					<p class="desc">{{ __('Type the number of rows you want to show.', 'fm-plugin') }}</p>
				</div>
			</div>

      <div id="fm-setting-row" class="fm-setting-row">
				<div class="fm-setting-label">
					<label>{{ __('Human date', 'fm-plugin') }}</label>
				</div>
				<div class="fm-setting-field">
          <input type="checkbox" id="showHumanDate" v-model="localShowHumanDate" />
        	<p class="desc">{{ __('Select to date column shows the date in human readable format than Unix timestamp.', 'fm-plugin') }}</p>
				</div>
			</div>

      <div id="fm-setting-row" class="fm-setting-row">
				<div class="fm-setting-label">
					<label>{{ __('Emails', 'fm-plugin') }}</label>
				</div>
				<div class="fm-setting-field">
          <email-component :emails="localEmails" @updateEmails="updateEmails"></email-component>
				</div>
			</div>

      <p class="fm-submit">
        <button type="submit" class="fm-btn fm-btn-orange">
          {{ __('Save Settings', 'fm-plugin') }}
        </button>
      </p>
    </form>
  </div>
</template>

<script>
import EmailComponent from './email/EmailComponent.vue';
import { mapActions } from 'vuex';

export default {
  components: { EmailComponent },
  name: 'SettingsTab',

  data() {
    return {
      localNumRows: this.$store.state.numRows,
      localShowHumanDate: this.$store.state.showHumanDate,
      localEmails: [...this.$store.getters.emails],
      adminEmail: fmPluginData.admin_email
    };
  },

  watch: {
    '$store.state.numRows'(newValue) {
      this.localNumRows = newValue;
    },
    '$store.state.showHumanDate'(newValue) {
      this.localShowHumanDate = newValue;
    },
    '$store.state.emails'(newEmails) {
      this.localEmails = [...newEmails];
    },
  },

  methods: {
    ...mapActions(['updateSettings']),

    validateEmails(emailArray) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailArray.every(email => emailRegex.test(email));
    },

    saveSettings() {

      if (!this.localNumRows || this.localNumRows < 1 || this.localNumRows > 5) {
        alert(fmPluginData.fmPluginTranslations.numberOfRowsAlert);
        return;
      }

      if (!this.validateEmails(this.localEmails)) {
        alert(fmPluginData.fmPluginTranslations.insertValidEmail);
        return;
      }

      const settingsData = {
        numRows: this.localNumRows,
        showHumanDate: this.localShowHumanDate,
        emails: this.localEmails,
      };

      this.updateSettings(settingsData)
        .then(() => {
        })
        .catch(error => {
          console.error('Error updating settings:', error);
        });
    },
    updateEmails(newEmails) {
      this.localEmails = newEmails;
    },
  },
};
</script>

<style scoped>

.fm-setting-row {
  border-bottom: 1px solid #e4e4e4;
  padding: 30px 0;
  font-size: 14px;
  line-height: 1.3;
}


.fm-setting-label {
  display: block;
  font-weight: 600;
  padding-top: 8px;
  display: block;
  float: left;
  width: 205px;
  padding: 0 20px 0 0;
}

.fm-setting-field {
  display: block;
  margin: 0 0 0 205px;
  max-width: 800px;
}

input[type=text] {
  background-color: #fff;
  border: 1px solid #999;
  border-radius: 4px;
  box-shadow: none;
  color: #555;
  display: inline-block;
  vertical-align: middle;
  padding: 7px 12px;
  margin: 0 10px 0 0;
  width: 400px;
  min-height: 35px;
  line-height: 1.3;
}

.fm-submit {
  margin: 0;
  padding: 25px 0;
}

</style>