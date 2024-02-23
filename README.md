# Sample employee management app

This is an example of a simple employee management app. It is a simple web application that allows you to add, edit, and delete employees.
It is built using Symfony 7 & PHP 8.3.

It is a server rendered app built on Symfony that uses Twig for templating and Bootstrap for styling. 
I have chosen this combination because it is a simple and effective way to build a small web application in PHP.
Chart is created using Chart.js and all assets are loaded using Stimulus.js and AssetMapper, because it is a simple modern way without need for Node.js or any bundler.

There are still many improvements that could be made to this app, such as adding better UI layout / design, pagination, adding tests, etc.

## Installation
development Docker environment is provided. To run the app in development mode
run `docker compose up -d --build` in `docker` directory

run `docker compose exec php composer install` to install dependencies

run `docker compose exec php php bin/console asset-map:compile` to compile assets

## Usage
The app will be available at `http://localhost`

## Extensibility
Example of how to add a new field to employee is in `Add employee gender` https://github.com/martincivan/employee_list/commit/d2e19a9e0d5aeb10eca7bde581b6d66437af7651 commit:
Basically the field has to be added to 4 places:
- to `Employee` entity
- to `EmployeeType` form - UI for adding / editing employees
- to repository impl: `XmlEmployeeRepository`
- to `_employee_list_item.html.twig` to show the new field in the list of employees
