<template>
    <table>
      <tr v-for="(email, index) in emails" :key="index">
        <td><input type="email" v-model="emails[index]" /></td>
        <td><button type="button" @click="removeEmail(index)" class="fm-btn fm-btn-red">{{ __('Remove', 'fm-plugin') }}</button></td>
      </tr>
      <tr>
        <td>
          <button type="button" @click="addEmail" :disabled="emails.length >= 5" class="fm-btn fm-btn-gray">{{ __('Add Email', 'fm-plugin') }}</button>
        </td>
      </tr>
    </table>
</template>

<script>

export default {
  name: 'EmailComponent',

  data() {
    return {
      adminEmail: fmPluginData.admin_email
    };
  },
  props: {
    emails: {
      type: [],
      required: true,
    },
  },

  methods: {
    addEmail() {
      if (this.emails.length < 5) {
        this.emails.push('');
      }
    },

    removeEmail(index) {
      this.emails.splice(index, 1);
      
      if (this.emails.length == 0) {
        this.emails.push(this.adminEmail);
      }
    },
  }
};
</script>