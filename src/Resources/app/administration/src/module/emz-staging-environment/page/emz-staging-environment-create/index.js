import template from './emz-staging-environment-create.html.twig';

const { Component, Context, Data, Mixin } = Shopware;
const { Criteria } = Data;

Component.register('emz-staging-environment-create', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: [
        'repositoryFactory',
        'stagingEnvironmentApiService'
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
            selectedProfile: null,
            isLoading: false,
            processes: {
                createNewStagingEnvironment: false,
            },
            processSuccess: {
                createNewStagingEnvironment: false
            },
            stepIndex: 0,
            stepVariant: "success"
        }
    },

    computed: {
        stepInitialItemVariants() {
            const steps = [
                ['info', 'disabled', 'disabled'],
                ['success', 'info', 'disabled'],
                ['success', 'success', 'info'],
            ];

            return steps[0];
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
        createNewStatingEnvironment() {
            this.createNotificationInfo({
                title: this.$t('global.default.info'),
                message: this.$t('emz-staging-environment.create.processStarted')
            });

            this.processes.createNewStagingEnvironment = true;

            return this.stagingEnvironmentApiService.syncFiles({
                name: this.environment.name,
                selectedProfile: this.selectedProfile
            }).then(() => {
                this.createNotificationSuccess({
                    title: this.$t('global.default.success'),
                    message: 'Sync files finished'
                });

                this.stagingEnvironmentApiService.cloneDatabase({
                    name: this.environment.name,
                    selectedProfile: this.selectedProfile
                }).then(() => {

                    this.createNotificationSuccess({
                        title: this.$t('global.default.success'),
                        message: 'clone database finished'
                    });

                    this.stagingEnvironmentApiService.updateSettings({
                        name: this.environment.name,
                        selectedProfile: this.selectedProfile
                    }).then(() => {
                        this.processes.createNewStagingEnvironment = false;

                        this.createNotificationSuccess({
                            title: this.$t('global.default.success'),
                            message: 'update settings finished'
                        });

                    }).finally(() => {
                        this.processes.createNewStagingEnvironment = false;
                    });
                });

            }).catch(() => {
                this.processSuccess.createNewStagingEnvironment = false;

                this.createNotificationError({
                    title: this.$t('global.default.error'),
                    message: this.$t('emz-staging-environment.create.error')
                });
            });

        },
        resetButton() {
            this.processSuccess = {
                createNewStagingEnvironment: false
            };
        }
    }
});