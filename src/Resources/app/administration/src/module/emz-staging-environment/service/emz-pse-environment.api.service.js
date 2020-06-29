import ApiService from 'src/core/service/api.service';

class StagingEnvironmentApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'environment') {
        super(httpClient, loginService, apiEndpoint);
        this.name = 'stagingEnvironmentApiService';
    }

    syncFiles({ selectedProfileId }, additionalParams = {}, additionalHeaders = {}) {
        const headers = this.getBasicHeaders({});
        const payload = {
            selectedProfileId
        };
        
        return this.httpClient.post('/_action/emz_pse/environment/sync_files', payload, { headers });
    }

    cloneDatabase({ name, profileName }, additionalParams = {}, additionalHeaders = {}) {
        const headers = this.getBasicHeaders();
        const payload = {
            name,
            profileName
        };

        return this.httpClient.post('/_action/emz_pse/environment/clone_database', payload, { headers });
    }

    updateSettings({ name, profileName }, additionalParams = {}, additionalHeaders = {}) {
        const headers = this.getBasicHeaders();
        const payload = {
            name,
            profileName
        };

        return this.httpClient.post('/_action/emz_pse/environment/update_settings', payload, { headers });
    }
}

export default StagingEnvironmentApiService;