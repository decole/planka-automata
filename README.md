# Автоматизация для планки

- Для карточек Planka с определенным лейблом "Проектная карточка" нумеровать автоматически префиксом `[UBS-<number>]`
- WebHook для Sentry, создавать карточку в проекте с лейблом "Ошибка"
- Уведомлять в телеграм о подходящем дедлайне карточки
- Уведомлять о событиях (нотификациях) пользователя в планке

Использовать RoadRunner

https://github.com/Baldinof/roadrunner-bundle

Some files have been created and/or updated to configure your new packages.
Please review, edit and commit them: these files are yours.

baldinof/roadrunner-bundle  instructions:

----

`bin/rr serve`

----

* Download RoadRunner locally: vendor/bin/rr get --location bin/
* Run your application: bin/rr serve -c .rr.dev.yaml --debug
* For production, use: bin/rr serve
* Visit http://localhost:8080

