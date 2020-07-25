import ApiService from 'src/core/service/api.service';

class StagingEnvironmentApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'environment') {
        super(httpClient, loginService, apiEndpoint);
        this.name = 'stagingEnvironmentApiService';
    }

    syncFiles({ folderName }, additionalParams = {}, additionalHeaders = {}) {
        const headers = this.getBasicHeaders({});
        const payload = {
            folderName
        };
        
        return this.httpClient.post('/_action/emz_pse/environment/sync_files', payload, { headers });
    }

    cloneDatabase({ 
            databaseHost, 
            databaseUser,
            databaseName,
            databasePassword,
            databasePort 
        }, 
        additionalParams = {}, 
        additionalHeaders = {}
    )
    {
        const headers = this.getBasicHeaders();
        const payload = {
            databaseHost,
            databaseUser,
            databaseName,
            databasePassword,
            databasePort
        };

        return this.httpClient.post('/_action/emz_pse/environment/clone_database', payload, { headers });
    }

    updateSettings({
            folderName,
            databaseHost, 
            databaseUser,
            databaseName,
            databasePassword,
            databasePort 
        }, 
        additionalParams = {},
        additionalHeaders = {}
    ) 
    {
        const headers = this.getBasicHeaders();
        const payload = {
            folderName,
            databaseHost, 
            databaseUser,
            databaseName,
            databasePassword,
            databasePort
        };

        return this.httpClient.post('/_action/emz_pse/environment/update_settings', payload, { headers });
    }
}

export default StagingEnvironmentApiService;