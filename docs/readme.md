Command line for generate files :

a. Setting up for database :
#1 making model with it's migration and seeder file : php artisan make:model yourModelName --migration --seeder
#1.1 migration table -> this will make a table inside the database as result from table specifitaion was made up : php artisan migrate

b. Generate request and resource files :
#1 generate request : php artisan make:request yourRequestFileName
#2 generate resource : php artisan make:resource yourResourceFileName

c. Generate unit test :
#1 command : php artisan make:test yourUnitTestFileName

d. Generate controller file :
#1 command : php artisan make:controller yourControllerFileName

e. Generate middleware file :
#1 command : php artisan make:middleware yourMiddlewareFileName