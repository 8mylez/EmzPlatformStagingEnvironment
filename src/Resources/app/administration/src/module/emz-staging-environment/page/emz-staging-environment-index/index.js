import template from './emz-staging-environment-index.html.twig';

const { Component, Data } = Shopware;
const { Criteria } = Data;

Component.register('emz-staging-environment-index', {
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
            environments: null
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('emz_pse_environment');

        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then(result => {
                this.environments = result;

                console.log('this.environments', this.environments);
            });
    },

    computed: {
        columns() {
            return [{
                property: 'environmentName',
                dataIndex: 'environmentName',
                label: this.$t('emz-staging-environment.list.columnEnvironmentName'),
                routerLink: 'emz.staging.environment.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'comment',
                dataIndex: 'comment',
                label: this.$t('emz-staging-environment.list.columnComment'),
                inlineEdit: 'string',
                allowResize: true,
            }, {
                property: 'folderName',
                dataIndex: 'folderName',
                label: this.$t('emz-staging-environment.list.columnAccessLinks'),
                inlineEdit: 'string',
                allowResize: true,
            }];
        }
    },

    methods: {

    }
});