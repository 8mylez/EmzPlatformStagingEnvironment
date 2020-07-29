import ApiService from 'src/core/service/api.service';

class StagingEnvironmentApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'environment') {
        super(httpClient, loginService, apiEndpoint);
        this.name = 'stagingEnvironmentApiService';
    }

    syncFiles({ environmentId }, additionalParams = {}, additionalHeaders = {}) {
        const headers = this.getBasicHeaders({});
        const payload = {
            environmentId
        };
        
        return this.httpClient.post('/_action/emz_pse/environment/sync_files', payload, { headers });
    }

    cloneDatabase({ environmentId }, additionalParams = {}, additionalHeaders = {})
    {
        const headers = this.getBasicHeaders();
        const payload = {
            environmentId
        };

        return this.httpClient.post('/_action/emz_pse/environment/clone_database', payload, { headers });
    }

    updateSettings({ environmentId }, additionalParams = {}, additionalHeaders = {}) 
    {
        const headers = this.getBasicHeaders();
        const payload = {
            environmentId
        };

        return this.httpClient.post('/_action/emz_pse/environment/update_settings', payload, { headers });
    }

    getLastSync({ environmentId }, additionalParams = {}, additionalHeaders = {})
    {
        const headers = this.getBasicHeaders();
        const payload = {
            environmentId
        };

        return this.httpClient.post('/_action/emz_pse/environment/get_last_sync', payload, { headers });
    }
}

export default StagingEnvironmentApiService;