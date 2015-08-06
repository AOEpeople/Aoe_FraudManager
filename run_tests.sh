#!/bin/bash
set -e

# check if this is a travis environment
if [ -n "${TRAVIS_BUILD_DIR}" ] ; then
  WORKSPACE=${TRAVIS_BUILD_DIR}
fi

if [ -z "${WORKSPACE}" ] ; then
  echo "No workspace configured, please set your WORKSPACE environment"
  exit
fi

# Create a working directory that is removed on exit
function cleanup {
  if [ -z "${SKIP_CLEANUP}" -a -n "${BUILDENV}" ]; then
    echo "Removing build directory ${BUILDENV}"
    rm -rf "${BUILDENV}"
  fi
}
trap cleanup EXIT
BUILDENV=`mktemp -d /tmp/buildenv.XXXXXXXX`
echo "Using build directory ${BUILDENV}"

# Grab the composer package name for the package we are testing
COMPOSER_PACKAGE_NAME=`jq -r '.name' "${WORKSPACE}/composer.json"`

# Initialize git
echo "Setting git user information"
if [ -z "`git config --get user.name`" ] ; then
    git config --global user.name "CI System"
fi
if [ -z "`git config --get user.email`" ] ; then
    git config --global user.email "ci@aoe.com"
fi

# Add a fake version into the composer.json of the package we are testing
jq '. |= .+ {version:"dev-current"}' "${WORKSPACE}/composer.json" > "${WORKSPACE}/composer.json.new"
mv -f "${WORKSPACE}/composer.json.new" "${WORKSPACE}/composer.json"

# Create artifact with update composer.json
ARTIFACT_NAME=`echo ${COMPOSER_PACKAGE_NAME} | tr / -`
git stash
cd "${WORKSPACE}"
mkdir "${BUILDENV}/artifacts"
git archive -o "${BUILDENV}/artifacts/${ARTIFACT_NAME}.zip" stash@{0}
git stash drop

# Checkout the testing framework
TESTSTAND="${BUILDENV}/teststand"
mkdir "${TESTSTAND}"
echo "Cloning AOEpeople/MageTestStand into ${TESTSTAND}"
git clone https://github.com/AOEpeople/MageTestStand.git "${TESTSTAND}"

# Add testing framework to PATH
PATH="${TESTSTAND}/tools:${TESTSTAND}/bin:${PATH}"

# Add the artifact repository to the testing framework
echo "Add the artifact repository to the testing framework"
jq ".repositories |= .+ [{type:\"artifact\", url:\"${BUILDENV}/artifacts\"}]" "${TESTSTAND}/composer.json" > "${TESTSTAND}/composer.json.new"
mv -f "${TESTSTAND}/composer.json.new" "${TESTSTAND}/composer.json"

# Use composer to require the package being tested
cd "${TESTSTAND}"
composer require --no-interaction --no-progress ${COMPOSER_PACKAGE_NAME}:dev-current

# Use modman to deploy any Magento modules
cd "${TESTSTAND}"
echo "Installing Magento Test Framework"
bash ./install.sh

# Run PHPUnit
cd "${TESTSTAND}/htdocs"
phpunit --colors -d display_errors=1
