(this.webpackJsonp=this.webpackJsonp||[]).push([["emz-platform-staging-environment"],{"24Ln":function(e){e.exports=JSON.parse('{"emz-staging-environment":{"general":{"mainMenuItemGeneral":"Staging","mainMenuItemEnvironments":"Environments","mainMenuItemLogs":"Logs","mainMenuItemProfiles":"Profiles","descriptionTextModule":"Manage your staging environments here"},"list":{"columnProfileName":"Name","columnComment":"Comment","addButtonText":"Create profile","addEnvironmentButton":"Create new staging environment"},"detail":{"errorTitle":"Error saving the profile","cancelButtonText":"Cancel","saveButtonText":"Save","profileCardTitle":"Profile","databaseCardTitle":"Database","settingsCardTitle":"Settings","profileNameLabel":"Profile name","folderNameLabel":"Directory name","excludedFoldersLabel":"Excluded directories","commentLabel":"Comment","databaseHostLabel":"Database host","databaseUserLabel":"Database user","databaseNameLabel":"Database name","databasePasswordLabel":"Database password","databasePortLabel":"Database port","anonymizeDataLabel":"Anonymize data","deactivateScheduledTasksLabel":"Deactivate scheduled tasks","setInMaintenanceLabel":"Set in maintenance"},"create":{"title":"New staging environment","name":"Name","start":"Start","processStarted":"Process started!","success":"Environment created successfully!","error":"Error occured. Environment was not created!","stepsTitle":"Detailed steps","syncFiles":"Copy files","syncDatabase":"Clone database","updateEnv":"Update settings","prepare":"Preparation","stepsContent":{"preparation":"In the preparation step the system proves if everything is correct to create a new staging environment.","syncFiles":"In this step all files will be copied in the appropriate sub folder, so that Shopware 6 has everything it needs to run smoothly.","cloneDatabase":"The database is a crucial part of a running system. All tables will be cloned and filled with the content of the main system.","updateSettings":"At the end some settings needs to be updated on the lately created staging database.","finished":"Congratulations! Your staging environemnt is up and running!"}}}}')},"9IKe":function(e,t){e.exports='{% block emz_staging_environment_profile_detail %}\n    <sw-page class="emz-staging-environment-profile-detail">\n        <template slot="smart-bar-actions">\n            <sw-button\n                :routerLink="{ name: \'emz.staging.environment.profile_index\' }"\n            >\n                {{ $t(\'emz-staging-environment.detail.cancelButtonText\') }}\n            </sw-button>\n            <sw-button-process\n                variant="primary"\n                @click="onClickSave"\n                :isLoading="isLoading"\n                :processSuccess="processSuccess"\n                @process-finish="saveFinish"\n            >\n                {{ $t(\'emz-staging-environment.detail.saveButtonText\') }}\n            </sw-button-process>\n        </template>\n        <template slot="content">\n            <sw-card-view>\n                <sw-card\n                    v-if="profile"\n                    :isLoading="isLoading"\n                    :title="$t(\'emz-staging-environment.detail.profileCardTitle\')"\n                >\n                    <sw-container columns="1fr 1fr" gap="32px">\n                        <sw-text-field\n                            :label="$t(\'emz-staging-environment.detail.profileNameLabel\')"\n                            :placeholder="$t(\'emz-staging-environment.detail.profileNameLabel\')"\n                            v-model="profile.profileName"\n                            validation="required"\n                        >\n                        </sw-text-field>\n\n                        <sw-text-field\n                            :label="$t(\'emz-staging-environment.detail.folderNameLabel\')"\n                            :placeholder="$t(\'emz-staging-environment.detail.folderNameLabel\')"\n                            v-model="profile.folderName"\n                            validation="required"\n                        >\n                        </sw-text-field>\n                    </sw-container>\n\n                    <sw-text-field\n                        :label="$t(\'emz-staging-environment.detail.excludedFoldersLabel\')"\n                        :placeholder="$t(\'emz-staging-environment.detail.excludedFoldersLabel\')"\n                        v-model="profile.excludedFolders"\n                    >\n                    </sw-text-field>\n\n                    <sw-textarea-field\n                        :label="$t(\'emz-staging-environment.detail.commentLabel\')"\n                        :placeholder="$t(\'emz-staging-environment.detail.commentLabel\')"\n                        v-model="profile.comment"\n                    >\n                    </sw-textarea-field>\n                </sw-card>\n\n                <sw-card\n                    v-if="profile"\n                    :isLoading="isLoading"\n                    :title="$t(\'emz-staging-environment.detail.databaseCardTitle\')"\n                >\n                    <sw-text-field\n                        :label="$t(\'emz-staging-environment.detail.databaseHostLabel\')"\n                        :placeholder="$t(\'emz-staging-environment.detail.databaseHostLabel\')"\n                        v-model="profile.databaseHost"\n                        validation="required"\n                    >\n                    </sw-text-field>\n\n                    <sw-text-field\n                        :label="$t(\'emz-staging-environment.detail.databaseUserLabel\')"\n                        :placeholder="$t(\'emz-staging-environment.detail.databaseUserLabel\')"\n                        v-model="profile.databaseUser"\n                        validation="required"\n                    >\n                    </sw-text-field>\n\n                    <sw-text-field\n                        :label="$t(\'emz-staging-environment.detail.databaseNameLabel\')"\n                        :placeholder="$t(\'emz-staging-environment.detail.databaseNameLabel\')"\n                        v-model="profile.databaseName"\n                        validation="required"\n                    >\n                    </sw-text-field>\n\n                    <sw-text-field\n                        :label="$t(\'emz-staging-environment.detail.databasePasswordLabel\')"\n                        :placeholder="$t(\'emz-staging-environment.detail.databasePasswordLabel\')"\n                        v-model="profile.databasePassword"\n                        validation="required"\n                    >\n                    </sw-text-field>\n\n                    <sw-text-field\n                        :label="$t(\'emz-staging-environment.detail.databasePortLabel\')"\n                        :placeholder="$t(\'emz-staging-environment.detail.databasePortLabel\')"\n                        v-model="profile.databasePort"\n                        validation="required"\n                    >\n                    </sw-text-field>\n                </sw-card>\n\n                <sw-card\n                    v-if="profile"\n                    :isLoading="isLoading"\n                    :title="$t(\'emz-staging-environment.detail.settingsCardTitle\')"\n                >\n                    <sw-checkbox-field\n                        :label="$t(\'emz-staging-environment.detail.anonymizeDataLabel\')"\n                        v-model="profile.anonymizeData"\n                    >\n                    </sw-checkbox-field>\n\n                    <sw-checkbox-field\n                        :label="$t(\'emz-staging-environment.detail.deactivateScheduledTasksLabel\')"\n                        v-model="profile.deactivateScheduledTasks"\n                    >\n                    </sw-checkbox-field>\n\n                    <sw-checkbox-field\n                        :label="$t(\'emz-staging-environment.detail.setInMaintenanceLabel\')"\n                        v-model="profile.setInMaintenance"\n                    >\n                    </sw-checkbox-field>\n\n                </sw-card>\n            </sw-card-view>\n        </template>\n    </sw-page>\n{% endblock %}'},BZnw:function(e,t){e.exports='{% block emz_staging_environment_log_index %}\n    <sw-page class="emz-staging-environment-log-index">\n        <template #content>\n            <sw-card-view>\n                <sw-card>\n                    Hallo Log Index\n                </sw-card>\n            </sw-card-view>\n        </template>\n    </sw-page>\n{% endblock %}'},CkOj:function(e,t,n){"use strict";n.d(t,"a",(function(){return c}));var i=n("lSNA"),a=n.n(i),r=n("lO2t"),s=n("lYO9");function o(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(e);t&&(i=i.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,i)}return n}function l(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?o(Object(n),!0).forEach((function(t){a()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):o(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function c(e){var t=function(e){var t;if(r.a.isString(e))try{t=JSON.parse(e)}catch(e){return!1}else{if(!r.a.isObject(e)||r.a.isArray(e))return!1;t=e}return t}(e);if(!t)return null;if(!0===t.parsed||!function(e){return void 0!==e.data||void 0!==e.errors||void 0!==e.links||void 0!==e.meta}(t))return t;var n=function(e){var t={links:null,errors:null,data:null,associations:null,aggregations:null};if(e.errors)return t.errors=e.errors,t;var n=function(e){var t=new Map;if(!e||!e.length)return t;return e.forEach((function(e){var n="".concat(e.type,"-").concat(e.id);t.set(n,e)})),t}(e.included);if(r.a.isArray(e.data))t.data=e.data.map((function(e){var i=d(e,n);return Object(s.f)(i,"associationLinks")&&(t.associations=l({},t.associations,{},i.associationLinks),delete i.associationLinks),i}));else if(r.a.isObject(e.data)){var i=d(e.data,n);Object.prototype.hasOwnProperty.call(i,"associationLinks")&&(t.associations=l({},t.associations,{},i.associationLinks),delete i.associationLinks),t.data=i}else t.data=null;e.meta&&Object.keys(e.meta).length&&(t.meta=m(e.meta));e.links&&Object.keys(e.links).length&&(t.links=e.links);e.aggregations&&Object.keys(e.aggregations).length&&(t.aggregations=e.aggregations);return t}(t);return n.parsed=!0,n}function d(e,t){var n={id:e.id,type:e.type,links:e.links||{},meta:e.meta||{}};e.attributes&&Object.keys(e.attributes).length>0&&(n=l({},n,{},m(e.attributes)));if(e.relationships){var i=function(e,t){var n={},i={};return Object.keys(e).forEach((function(a){var s=e[a];if(s.links&&Object.keys(s.links).length&&(i[a]=s.links.related),s.data){var o=s.data;r.a.isArray(o)?n[a]=o.map((function(e){return g(e,t)})):r.a.isObject(o)?n[a]=g(o,t):n[a]=null}})),{mappedRelations:n,associationLinks:i}}(e.relationships,t);n=l({},n,{},i.mappedRelations,{},{associationLinks:i.associationLinks})}return n}function m(e){var t={};return Object.keys(e).forEach((function(n){var i=e[n],a=n.replace(/-([a-z])/g,(function(e,t){return t.toUpperCase()}));t[a]=i})),t}function g(e,t){var n="".concat(e.type,"-").concat(e.id);return t.has(n)?d(t.get(n),t):e}},FSyA:function(e,t){const{Component:n}=Shopware;n.extend("emz-staging-environment-profile-create","emz-staging-environment-profile-detail",{methods:{getProfile(){this.profile=this.repository.create(Shopware.Context.api)},onClickSave(){this.isLoading=!0,this.repository.save(this.profile,Shopware.Context.api).then(()=>{this.isLoading=!1,this.$router.push({name:"emz.staging.environment.profile_detail",params:{id:this.profile.id}})}).catch(e=>{this.isLoading=!1,this.createNotificationError({title:this.$t("emz-staging-environment.detail.errorTitle"),message:e})})}}})},J5Mq:function(e,t){e.exports='{% block emz_staging_environment_create %}\n    <sw-page class="emz-staging-environment-create">\n        <template slot="smart-bar-header">\n            <h2>\n                {{ $t(\'emz-staging-environment.general.mainMenuItemGeneral\') }}\n                    <sw-icon name="small-arrow-medium-right" small></sw-icon>\n                {{ $t(\'emz-staging-environment.create.title\') }}\n            </h2>\n        </template>\n        \n        <template #content>\n            <sw-card-view>\n                <sw-card\n                    :title="$t(\'emz-staging-environment.create.title\')"    \n                >\n                    <sw-text-field\n                        :label="$t(\'emz-staging-environment.create.name\')"\n                        :placeholder="$t(\'emz-staging-environment.create.name\')"\n                        v-model="environment.name"\n                        required\n                    >\n                    </sw-text-field>\n\n                    <sw-entity-single-select\n                        required\n                        entity="emz_pse_profile"\n                        label="Profile"\n                        labelProperty="profileName"\n                        v-model="selectedProfile">\n                    </sw-entity-single-select>\n\n                    <sw-button-process variant="ghost"\n                        :isLoading="processes.createNewStagingEnvironment"\n                        :processSuccess="processSuccess.createNewStagingEnvironment"\n                        @process-finish="resetButton"\n                        @click="createNewStatingEnvironment"\n                    >\n                        {{ $t(\'emz-staging-environment.create.start\') }}\n                    </sw-button-process>\n                </sw-card>\n                <sw-card\n                    :title="$t(\'emz-staging-environment.create.stepsTitle\')"\n                >\n                    <sw-container columns="1fr 1fr">\n                        <sw-card-section divider="right">\n                            <sw-step-display :itemIndex="stepIndex"\n                                :itemVariant="stepVariant"\n                                :initialItemVariants="stepInitialItemVariants">\n                                <sw-step-item>\n                                    {{ $t(\'emz-staging-environment.create.prepare\') }}\n                                </sw-step-item>\n                                <sw-step-item>\n                                    {{ $t(\'emz-staging-environment.create.syncFiles\') }}\n                                </sw-step-item>\n                                <sw-step-item>\n                                    {{ $t(\'emz-staging-environment.create.syncDatabase\') }}\n                                </sw-step-item>\n                                <sw-step-item>\n                                    {{ $t(\'emz-staging-environment.create.updateEnv\') }}\n                                </sw-step-item>\n                            </sw-step-display>\n                        </sw-card-section>\n                        <sw-card-section>\n                            <p>{{stepContent}}</p>\n                        </sw-card-section>\n                    </sw-container>\n                </sw-card>\n            </sw-card-view>\n        </template>\n    </sw-page>\n{% endblock %}'},SwLI:function(e,t,n){"use strict";n.r(t);var i=n("lwsE"),a=n.n(i),r=n("W8MJ"),s=n.n(r),o=n("CkOj"),l=function(){function e(t,n,i){var r=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"application/vnd.api+json";a()(this,e),this.httpClient=t,this.loginService=n,this.apiEndpoint=i,this.contentType=r}return s()(e,[{key:"getList",value:function(t){var n=t.page,i=void 0===n?1:n,a=t.limit,r=void 0===a?25:a,s=t.sortBy,o=t.sortDirection,l=void 0===o?"asc":o,c=t.sortings,d=t.queries,m=t.term,g=t.criteria,p=t.aggregations,u=t.associations,h=t.headers,f=t.versionId,v=t.ids,b=this.getBasicHeaders(h),w={page:i,limit:r};return c?w.sort=c:s&&s.length&&(w.sort=("asc"===l.toLowerCase()?"":"-")+s),v&&(w.ids=v.join("|")),m&&(w.term=m),g&&(w.filter=[g.getQuery()]),p&&(w.aggregations=p),u&&(w.associations=u),f&&(b=Object.assign(b,e.getVersionHeader(f))),d&&(w.query=d),w.term&&w.term.length||w.filter&&w.filter.length||w.aggregations||w.sort||w.queries||w.associations?this.httpClient.post("".concat(this.getApiBasePath(null,"search")),w,{headers:b}).then((function(t){return e.handleResponse(t)})):this.httpClient.get(this.getApiBasePath(),{params:w,headers:b}).then((function(t){return e.handleResponse(t)}))}},{key:"getById",value:function(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};if(!t)return Promise.reject(new Error("Missing required argument: id"));var a=n,r=this.getBasicHeaders(i);return this.httpClient.get(this.getApiBasePath(t),{params:a,headers:r}).then((function(t){return e.handleResponse(t)}))}},{key:"updateById",value:function(t,n){var i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},a=arguments.length>3&&void 0!==arguments[3]?arguments[3]:{};if(!t)return Promise.reject(new Error("Missing required argument: id"));var r=i,s=this.getBasicHeaders(a);return this.httpClient.patch(this.getApiBasePath(t),n,{params:r,headers:s}).then((function(t){return e.handleResponse(t)}))}},{key:"deleteAssociation",value:function(e,t,n,i){if(!e||!n||!n)return Promise.reject(new Error("Missing required arguments."));var a=this.getBasicHeaders(i);return this.httpClient.delete("".concat(this.getApiBasePath(e),"/").concat(t,"/").concat(n),{headers:a}).then((function(e){return e.status>=200&&e.status<300?Promise.resolve(e):Promise.reject(e)}))}},{key:"create",value:function(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},a=n,r=this.getBasicHeaders(i);return this.httpClient.post(this.getApiBasePath(),t,{params:a,headers:r}).then((function(t){return e.handleResponse(t)}))}},{key:"delete",value:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};if(!e)return Promise.reject(new Error("Missing required argument: id"));var i=Object.assign({},t),a=this.getBasicHeaders(n);return this.httpClient.delete(this.getApiBasePath(e),{params:i,headers:a})}},{key:"clone",value:function(t){return t?this.httpClient.post("/_action/clone/".concat(this.apiEndpoint,"/").concat(t),null,{headers:this.getBasicHeaders()}).then((function(t){return e.handleResponse(t)})):Promise.reject(new Error("Missing required argument: id"))}},{key:"versionize",value:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},i="/_action/version/".concat(this.apiEndpoint,"/").concat(e),a=Object.assign({},t),r=this.getBasicHeaders(n);return this.httpClient.post(i,{},{params:a,headers:r})}},{key:"mergeVersion",value:function(t,n,i,a){if(!t)return Promise.reject(new Error("Missing required argument: id"));if(!n)return Promise.reject(new Error("Missing required argument: versionId"));var r=Object.assign({},i),s=Object.assign(e.getVersionHeader(n),this.getBasicHeaders(a)),o="_action/version/merge/".concat(this.apiEndpoint,"/").concat(n);return this.httpClient.post(o,{},{params:r,headers:s})}},{key:"getApiBasePath",value:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"",n="";return t&&t.length&&(n+="".concat(t,"/")),e&&e.length>0?"".concat(n).concat(this.apiEndpoint,"/").concat(e):"".concat(n).concat(this.apiEndpoint)}},{key:"getBasicHeaders",value:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t={Accept:this.contentType,Authorization:"Bearer ".concat(this.loginService.getToken()),"Content-Type":"application/json"};return Object.assign({},t,e)}},{key:"apiEndpoint",get:function(){return this.endpoint},set:function(e){this.endpoint=e}},{key:"httpClient",get:function(){return this.client},set:function(e){this.client=e}},{key:"contentType",get:function(){return this.type},set:function(e){this.type=e}}],[{key:"handleResponse",value:function(t){if(null===t.data||void 0===t.data)return t;var n=t.data,i=t.headers;return i&&i["content-type"]&&"application/vnd.api+json"===i["content-type"]&&(n=e.parseJsonApiData(n)),n}},{key:"parseJsonApiData",value:function(e){return Object(o.a)(e)}},{key:"getVersionHeader",value:function(e){return{"sw-version-id":e}}}]),e}();t.default=l},T8cp:function(e,t,n){"use strict";n.r(t);var i=n("vOJG"),a=n.n(i);const{Component:r}=Shopware;r.register("emz-staging-environment-index",{template:a.a,data:()=>({}),created(){},methods:{}});var s=n("J5Mq"),o=n.n(s);const{Component:l,Context:c,Data:d,Mixin:m}=Shopware,{Criteria:g}=d;l.register("emz-staging-environment-create",{template:o.a,mixins:[m.getByName("notification")],inject:["repositoryFactory","stagingEnvironmentApiService"],metaInfo(){return{title:this.$createTitle()}},data:()=>({environment:null,repositoryEnvironment:null,repositoryProfile:null,profiles:null,selectedProfile:null,isLoading:!1,processes:{createNewStagingEnvironment:!1},processSuccess:{createNewStagingEnvironment:!1},stepVariant:"info",currentStep:1}),computed:{stepIndex(){return this.currentStep<1?0:this.currentStep-1},stepInitialItemVariants(){return[["disabled","disabled","disabled","disabled"],["success","disabled","disabled","disabled"],["success","info","disabled","disabled"],["success","success","info","disabled"],["success","success","success","info"],["success","success","success","success"]][this.currentStep]},stepContent(){return["",this.$t("emz-staging-environment.create.stepsContent.preparation"),this.$t("emz-staging-environment.create.stepsContent.syncFiles"),this.$t("emz-staging-environment.create.stepsContent.cloneDatabase"),this.$t("emz-staging-environment.create.stepsContent.updateSettings"),this.$t("emz-staging-environment.create.stepsContent.finished")][this.currentStep]}},created(){this.repositoryEnvironment=this.repositoryFactory.create("emz_pse_environment"),this.environment=this.repositoryEnvironment.create(c.api)},methods:{createNewStatingEnvironment(){return this.createNotificationInfo({title:this.$t("global.default.info"),message:this.$t("emz-staging-environment.create.processStarted")}),this.processes.createNewStagingEnvironment=!0,this.currentStep=2,this.stagingEnvironmentApiService.syncFiles({selectedProfileId:this.selectedProfile}).then(()=>{this.createNotificationSuccess({title:this.$t("global.default.success"),message:"Sync files finished"}),this.currentStep++,this.stagingEnvironmentApiService.cloneDatabase({name:this.environment.name,selectedProfile:this.selectedProfile}).then(()=>{this.createNotificationSuccess({title:this.$t("global.default.success"),message:"clone database finished"}),this.currentStep++,this.stagingEnvironmentApiService.updateSettings({name:this.environment.name,selectedProfile:this.selectedProfile}).then(()=>{this.processes.createNewStagingEnvironment=!1,this.createNotificationSuccess({title:this.$t("global.default.success"),message:"update settings finished"}),this.currentStep++}).finally(()=>{this.processes.createNewStagingEnvironment=!1,this.currentStep=5})})}).catch(()=>{this.resetButton(),this.createNotificationError({title:this.$t("global.default.error"),message:this.$t("emz-staging-environment.create.error")})})},resetButton(){this.processSuccess={createNewStagingEnvironment:!1}}}});var p=n("ieVc"),u=n.n(p);const{Component:h,Data:f}=Shopware,{Criteria:v}=f;h.register("emz-staging-environment-profile-index",{template:u.a,metaInfo(){return{title:this.$createTitle()}},inject:["repositoryFactory"],data:()=>({repository:null,profiles:null}),created(){this.repository=this.repositoryFactory.create("emz_pse_profile"),this.repository.search(new v,Shopware.Context.api).then(e=>{this.profiles=e})},computed:{columns(){return[{property:"profileName",dataIndex:"profileName",label:this.$t("emz-staging-environment.list.columnProfileName"),routerLink:"emz.staging.environment.profile_detail",inlineEdit:"string",allowResize:!0,primary:!0},{property:"comment",dataIndex:"comment",label:this.$t("emz-staging-environment.list.columnComment"),inlineEdit:"string",allowResize:!0}]}}});var b=n("9IKe"),w=n.n(b);const{Component:y,Context:z,Mixin:S}=Shopware;y.register("emz-staging-environment-profile-detail",{template:w.a,inject:["repositoryFactory"],mixins:[S.getByName("notification")],metaInfo(){return{title:this.$createTitle()}},data:()=>({profile:null,isLoading:!1,processSuccess:!1,repository:null}),created(){this.repository=this.repositoryFactory.create("emz_pse_profile"),this.getProfile()},methods:{getProfile(){this.repository.get(this.$route.params.id,z.api).then(e=>{this.profile=e})},onClickSave(){this.repository.save(this.profile,z.api).then(()=>{this.getProfile(),this.isLoading=!1,this.processSuccess=!0}).catch(e=>{this.isLoading=!1,this.createNotificationError({title:this.$t("emz-staging-environment.detail.errorTitle"),message:e})})},saveFinish(){this.processSuccess=!1}}});n("FSyA");var k=n("BZnw"),x=n.n(k);const{Component:P}=Shopware;P.register("emz-staging-environment-log-index",{template:x.a,data:()=>({}),created(){},methods:{}});var L=n("yCrm"),O=n("24Ln"),E=n("SwLI");class C extends E.default{constructor(e,t,n="environment"){super(e,t,n),this.name="stagingEnvironmentApiService"}syncFiles({selectedProfileId:e},t={},n={}){const i=this.getBasicHeaders({}),a={selectedProfileId:e};return this.httpClient.post("/_action/emz_pse/environment/sync_files",a,{headers:i})}cloneDatabase({name:e,profileName:t},n={},i={}){const a=this.getBasicHeaders(),r={name:e,profileName:t};return this.httpClient.post("/_action/emz_pse/environment/clone_database",r,{headers:a})}updateSettings({name:e,profileName:t},n={},i={}){const a=this.getBasicHeaders(),r={name:e,profileName:t};return this.httpClient.post("/_action/emz_pse/environment/update_settings",r,{headers:a})}}var j=C;const{Module:N,Application:$}=Shopware;N.register("emz-staging-environment",{type:"plugin",name:"Staging",title:"emz-staging-environment.general.mainMenuItemGeneral",description:"emz-staging-environment.general.descriptionTextModule",color:"#009bd9",icon:"default-device-server",snippets:{"de-DE":L,"en-GB":O},routes:{index:{component:"emz-staging-environment-index",path:"index"},create:{component:"emz-staging-environment-create",path:"create",meta:{parentPath:"emz.staging.environment.index"}},profile_index:{component:"emz-staging-environment-profile-index",path:"profile/index"},profile_detail:{component:"emz-staging-environment-profile-detail",path:"profile/detail/:id",meta:{parentPath:"emz.staging.environment.profile_index"}},profile_create:{component:"emz-staging-environment-profile-create",path:"profile/create",meta:{parentParth:"emz.staging.environment.profile_index"}},log_index:{component:"emz-staging-environment-log-index",path:"log/index"}},navigation:[{id:"emz-staging-environment",label:"emz-staging-environment.general.mainMenuItemGeneral",color:"#009bd9",path:"emz.staging.environment.index",icon:"default-device-server",position:100},{path:"emz.staging.environment.index",label:"emz-staging-environment.general.mainMenuItemEnvironments",parent:"emz-staging-environment"},{path:"emz.staging.environment.profile_index",label:"emz-staging-environment.general.mainMenuItemProfiles",parent:"emz-staging-environment"},{path:"emz.staging.environment.log_index",label:"emz-staging-environment.general.mainMenuItemLogs",parent:"emz-staging-environment"}]}),$.addServiceProvider("stagingEnvironmentApiService",e=>{const t=$.getContainer("init");return new j(t.httpClient,e.loginService)})},ieVc:function(e,t){e.exports='{% block emz_staging_environment_profile_index %}\n    <sw-page class="emz-staging-environment-profile-index">\n        <template slot="smart-bar-actions">\n            <sw-button\n                variant="primary"\n                :routerLink="{ name: \'emz.staging.environment.profile_create\' }"\n            >\n                {{ $t(\'emz-staging-environment.list.addButtonText\') }}\n            </sw-button>\n        </template>\n\n        <template slot="content">\n            {% block emz_staging_environment_profile_list_content %}\n                <sw-entity-listing\n                    v-if="profiles"\n                    :items="profiles"\n                    :repository="repository"\n                    :showSelection="false"\n                    :columns="columns"\n                    detailRoute="emz.staging.environment.profile_detail"\n                >\n                </sw-entity-listing>\n            {% endblock %}\n        </template>\n    </sw-page>\n{% endblock %}'},lO2t:function(e,t,n){"use strict";n.d(t,"b",(function(){return P}));var i=n("GoyQ"),a=n.n(i),r=n("YO3V"),s=n.n(r),o=n("E+oP"),l=n.n(o),c=n("wAXd"),d=n.n(c),m=n("Z0cm"),g=n.n(m),p=n("lSCD"),u=n.n(p),h=n("YiAA"),f=n.n(h),v=n("4qC0"),b=n.n(v),w=n("Znm+"),y=n.n(w),z=n("Y+p1"),S=n.n(z),k=n("UB5X"),x=n.n(k);function P(e){return void 0===e}t.a={isObject:a.a,isPlainObject:s.a,isEmpty:l.a,isRegExp:d.a,isArray:g.a,isFunction:u.a,isDate:f.a,isString:b.a,isBoolean:y.a,isEqual:S.a,isNumber:x.a,isUndefined:P}},lYO9:function(e,t,n){"use strict";n.d(t,"g",(function(){return f})),n.d(t,"a",(function(){return v})),n.d(t,"c",(function(){return b})),n.d(t,"h",(function(){return w})),n.d(t,"f",(function(){return y})),n.d(t,"b",(function(){return z})),n.d(t,"e",(function(){return S})),n.d(t,"d",(function(){return k}));var i=n("lSNA"),a=n.n(i),r=n("QkVN"),s=n.n(r),o=n("BkRI"),l=n.n(o),c=n("mwIZ"),d=n.n(c),m=n("D1y2"),g=n.n(m),p=n("lO2t");function u(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(e);t&&(i=i.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,i)}return n}function h(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?u(Object(n),!0).forEach((function(t){a()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):u(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}s.a,l.a,d.a,g.a;var f=s.a,v=l.a,b=d.a,w=g.a;function y(e,t){return Object.prototype.hasOwnProperty.call(e,t)}function z(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return JSON.parse(JSON.stringify(e))}function S(e,t){return e===t?{}:p.a.isObject(e)&&p.a.isObject(t)?p.a.isDate(e)||p.a.isDate(t)?e.valueOf()===t.valueOf()?{}:t:Object.keys(t).reduce((function(n,i){if(!y(e,i))return h({},n,a()({},i,t[i]));if(p.a.isArray(t[i])){var r=k(e[i],t[i]);return Object.keys(r).length>0?h({},n,a()({},i,t[i])):n}if(p.a.isObject(t[i])){var s=S(e[i],t[i]);return!p.a.isObject(s)||Object.keys(s).length>0?h({},n,a()({},i,s)):n}return e[i]!==t[i]?h({},n,a()({},i,t[i])):n}),{}):t}function k(e,t){if(e===t)return[];if(!p.a.isArray(e)||!p.a.isArray(t))return t;if(e.length<=0&&t.length<=0)return[];if(e.length!==t.length)return t;if(!p.a.isObject(t[0]))return t.filter((function(t){return!e.includes(t)}));var n=[];return t.forEach((function(i,a){var r=S(e[a],t[a]);Object.keys(r).length>0&&n.push(t[a])})),n}},vOJG:function(e,t){e.exports='{% block emz_staging_environment_index %}\n    <sw-page class="emz-staging-environment-index">\n        <template slot="smart-bar-actions">\n            <sw-button\n                variant="primary"\n                :routerLink="{ name: \'emz.staging.environment.create\' }"\n            >\n                {{ $t(\'emz-staging-environment.list.addEnvironmentButton\') }}\n            </sw-button>\n        </template>\n        <template #content>\n            <sw-card-view>\n                <sw-card>\n                    Hallo Staging11\n                </sw-card>\n            </sw-card-view>\n        </template>\n    </sw-page>\n{% endblock %}'},yCrm:function(e){e.exports=JSON.parse('{"emz-staging-environment":{"general":{"mainMenuItemGeneral":"Staging","mainMenuItemEnvironments":"Umgebungen","mainMenuItemLogs":"Protokoll","mainMenuItemProfiles":"Profile","descriptionTextModule":"Verwalte deine Staging Umgebungen hier"},"list":{"columnProfileName":"Name","columnComment":"Kommentar","addButtonText":"Profil erstellen","addEnvironmentButton":"Staging Umgebung erstellen"},"detail":{"errorTitle":"Fehler beim Speichern des Profils","cancelButtonText":"Abbrechen","saveButtonText":"Speichern","profileCardTitle":"Profil","databaseCardTitle":"Datenbank","settingsCardTitle":"Einstellungen","profileNameLabel":"Profil Name","folderNameLabel":"Verzeichnis Name","excludedFoldersLabel":"Ausgeschlossene Verzeichnisse","commentLabel":"Kommentar","databaseHostLabel":"Datenbank Host","databaseUserLabel":"Datenbank Benutzer","databaseNameLabel":"Datenbank Name","databasePasswordLabel":"Datenbank Passwort","databasePortLabel":"Datenbank Port","anonymizeDataLabel":"Daten anonymisieren","deactivateScheduledTasksLabel":"Geplante Aufgaben deaktivieren","setInMaintenanceLabel":"In Wartungsmodus setzen"},"create":{"title":"Neue Staging Umgebung","name":"Name","start":"Start","processStarted":"Erstellung gestartet!","success":"Umgebung erfolgreich erstellt!","error":"Es ist ein Fehler aufgetreten!","stepsTitle":"Einzelne Schritte","syncFiles":"Dateien kopieren","syncDatabase":"Datenbank klonen","updateEnv":"Einstellungen vornehmen","prepare":"Vorbereitung","stepsContent":{"preparation":"In der Vorbereitung wird geprüft ob das System bereit für die Anlage einer Staging Umgebung ist.","syncFiles":"In diesem Schritt werden alle wichtigen Dateien in den entsprechenden Unterordner kopiert, sodass Shopware 6 alles hat was es braucht.","cloneDatabase":"Die Datenbank ist ein wichtiger Bestandteil eines lauffähigen Shops. Alle Tabellen und Inhalte werden geklont und bereitgestellt.","updateSettings":"Zum Schluss müssen diverse Einstellungen auf die Staging Umgebung angepasst werden, sodass diese auch zurecht kommt.","finished":"Herzlichen Glückwunsch! Die Staging Umgebung wurde erfolgreich angelegt und steht bereit!"}}}}')}},[["T8cp","runtime","vendors-node"]]]);