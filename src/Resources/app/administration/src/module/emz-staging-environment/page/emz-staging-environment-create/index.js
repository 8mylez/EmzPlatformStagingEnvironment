const { Component, Context } = Shopware;

Component.extend('emz-staging-environment-create', 'emz-staging-environment-detail', {
    methods: {
        getEnvironment() {
            this.environment = this.repositoryEnvironment.create(Context.api);

            this.isLoading = false;
        },
        
        onClickSave() {
            this.isLoading = true;

            this.repositoryEnvironment
                .save(this.environment, Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'emz.staging.environment.detail', params: { id: this.environment.id } });
                }).catch(exception => {
                    this.isLoading = false;

                    this.createNotificationError({
                        title: this.$t('emz-staging-environment.detail.errorTitle'),
                        message: exception
                    });
                });
        },
    }
});