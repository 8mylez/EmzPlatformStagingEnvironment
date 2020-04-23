import template from './emz-staging-environment-create.html.twig';

const { Component, Context, Data } = Shopware;
const { Criteria } = Data;

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
            repositoryEnvironment: null,
            repositoryProfile: null,
            profiles: null,
            selectedProfile: null
        }
    },

    created() {
        this.repositoryEnvironment = this.repositoryFactory.create('emz_pse_environment');
        this.environment = this.repositoryEnvironment.create(Context.api);

        // this.repositoryProfile = this.repositoryFactory.create('emz_pse_profile');
        // this.repositoryProfile
        //     .search(new Criteria(), Shopware.Context.api)
        //     .then(result => {
        //         this.profiles = result;
        //     });
    },

    methods: {
        
    }
});