# Инструкция по установке проекта
## Требования

Перед установкой убедитесь, что у Вас установлены:

PHP 8.2+
Composer
PostgreSQL
Symfony CLI

## Клонирование проекта
git clone git@github.com:explt13/task_manager.git TaskManager
cd TaskManager

## Конфигурация
cp .env.example .env
Заменить USERNAME, PASSWORD, DATABASE актуальными данными
DATABASE_URL="postgresql://USERNAME:PASSWORD@127.0.0.1:5432/DATABASE?serverVersion=16&charset=utf8"

## Установить зависимости
composer install

## Создание БД
php bin/console doctrine:database:create
## Установить миграции
php bin/console doctrine:migrations:migrate
## Заполнить БД тестовыми данными
php bin/console doctrine:fixtures:load

## Запуск сервера
symfony serve

## API тестирование
Postman коллекция нахоидтся в папке /docs

Получение всех задач
GET /api/tasks — возвращает список всех задач.
Создание новой задачи
POST /api/tasks — принимает данные задачи (название, описание, статус) и сохраняет её в базе данных.
Обновление существующей задачи
PUT /api/tasks/{id} — обновляет информацию о задаче по ID (например, название или описание).
Удаление задачи
DELETE /api/tasks/{id} — удаляет задачу по ID.
Получение задачи по ID
GET /api/tasks/{id} — возвращает информацию о задаче по её ID.
