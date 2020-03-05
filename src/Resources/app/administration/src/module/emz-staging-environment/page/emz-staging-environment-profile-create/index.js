const { Component } = Shopware;

Component.extend('emz-staging-environment-profile-create', 'emz-staging-environment-profile-detail', {
    methods: {
        getProfile() {
            this.profile = this.repository.create(Shopware.Context.api);
        },
        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.profile, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'emz.staging.environment.profile_detail', params: { id: this.profile.id }});
                }).catch(exception => {
                    this.isLoading = false;

                    this.createNotificationError({
                        title: this.$t('emz-staging-environment.detail.errorTitle'),
                        message: exception
                    });
                });
        }
    }
});
