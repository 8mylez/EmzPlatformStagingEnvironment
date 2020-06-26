import ApiService from 'src/core/service/api.service';

class StagingEnvironmentApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'environment') {
        super(httpClient, loginService, apiEndpoint);
        this.name = 'stagingEnvironmentApiService';
    }

    create({ name, profileName }, additionalParams = {}, additionalHeaders = {}) {
        const headers = this.getBasicHeaders({});
        const payload = {
            name,
            profileName
        };
        
        return this.httpClient.post('/_action/emz_pse/environment/sync_files', payload, { headers });
        // return this.httpClient.post('/_action/emz_pse/environment/create', payload, { headers });
    }
}

export default StagingEnvironmentApiService;