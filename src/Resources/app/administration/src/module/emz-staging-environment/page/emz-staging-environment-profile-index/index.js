import template from './emz-staging-environment-profile-index.html.twig';

const { Component, Data } = Shopware;
const { Criteria } = Data;

Component.register('emz-staging-environment-profile-index', {
    template,

    metaInfo() {
        return {
            title: this.$createTitle()
        }
    },

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            repository: null,
            profiles: null
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('emz_pse_profile');

        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then(result => {
                this.profiles = result;
            });
    },

    computed: {
        columns() {
            return [{
                property: 'profileName',
                dataIndex: 'profileName',
                label: this.$t('emz-staging-environment.list.columnProfileName'),
                routerLink: 'emz.staging.environment.profile_detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'comment',
                dataIndex: 'comment',
                label: this.$t('emz-staging-environment.list.columnComment'),
                inlineEdit: 'string',
                allowResize: true,
            }];
        }
    }
});