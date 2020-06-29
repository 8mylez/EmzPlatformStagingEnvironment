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
            stepVariant: "info",
            currentStep: 1
        }
    },

    computed: {
        stepIndex() {
            return this.currentStep < 1 ? 0 : this.currentStep -1;
        },
        stepInitialItemVariants() {
            const steps = [
                ['disabled', 'disabled', 'disabled', 'disabled'],
                ['success', 'disabled', 'disabled', 'disabled'],
                ['success', 'info', 'disabled', 'disabled'],
                ['success', 'success', 'info', 'disabled'],
                ['success', 'success', 'success', 'info'],
                ['success', 'success', 'success', 'success'],
            ];

            return steps[this.currentStep];
        },
        stepContent() {
            const stepContent = [
                '',
                this.$t('emz-staging-environment.create.stepsContent.preparation'),
                this.$t('emz-staging-environment.create.stepsContent.syncFiles'),
                this.$t('emz-staging-environment.create.stepsContent.cloneDatabase'),
                this.$t('emz-staging-environment.create.stepsContent.updateSettings'),
                this.$t('emz-staging-environment.create.stepsContent.finished')
            ];

            return stepContent[this.currentStep];
        }
    },

    created() {
        this.repositoryEnvironment = this.repositoryFactory.create('emz_pse_environment');
        this.environment = this.repositoryEnvironment.create(Context.api);
    },

    methods: {
        createNewStatingEnvironment() {
            this.createNotificationInfo({
                title: this.$t('global.default.info'),
                message: this.$t('emz-staging-environment.create.processStarted')
            });

            this.processes.createNewStagingEnvironment = true;
            this.currentStep = 2;

            return this.stagingEnvironmentApiService.syncFiles({
                selectedProfileId: this.selectedProfile
            }).then(() => {
                this.createNotificationSuccess({
                    title: this.$t('global.default.success'),
                    message: 'Sync files finished'
                });

                this.currentStep++;

                this.stagingEnvironmentApiService.cloneDatabase({
                    name: this.environment.name,
                    selectedProfile: this.selectedProfile
                }).then(() => {

                    this.createNotificationSuccess({
                        title: this.$t('global.default.success'),
                        message: 'clone database finished'
                    });

                    this.currentStep++;

                    this.stagingEnvironmentApiService.updateSettings({
                        name: this.environment.name,
                        selectedProfile: this.selectedProfile
                    }).then(() => {
                        this.processes.createNewStagingEnvironment = false;

                        this.createNotificationSuccess({
                            title: this.$t('global.default.success'),
                            message: 'update settings finished'
                        });

                        this.currentStep++;

                    }).finally(() => {
                        this.processes.createNewStagingEnvironment = false;
                        this.currentStep = 5;
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