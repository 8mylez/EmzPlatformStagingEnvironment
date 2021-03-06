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
            isLoading: true,
            readyToSync: false,
            readyToClearDatabase: false,
            readyToClearFiles: false,
            processes: {
                createNewStagingEnvironment: false,
                clearDatabase: false,
                clearFiles: false
            },
            processSuccess: {
                createNewStagingEnvironment: false,
                clearDatabase: false,
                clearFiles: false,
            },
            stepVariant: "info",
            currentStep: 1,
            lastSync: null,
            clearFilesConfirmation: null,
            clearDatabaseConfirmation: null
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
                    this.isLoading = false;
                    this.getLastSync();
                    this.checkClearingState();
                });
        },
        onClickSave() {
            this.isLoading = true;

            this.repositoryEnvironment
                .save(this.environment, Context.api)
                .then(() => {
                    this.getEnvironment();
                    this.isLoading = false;
                    this.getLastSync();
                    this.checkClearingState();
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
                    environmentId: this.environment.id
                }).then(response => {
                    if (response.data.status == false) {
                        this.reset();
                        this.createNotificationError({
                            title: this.$t('global.default.error'),
                            message: response.data.message
                        });
                        return;
                    }

                    this.createNotificationSuccess({
                        title: this.$t('global.default.success'),
                        message: 'clone database finished'
                    });

                    this.currentStep++;

                    this.stagingEnvironmentApiService.updateSettings({
                        environmentId: this.environment.id
                    }).then(() => {
                        this.processes.createNewStagingEnvironment = false;

                        this.createNotificationSuccess({
                            title: this.$t('global.default.success'),
                            message: 'update settings finished'
                        });

                        this.currentStep++;
                    }).catch(() => {
                        this.reset();
                        this.createNotificationError({
                            title: this.$t('global.default.error'),
                            message: this.$t('emz-staging-environment.create.error')
                        });
                    }).finally(() => {
                        this.processes.createNewStagingEnvironment = false;
                        this.currentStep = 5;

                        this.getLastSync();
                        this.checkClearingState();                
                    });
                }).catch(({response}) => {
                    response.data.errors.forEach((singleError) => {
                        this.createNotificationError({
                            title: this.$t('global.default.error'),
                            message: `${singleError.detail}`
                        });
                    });

                    this.reset();
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
        },
        getLastSync() {
            if (this.environment && this.environment.id) {
                this.stagingEnvironmentApiService.getLastSync({
                    environmentId: this.environment.id
                }).then(log => {
                    if (log && log.data && log.data.lastSync) {
                        this.lastSync = log.data.lastSync;
                    }
                });
            }
        },
        checkClearingState() {
            if (this.environment && this.environment.id) {
                this.stagingEnvironmentApiService.getClearingState({
                    environmentId: this.environment.id
                }).then(clearingState => {
                    if (clearingState && clearingState.data) {
                        if (clearingState.data.statusDatabase) {
                            this.readyToClearDatabase = true;
                        }

                        if (clearingState.data.statusFiles) {
                            this.readyToClearFiles = true;
                        }
                    } else {
                        this.readyToClearDatabase = false;
                        this.readyToClearFiles = false;
                    }
                });
            }
        },
        clearDatabase() {
            if (this.environment && this.environment.id) {

                this.processes.clearDatabase = true;

                this.stagingEnvironmentApiService.clearDatabase({
                    environmentId: this.environment.id
                }).then(() => {
                    this.createNotificationSuccess({
                        title: this.$t('global.default.success'),
                        message: 'Clearing database finished'
                    });

                    this.processes.clearDatabase = false;

                    this.checkClearingState();
                });
            }
        },
        clearFiles() {
            if (this.environment && this.environment.id) {

                this.processes.clearFiles = true;

                this.stagingEnvironmentApiService.clearFiles({
                    environmentId: this.environment.id
                }).then(() => {
                    this.createNotificationSuccess({
                        title: this.$t('global.default.success'),
                        message: 'Removing Files finished'
                    });

                    this.processes.clearFiles = false;

                    this.checkClearingState();
                });
            }
        }
    }
}); 