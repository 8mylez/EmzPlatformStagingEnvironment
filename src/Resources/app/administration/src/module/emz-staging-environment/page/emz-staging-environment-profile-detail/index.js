import template from './emz-staging-environment-profile-detail.html.twig';

const { Component, Context, Mixin } = Shopware;

Component.register('emz-staging-environment-profile-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            profile: null,
            isLoading: false,
            processSuccess: false,
            repository: null
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('emz_pse_profile');
        this.getProfile();
    },

    methods: {
        getProfile() {
            this.repository
                .get(this.$route.params.id, Context.api)
                .then(entity => {
                    this.profile = entity;
                });
        },
        onClickSave() {
            this.repository
                .save(this.profile, Context.api)
                .then(() => {
                    this.getProfile();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch(exception => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$t('emz-staging-environment.detail.errorTitle'),
                        message: exception
                    });
                });
        },
        saveFinish() {
            this.processSuccess = false;
        }
    }
});