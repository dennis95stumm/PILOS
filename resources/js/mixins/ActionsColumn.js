import EventBus from '../services/EventBus';
import PermissionService from '../services/PermissionService';

export default {
  methods: {
    /**
     * Adds or removes the actions column to the field depending on the users permissions.
     */
    toggleActionsColumn () {
      if (PermissionService.currentUser && (this.actionPermissions.some(permission => PermissionService.currentUser.permissions.includes(permission)))) {
        if (this.tableFields[this.tableFields.length - 1].key !== 'actions') {
          this.tableFields.push({ key: 'actions', label: this.$t('app.actions') });
        }
      } else if (this.tableFields[this.tableFields.length - 1].key === 'actions') {
        this.tableFields.pop();
      }
    }
  },

  /**
   * Sets the event listener for current user change to re-evaluate whether the
   * action column should be shown or not.
   *
   * @method mounted
   * @return undefined
   */
  mounted () {
    EventBus.$on('currentUserChangedEvent', this.toggleActionsColumn);
    this.toggleActionsColumn();
  },

  /**
   * Removes the listener for current user change on destroy of this component.
   *
   * @method beforeDestroy
   * @return undefined
   */
  beforeDestroy () {
    EventBus.$off('currentUserChangedEvent', this.toggleActionsColumn);
  }
};
