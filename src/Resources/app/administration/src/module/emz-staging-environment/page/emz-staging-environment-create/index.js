import template from './emz-staging-environment-create.html.twig';

const { Component, Context } = Shopware;

Component.register('emz-staging-environment-create', {
    template,

    inject: [
        'repositoryFactory'
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            environment: null,
            repository: null
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('emz_pse_environment');
        this.environment = this.repository.create(Context.api);
    }, 

    methods: {
        
    }
});