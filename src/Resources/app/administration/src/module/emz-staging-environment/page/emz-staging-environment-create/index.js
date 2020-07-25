const { Component, Context } = Shopware;

Component.extend('emz-staging-environment-create', 'emz-staging-environment-detail', {
    methods: {
        getEnvironment() {
            this.environment = this.repositoryEnvironment.create(Context.api);
        },
        
        onClickSave() {

        }
    }
});