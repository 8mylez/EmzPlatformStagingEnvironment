import template from './emz-staging-environment-create.html.twig';
import './emz-staging-environment-create.scss';

const { Component } = Shopware;

Component.register('emz-staging-environment-create', {
    template,

    data() {
        return {
            createSelection: 'fresh',
        }
    }
});