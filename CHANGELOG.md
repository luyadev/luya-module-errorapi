# CHANGELOG

All notable changes to this project will be documented in this file. This project adheres to [Semantic Versioning](http://semver.org/).
In order to read more about upgrading and BC breaks have a look at the [UPGRADE Document](UPGRADE.md).

## 2.2.3 (?)

+ Fixed Data::getTrace() PHP Notice: Undefined index: class.
+ Fixed SentryAdapter::generateContext() method. When OS or Browser version is not detected, an exception was thrown (Undefined property).

## 2.2.2 (9. December 2021)

+ Slack token docs

## 2.2.1 (7. May 2020)

+ [#14](https://github.com/luyadev/luya-module-errorapi/pull/14) Add new app and yii version to sentry reports. Use app version as release name.

## 2.2.0 (22. October 2019)

+ [#13](https://github.com/luyadev/luya-module-errorapi/pull/13) Configure `$invalidServers` in any adapter to ignore certain server names.

## 2.1.0 (13. September 2019)

+ [#11](https://github.com/luyadev/luya-module-errorapi/issues/11) Add configuration for fingerprint in sentry adapter.

## 2.0.1 (6. August 2019)

+ [#10](https://github.com/luyadev/luya-module-errorapi/issues/10) Fixed bug with project slug..
+ Added travis & codeclimate.

## 2.0.0 (5. August 2019)

+ [#9](https://github.com/luyadev/luya-module-errorapi/pull/9) Switch to single adapters in order to make integrations more flexible. Therefore new sentry adapter integration is possible.

## 1.0.2 (21. July 2018)

+ [#6](https://github.com/luyadev/luya-module-errorapi/issues/6) Add more informations to slack channel.

## 1.0.1 (15. January 2018)

+ Add email message VarDumper for content, ensure input encoding, cleanup code.
+ Fixed unit tests, added new tests for mails and model validation.

## 1.0.0 (12, December 2017)

+ First stable release.
