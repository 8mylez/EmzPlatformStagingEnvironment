import ApiService from 'src/core/service/api.service';

class StagingEnvironmentApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'environment') {
        super(httpClient, loginService, apiEndpoint);
        this.name = 'stagingEnvironmentApiService';
    }

    create(payload = {}) {
        const headers = this.getBasicHeaders({});
        return this.httpClient.post('/_action/emz_pse/environment/create', payload, { headers });
    }
}

export default StagingEnvironmentApiService;