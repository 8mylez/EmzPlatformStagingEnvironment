#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag | tail -n 1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Build new release
mkdir -p EmzPlatformStagingEnvironment
git archive ${commit} | tar -x -C EmzPlatformStagingEnvironment
zip -r EmzPlatformStagingEnvironment-${commit}.zip EmzPlatformStagingEnvironment
rm -rf ./EmzPlatformStagingEnvironment