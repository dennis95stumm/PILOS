<template>
  <div>
    <b-overlay :show="isBusy" z-index="100">
    <div class="row">
      <div class="col-12">

        <b-button-group class="float-lg-right">
          <can method="manageSettings" :policy="room">
          <!-- Add existing user from database -->
          <b-button
            variant="success"
            :disabled="isBusy"
            ref="add-member"
            @click="showAddMemberModal"
          >
            <i class="fas fa-user-plus"></i> {{ $t('rooms.members.addUser') }}
          </b-button>
          </can>

          <!-- Reload members list -->
          <b-button
            variant="dark"
            @click="reload"
            :disabled="isBusy"
            :title="$t('app.reload')"
            v-b-tooltip.hover
          >
            <i class="fas fa-sync"></i>
          </b-button>
        </b-button-group>
      </div>
    </div>
    <div class="row pt-4">
      <div class="col-12">
        <!-- table with all room members -->
        <b-table
          :current-page="currentPage"
          :per-page="settings('pagination_page_size')"
          :fields="tableFields"
          :items="members"
          hover
          stacked="lg"
          show-empty
        >
          <!-- message on no members -->
          <template v-slot:empty>
            <i>{{ $t('rooms.members.nodata') }}</i>
          </template>

          <!-- table data fetching spinner -->
          <template v-slot:table-busy>
            <div class="text-center my-2">
              <b-spinner class="align-middle"></b-spinner>
            </div>
          </template>

          <!-- action buttons -->
          <template v-slot:cell(actions)="data">
            <b-button-group class="float-md-right" v-if="currentUser && currentUser.id !== data.item.id" >
              <!-- edit membership role -->
              <b-button
                :disabled="isBusy"
                variant="dark"
                @click="showEditMemberModal(data.item)"
                v-b-tooltip.hover
                :title="$t('rooms.members.editUser')"
              >
                <i class="fas fa-user-edit"></i>
              </b-button>
              <!-- remove member -->
              <b-button
                :disabled="isBusy"
                variant="danger"
                @click="showRemoveMemberModal(data.item)"
                v-b-tooltip.hover
                :title="$t('rooms.members.deleteUser')"
              >
                <i class="fas fa-trash"></i>
              </b-button>
            </b-button-group>
          </template>

          <!-- render user profile image -->
          <template v-slot:cell(image)="data">
            <img :src="data.value ? data.value : '/images/default_profile.png'" class="profileImage" />
          </template>

          <!-- render user role -->
          <template v-slot:cell(role)="data">
            <b-badge v-if="data.value === 0" variant="primary">{{ $t('rooms.members.roles.guest') }}</b-badge>
            <b-badge
              class="text-white"
              v-if="data.value === 1"
              variant="success"
            >{{ $t('rooms.members.roles.participant') }}
            </b-badge>
            <b-badge v-if="data.value === 2" variant="danger"
            >{{ $t('rooms.members.roles.moderator') }}
            </b-badge>
            <b-badge v-if="data.value === 3" variant="dark"
            >{{ $t('rooms.members.roles.co_owner') }}
            </b-badge>
          </template>

        </b-table>
        <b-row>
          <b-col cols="12" class="my-1">
            <b-pagination
              v-if="members.length>settings('pagination_page_size')"
              v-model="currentPage"
              :total-rows="members.length"
              :per-page="settings('pagination_page_size')"
            ></b-pagination>
          </b-col>
        </b-row>
      </div>
    </div>

    </b-overlay>

    <!-- Modals -->

    <!-- edit user role modal -->
    <b-modal
      :static='modalStatic'
      :busy="isLoadingAction"
      ok-variant="success"
      :cancel-title="$t('rooms.members.modals.edit.cancel')"
      @ok="saveEditMember"
      ref="edit-member-modal"
      :no-close-on-esc="isLoadingAction"
      :no-close-on-backdrop="isLoadingAction"
      :hide-header-close="isLoadingAction"
    >
      <template v-slot:modal-title>
        <span v-if="editMember">
        {{ $t('rooms.members.modals.edit.title',{firstname: editMember.firstname,lastname: editMember.lastname}) }}
          </span>
      </template>
      <template v-slot:modal-ok>
        <b-spinner small v-if="isLoadingAction"></b-spinner>  {{ $t('rooms.members.modals.edit.save') }}
      </template>
      <div v-if="editMember">
        <b-form-group :label="$t('rooms.members.modals.edit.role')" v-if="editMember">
          <b-form-radio v-model.number="editMember.role" name="some-radios" value="1">
            <b-badge class="text-white" variant="success">{{ $t('rooms.members.roles.participant') }}</b-badge>
          </b-form-radio>
          <b-form-radio v-model.number="editMember.role" name="some-radios" value="2">
            <b-badge variant="danger">{{ $t('rooms.members.roles.moderator') }}</b-badge>
          </b-form-radio>
          <b-form-radio v-model.number="editMember.role" name="some-radios" value="3">
            <b-badge variant="dark">{{ $t('rooms.members.roles.co_owner') }}</b-badge>
          </b-form-radio>
        </b-form-group>
      </div>
    </b-modal>

    <!-- remove user modal -->
    <b-modal
      :busy="isLoadingAction"
      :static='modalStatic'
      ok-variant="danger"
      cancel-variant="dark"
      :cancel-title="$t('app.no')"
      @ok="confirmRemoveMember"
      ref="remove-member-modal"
      :no-close-on-esc="isLoadingAction"
      :no-close-on-backdrop="isLoadingAction"
      :hide-header-close="isLoadingAction"
    >
      <template v-slot:modal-title>
        {{ $t('rooms.members.modals.delete.title') }}
      </template>
      <template v-slot:modal-ok>
        <b-spinner small v-if="isLoadingAction"></b-spinner>  {{ $t('app.yes') }}
      </template>
      <span v-if="deleteMember">
        {{ $t('rooms.members.modals.delete.confirm',{firstname: deleteMember.firstname,lastname: deleteMember.lastname}) }}
      </span>

    </b-modal>

    <!-- add new user modal -->
    <b-modal
      :busy="isLoadingAction"
      :static='modalStatic'
      ok-variant="success"
      :cancel-title="$t('rooms.members.modals.add.cancel')"
      @ok="saveNewMember"
      ref="add-member-modal"
      :no-close-on-esc="isLoadingAction"
      :no-close-on-backdrop="isLoadingAction"
      :hide-header-close="isLoadingAction"
    >
      <template v-slot:modal-title>
        {{ $t('rooms.members.modals.add.title') }}
      </template>
      <template v-slot:modal-ok>
        <b-spinner small v-if="isLoadingAction"></b-spinner>  {{ $t('rooms.members.modals.add.add') }}
      </template>
      <!-- show server validation errors -->
      <b-alert v-if="createError" show variant="danger">{{ createError }}</b-alert>
      <!-- select user -->
      <b-form-group :label="$t('rooms.members.modals.add.user')" :state="newMemberValid">
        <multiselect v-model="newMember"
                     label="lastname"
                     track-by="id"
                     :placeholder="$t('rooms.members.modals.add.name')"
                     open-direction="bottom"
                     :options="users"
                     :multiple="false"
                     :searchable="true"
                     :loading="isLoadingSearch"
                     :internal-search="false"
                     :clear-on-select="false"
                     :preserveSearch="true"
                     :close-on-select="true"
                     :options-limit="300"
                     :max-height="600"
                     :show-no-results="true"
                     :showLabels="false"
                     @search-change="asyncFind">
          <template slot="noResult">{{ $t('rooms.members.modals.add.noResult') }}</template>
          <template slot="noOptions">{{ $t('rooms.members.modals.add.noOptions') }}</template>
          <template slot="option" slot-scope="props">{{ props.option.firstname }} {{ props.option.lastname }}<br><small>{{ props.option.email }}</small></template>
          <template slot="singleLabel" slot-scope="props">{{ props.option.firstname }} {{ props.option.lastname }}</template>
        </multiselect>
        <template slot='invalid-feedback'><div v-html="userValidationError"></div></template>
      </b-form-group>
      <!-- select role -->
      <b-form-group :label="$t('rooms.members.modals.add.role')" v-if="newMember" :state="newMemberRoleValid">
        <b-form-radio v-model.number="newMember.role" name="addmember-role-radios" value="1">
          <b-badge class="text-white" variant="success">{{ $t('rooms.members.roles.participant') }}</b-badge>
        </b-form-radio>
        <b-form-radio v-model.number="newMember.role" name="addmember-role-radios" value="2">
          <b-badge variant="danger">{{ $t('rooms.members.roles.moderator') }}</b-badge>
        </b-form-radio>
        <b-form-radio v-model.number="newMember.role" name="addmember-role-radios" value="3">
          <b-badge variant="dark">{{ $t('rooms.members.roles.co_owner') }}</b-badge>
        </b-form-radio>
        <template slot='invalid-feedback'><div v-html="roleValidationError"></div></template>
      </b-form-group>
    </b-modal>

  </div>
</template>
<script>
import Base from '../../api/base';
import Multiselect from 'vue-multiselect';
import _ from 'lodash';
import { mapGetters, mapState } from 'vuex';
import FieldErrors from '../../mixins/FieldErrors';
import env from '../../env';
import Can from '../Permissions/Can';
import PermissionService from '../../services/PermissionService';

export default {
  mixins: [FieldErrors],
  components: { Multiselect, Can },

  props: {
    room: Object, // room object
    modalStatic: {
      type: Boolean,
      default: false,
      required: false
    }
  },
  data () {
    return {
      isBusy: false, // table is fetching data from api
      newMember: null, // object user to be added as member
      users: [], // list of all found users
      isLoadingSearch: false, // is user search active
      isLoadingAction: false, // is user search active
      members: [], // list of all members
      createError: null, // error on adding new user as member
      editMember: null, // member to be edited
      deleteMember: null, // member to be deleted
      errors: {},
      currentPage: 1
    };
  },
  methods: {

    /**
     * Remove given member from member list
     */
    removeMemberItem: function (member) {
      this.members.splice(this.members.findIndex(item => item.id === member.id), 1);
    },

    /**
     * Search for users in database
     * @param query
     */
    asyncFind (query) {
      this.isLoadingSearch = true;

      Base.call('users/search?query=' + query).then(response => {
        // disable users that are already members of this room or the room owner
        const idOfMembers = this.members.map(user => user.id);
        this.users = response.data.data.map(user => {
          if (idOfMembers.includes(user.id) || this.room.owner.id === user.id) { user.$isDisabled = true; }
          return user;
        });
      }).catch((error) => {
        Base.error(error, this.$root);
      }).finally(() => {
        this.isLoadingSearch = false;
      });
    },

    /**
     * show modal to remove a member
     * @param member member object
     */
    showRemoveMemberModal: function (member) {
      this.deleteMember = member;
      this.$refs['remove-member-modal'].show();
    },

    /**
     * Remove a member
     */
    confirmRemoveMember: function (bvModalEvt) {
      // prevent modal from closing
      bvModalEvt.preventDefault();

      this.isLoadingAction = true;

      Base.call('rooms/' + this.room.id + '/member/' + this.deleteMember.id, {
        method: 'delete'
      }).then(response => {
        // remove user entry from list
        this.removeMemberItem(this.deleteMember);
      }).catch((error) => {
        if (error.response.status === env.HTTP_GONE) {
          this.removeMemberItem(this.deleteMember);
        }
        Base.error(error, this.$root);
      }).finally(() => {
        this.$refs['remove-member-modal'].hide();
        this.isLoadingAction = false;
      });
    },
    /**
     * show modal to edit user role
     * @param user user object
     */
    showEditMemberModal: function (member) {
      // Clone object to edit properties without displaying the changes in realtime in the members list
      this.editMember = _.cloneDeep(member);
      this.$refs['edit-member-modal'].show();
    },

    /**
     * show modal to add a new user as member
     */
    showAddMemberModal: function () {
      this.newMember = null;
      this.createError = null;
      this.users = [];
      this.$refs['add-member-modal'].show();
    },

    /**
     * save new user role
     */
    saveEditMember: function (bvModalEvt) {
      // prevent modal from closing
      bvModalEvt.preventDefault();

      this.isLoadingAction = true;

      Base.call('rooms/' + this.room.id + '/member/' + this.editMember.id, {
        method: 'put',
        data: { role: this.editMember.role }
      }).then(response => {
        // user role was saved
        this.members[this.members.findIndex(item => item.id === this.editMember.id)].role = this.editMember.role;
      }).catch((error) => {
        if (error.response.status === env.HTTP_GONE) {
          this.removeMemberItem(this.editMember);
        }
        Base.error(error, this.$root);
      }).finally(() => {
        this.$refs['edit-member-modal'].hide();
        this.isLoadingAction = false;
      });
    },
    /**
     * add a user as a room member
     * @param bvModalEvt
     */
    saveNewMember: function (bvModalEvt) {
      // prevent modal from closing
      bvModalEvt.preventDefault();

      this.isLoadingAction = true;

      // reset previous error messages
      this.createError = null;

      this.errors = {};

      // post new user as room members
      Base.call('rooms/' + this.room.id + '/member', {
        method: 'post',
        data: { user: this.newMember.id, role: this.newMember.role }
      }).then(response => {
        // operation successful, close modal and reload list
        this.$refs['add-member-modal'].hide();
        this.reload();
      }).catch((error) => {
        // adding failed
        if (error.response) {
          // failed due to form validation errors
          if (error.response.status === env.HTTP_UNPROCESSABLE_ENTITY) {
            this.errors = error.response.data.errors;
            return;
          }
        }
        this.$refs['add-member-modal'].hide();
        Base.error(error, this.$root);
      }).finally(() => {
        this.isLoadingAction = false;
      });
    },
    /**
     * reload member list from api
     */
    reload: function () {
      // enable data loading indicator
      this.isBusy = true;
      // make request to load users
      Base.call('rooms/' + this.room.id + '/member')
        .then(response => {
          // fetching successful
          this.members = response.data.data;
        })
        .catch((error) => {
          Base.error(error, this.$root);
        })
        .finally(() => {
          this.isBusy = false;
        });
    }
  },
  computed: {
    ...mapGetters({
      settings: 'session/settings'
    }),
    ...mapState({
      currentUser: state => state.session.currentUser
    }),

    // check if new user input field is valid, local and server-side check
    newMemberValid: function () {
      if (this.newMember == null || this.newMember.id == null || this.fieldState('user') === false) { return false; }
      return null;
    },
    // check if new user role input field is valid, local and server-side check
    newMemberRoleValid: function () {
      if ((this.newMember != null && this.newMember.role == null) || this.fieldState('role') === false) { return false; }
      return null;
    },
    // return error message for user, local or server-side
    userValidationError: function () {
      return this.fieldState('user') === false ? this.fieldError('user') : this.$t('rooms.members.modals.add.selectUser');
    },
    // return error message for role, local or server-side
    roleValidationError: function () {
      return this.fieldState('role') === false ? this.fieldError('role') : this.$t('rooms.members.modals.add.selectRole');
    },

    // member tables headings
    tableFields () {
      const fields = [
        {
          key: 'image',
          label: this.$t('rooms.members.image'),
          sortable: false,
          thClass: 'profileImageColumn'
        },
        {
          key: 'firstname',
          label: this.$t('rooms.members.firstname'),
          sortable: true
        },
        {
          key: 'lastname',
          label: this.$t('rooms.members.lastname'),
          sortable: true
        },
        {
          key: 'email',
          label: this.$t('rooms.members.email'),
          sortable: true
        },
        {
          key: 'role',
          label: this.$t('rooms.members.role'),
          sortable: true
        }];

      if (PermissionService.can('manageSettings', this.room)) {
        fields.push({
          key: 'actions',
          label: this.$t('rooms.members.actions'),
          thClass: 'actionColumn',
          tdClass: 'actionButton'
        });
      }

      return fields;
    }

  },
  watch: {
    'newMember.id': function () {
      this.errors = {};
    },
    'newMember.role': function () {
      this.errors = {};
    }
  },
  created () {
    // initial member fetch
    this.reload();
  }
};
</script>
