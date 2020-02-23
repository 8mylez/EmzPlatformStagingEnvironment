import './page/emz-staging-environment-index';
import './page/emz-staging-environment-profile-index';
import './page/emz-staging-environment-profile-create';
import './page/emz-staging-environment-profile-detail';
import './page/emz-staging-environment-log-index';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('emz-staging-environment', {
    type: 'plugin',
    name: 'Staging',
    title: 'emz-staging-environment.general.mainMenuItemGeneral',
    description: 'emz-staging-environment.general.descriptionTextModule',
    color: '#009bd9',
    icon: 'default-device-server',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            component: 'emz-staging-environment-index',
            path: 'index'
        },
        profile_index: {
            component: 'emz-staging-environment-profile-index',
            path: 'profile/index'
        },
        profile_create: {
            component: 'emz-staging-environment-profile-create',
            path: 'profile/create',
            meta: {
                parentParth: 'emz.staging.environment.profile_index'
            }
        },
        profile_detail: {
            component: 'emz-staging-environment-profile-detail',
            path: 'profile/detail/:id',
            meta: {
                parentPath: 'emz.staging.environment.profile_index'
            }
        },
        log_index: {
            component: 'emz-staging-environment-log-index',
            path: 'log/index'
        }
    },

    navigation: [{
        id: 'emz-staging-environment',
        label: 'emz-staging-environment.general.mainMenuItemGeneral',
        color: '#009bd9',
        path: 'emz.staging.environment.index',
        icon: 'default-device-server',
        position: 100
    }, {
        path: 'emz.staging.environment.index',
        label: 'emz-staging-environment.general.mainMenuItemEnvironments',
        parent: 'emz-staging-environment'
    }, {
        path: 'emz.staging.environment.profile_index',
        label: 'emz-staging-environment.general.mainMenuItemProfiles',
        parent: 'emz-staging-environment'
    }, {
        path: 'emz.staging.environment.log_index',
        label: 'emz-staging-environment.general.mainMenuItemLogs',
        parent: 'emz-staging-environment'
    }]
});