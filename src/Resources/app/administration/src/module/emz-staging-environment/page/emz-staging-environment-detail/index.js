import template from './emz-staging-environment-detail.html.twig';
import './emz-staging-environment-detail.scss';

const { Component, Context, Data, Mixin } = Shopware;
const { Criteria } = Data;

Component.register('emz-staging-environment-detail', {
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
            readyToSync: false,
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
        this.getEnvironment();
    },

    methods: {
        getEnvironment() {
            this.repositoryEnvironment
                .get(this.$route.params.id, Context.api)
                .then(entity => {
                    this.environment = entity;
                    this.readyToSync = true;
                });
        },
        onClickSave() {
            this.repositoryEnvironment
                .save(this.environment, Context.api)
                .then(() => {
                    this.getEnvironment();

                }).catch(exception => {
                    this.createNotificationError({
                        title: this.$t('emz-staging-environment.detail.errorTitle'),
                        message: exception
                    });
                });
        },
        createNewStatingEnvironment() {
            this.createNotificationInfo({
                title: this.$t('global.default.info'),
                message: this.$t('emz-staging-environment.create.processStarted')
            });

            this.processes.createNewStagingEnvironment = true;
            this.currentStep = 2;

            return this.stagingEnvironmentApiService.syncFiles({
                environmentId: this.environment.id
            }).then(() => {
                this.createNotificationSuccess({
                    title: this.$t('global.default.success'),
                    message: 'Sync files finished'
                });

                this.currentStep++;

                this.stagingEnvironmentApiService.cloneDatabase({
                    databaseHost: this.environment.databaseHost,
                    databaseUser: this.environment.databaseUser,
                    databaseName: this.environment.databaseName,
                    databasePassword: this.environment.databasePassword,
                    databasePort: this.environment.databasePort
                }).then(() => {

                    this.createNotificationSuccess({
                        title: this.$t('global.default.success'),
                        message: 'clone database finished'
                    });

                    this.currentStep++;

                    this.stagingEnvironmentApiService.updateSettings({
                        folderName: this.environment.folderName,
                        databaseHost: this.environment.databaseHost,
                        databaseUser: this.environment.databaseUser,
                        databaseName: this.environment.databaseName,
                        databasePassword: this.environment.databasePassword,
                        databasePort: this.environment.databasePort
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
                
                this.reset();

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
        },
        reset() {
            this.processes.createNewStagingEnvironment = false;
            this.currentStep = 1;
        }
    }
}); 