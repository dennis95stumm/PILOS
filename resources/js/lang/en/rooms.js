export default {
  myRooms: 'My rooms',
  allRooms: 'All rooms',
  findRooms: 'Find rooms',
  findRoomsInfo: 'List of all public listed rooms without an access code',
  noRoomsAvailable: 'No rooms available',
  noRoomsAvailableSearch: 'No rooms found for this search query',
  rooms: 'Rooms',
  roomLimit: 'Room limit: {has}/{max}',
  sharedBy: 'Shared by {name}',
  sharedRooms: 'Rooms shared with me',
  start: 'Start',
  notRunning: 'This room is not started yet.',
  tryAgain: 'Try again',
  join: 'Join',
  recordingAttendanceInfo: 'The attendance in this room is logged.',
  recordingAttendanceAccept: 'I agree with the logging.',
  firstAndLastname: 'First- und last name',
  accessForParticipants: 'Access for participants',
  copyAccessForParticipants: 'Copy access for participants to clipboard',
  onlyUsedByAuthenticatedUsers: 'This room can only be used by authenticated users.',
  invalidPersonalLink: 'This personalised room link is invalid.',
  becomeMember: 'Become member',
  endMembership: {
    button: 'End membership',
    title: 'Are you sure you want to end the membership?',
    message: 'You will lose the role associated with your membership. To become a member again, you may need to ask the room owner. If the room has an access code, you will need to know it to continue to have access to the room.',
    yes: 'Yes, end membership',
    no: 'No, keep membership'
  },
  requireAccessCode: 'An access code is required to join this room',
  login: 'Login',
  placeholderName: 'John Doe',
  placeholderAccessCode: 'Access code',
  roomTypeInvalidAlert: 'The usage of the room type {roomTypeName} is only permitted for special user groups. If you are the owner of this room, please change the room type so that the room can be started again.',
  flash: {
    noNewRoom: {
      message: 'You do not have the necessary permission to create a new room.',
      title: 'Permission denied'
    },
    startForbidden: {
      message: 'The room could not be started by you.',
      title: 'Starting failed'
    },
    accessCodeInvalid: {
      message: 'The access code is invalid.',
      title: 'Login to room failed'
    },
    tokenInvalid: {
      message: 'The personalised room link is no longer valid.',
      title: 'Room link invalid'
    },
    fileForbidden: {
      message: 'The access to the requested file was forbidden.',
      title: 'Access forbidden'
    },
    fileGone: {
      message: 'The file has been deleted in the meantime.',
      title: 'File not found'
    }
  },
  modals: {
    delete: {
      title: 'Delete room',
      confirm: 'Should the room \'{name}\' be deleted?'
    }
  },
  create: {
    title: 'Create new room',
    ok: 'Create',
    cancel: 'Cancel'
  },
  files: {
    title: 'Files',
    nodata: 'No files available',
    filename: 'Filename',
    actions: 'Actions',
    default: 'Default',
    downloadable: 'Downloadable',
    uploadedAt: 'Uploaded at',
    useInNextMeeting: 'Use in the next meeting',
    selectordrag: 'Select a file or drag and drop it here...',
    view: 'View file',
    delete: 'Delete file',
    modals: {
      delete: {
        title: 'Delete file',
        confirm: 'Do you want to delete this file {filename}?'
      }
    },
    formats: 'Allowed file formats: {formats}',
    size: 'Max. file size: {size} MB',
    termsOfUse: {
      title: 'Terms of Use',
      content: 'Files that can be downloaded here are for personal study only. The files, or parts of them, may not be shared or distributed.',
      accept: 'I accept the terms of use'
    }
  },
  invitation: {
    room: 'Join \'{roomname}\' with PILOS',
    link: 'Link: {link}',
    code: 'Access code: {code}'
  },
  members: {
    title: 'Members',
    nodata: 'No members available',
    addUser: 'Add user',
    inviteGuest: 'Invite guest',
    image: 'Picture',
    firstname: 'Firstname',
    lastname: 'Lastname',
    email: 'Email',
    role: 'Role',
    actions: 'Actions',
    roles: {
      guest: 'Guest',
      participant: 'Participant',
      moderator: 'Moderator',
      co_owner: 'Co-owner'
    },
    editUser: 'Edit member',
    deleteUser: 'Delete member',
    modals: {
      edit: {
        title: 'Edit {firstname} {lastname}',
        save: 'Save',
        cancel: 'Cancel',
        role: 'Role'
      },
      add: {
        title: 'Add user',
        add: 'Add',
        cancel: 'Cancel',
        user: 'User',
        selectUser: 'Please select the user you would like to add',
        name: 'Name',
        noOptions: 'No entries, please search for a user.',
        noResult: 'Oops! No user was found for this query.',
        role: 'Role',
        selectRole: 'Please select a role'
      },
      delete: {
        title: 'Remove member from this room',
        confirm: 'Do you want to remove {firstname} {lastname} from this room?'
      }
    }
  },
  meetingHistory: {
    title: 'History'
  },
  settings: {
    title: 'Settings',
    nonePlaceholder: '-- none --',
    saving: 'saving ...',
    general: {
      title: 'General',
      type: 'Type',
      roomName: 'Room name',
      welcomeMessage: 'Welcome message',
      maxDuration: 'Max. duration',
      resetDuration: 'Reset duration',
      minutes: 'min.',
      chars: 'Characters: {chars}'
    },
    security: {
      title: 'Security',
      unprotectedPlaceholder: '-- unprotected --',
      accessCode: 'Access code',
      generateAccessCode: 'Create new access code',
      deleteAccessCode: 'Remove access code',
      accessCodeNote: 'Access restriction for joining the room and room membership (if enabled).',
      allowGuests: 'Allow guests',
      allowNewMembers: 'Allow new members',
      listed: 'Include in room search'
    },
    participants: {
      title: 'Participants',
      maxParticipants: 'Max. participants',
      clearMaxParticipants: 'Reset max. participants',
      defaultRole: {
        title: 'Default role',
        onlyLoggedIn: '(only for authenticated users)',
        participant: 'Participant',
        moderator: 'Moderator'
      },
      waitingRoom: {
        title: 'Waiting room',
        disabled: 'Disabled',
        enabled: 'Enabled',
        onlyForGuestsEnabled: 'Enabled only for guests'
      },
      recordAttendance: 'Log attendance of participants'
    },
    permissions: {
      title: 'Permissions',
      everyoneStart: 'Everyone can start the meeting',
      muteMic: 'Mute microphone on join'
    },
    restrictions: {
      title: 'Restrictions',
      enabled: 'Enabled restrictions',
      disableCam: 'Disable webcam',
      onlyModSeeCam: 'Only moderators can see the webcam',
      disableMic: 'Disable microphone',
      disablePublicChat: 'Disable public chat',
      disablePrivateChat: 'Disable private chat',
      disableNoteEdit: 'Disable editing of notes',
      hideParticipantsList: 'Hide list of participants'
    }
  },

  filter: {
    title: 'Filter',
    roomTypes: 'Room types',
    apply: 'Apply'
  },

  tokens: {
    nodata: 'No personalized room links available!',
    title: 'Personalized room links',
    firstname: 'Firstname',
    lastname: 'Lastname',
    role: 'Role',
    expires: 'Expiry date',
    lastUsage: 'Last used',
    add: 'Add personalized room link',
    edit: 'Edit personalized room link',
    delete: 'Delete personalized room link',
    copy: 'Copy personalized room link to clipboard',
    roomLinkCopied: 'The personalized room link for {firstname} {lastname} was copied to your clipboard.',

    modals: {
      delete: {
        title: 'Delete personalized room link',
        confirm: 'Do you really want to delete the personalized room link for {firstname} {lastname}?'
      }
    },

    roles: {
      participant: 'Participant',
      moderator: 'Moderator'
    }
  },
  roomTypes: {
    loadingError: 'An error occurred during loading of the room types.',
    selectType: '-- Select room type --',
    reload: 'Reload room types'
  },

  notification: {
    enable: 'Notify on room start',
    enabled: 'You will be notified by your browser when the room starts. Do not close this window/tab.',
    body: 'The room was started at {time}',

    denied: {
      message: 'The browser denied notifications.',
      title: 'No permission'
    },

    browserSupport: {
      message: 'Your browser does not support notification.',
      title: 'Missing support'
    }
  }

};
