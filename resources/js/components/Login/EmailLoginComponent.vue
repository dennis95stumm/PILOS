<template>
  <div>
    <h5>{{title}}</h5>
    <b-form @submit.prevent="submit">
      <b-form-group :label="emailLabel" :label-for="`${id}Email`">
        <b-form-input
          :id="`${id}Email`"
          v-model="email"
          type="email"
          required
          :placeholder="emailLabel"
          :state="errors !== null && errors.email && errors.email.length > 0 ? false: null"
        ></b-form-input>

        <b-form-invalid-feedback v-if="errors !== null && errors.email.length > 0">
          <template v-for="error in errors.email">
            {{ error }}
          </template>
        </b-form-invalid-feedback>
      </b-form-group>

      <b-form-group :label="passwordLabel" :label-for="`${id}Password`">
        <b-form-input
          :id="`${id}Password`"
          v-model="password"
          type="password"
          required
          :placeholder="passwordLabel"
          :state="errors !== null && errors.password && errors.password.length > 0 ? false: null"
          aria-describedby="passwordHelpBlock"
        ></b-form-input>

        <b-form-text id="passwordHelpBlock">
          <router-link to="/forgot_password" v-if="settings('password_self_reset_enabled')">
            {{ $t('auth.forgotPassword') }}
          </router-link>
        </b-form-text>

        <b-form-invalid-feedback v-if="errors !== null && errors.password && errors.password.length > 0">
          <template v-for="error in errors.password">
            {{ error }}
          </template>
        </b-form-invalid-feedback>
      </b-form-group>

      <b-button type="submit" variant="success" :disabled="loading" block >
        <b-spinner v-if="loading" small></b-spinner>
        {{submitLabel}}
      </b-button>
    </b-form>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  props: [
    'errors',
    'id',
    'loading',
    'passwordLabel',
    'submitLabel',
    'title',
    'emailLabel'
  ],
  computed: {
    ...mapGetters({
      settings: 'session/settings'
    })
  },
  data () {
    return {
      email: '',
      password: ''
    };
  },
  methods: {
    submit () {
      this.$emit('submit', {
        id: this.id,
        data: {
          email: this.email,
          password: this.password
        }
      });
    }
  }
};
</script>

<style scoped>

</style>
