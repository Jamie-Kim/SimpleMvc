# SimpleMvc
Simple PHP framework startup code

The purpose of this framework is to build your project with MVC model.
It’s simple and easy to understand.
So, feel free to use it to your project.

It is developed on PHP version 5.4.

Actually, we don’t need manual for this framework because its’ too simple.
But it will support powerful things like SQL injection and clean URL, restFul Api etc along with MVC model.

Anyway, this is how to use the framework.

It is divided into three parts, app(your application), config(configuration), core(framework).
So, you application code will be placed in app folder.

Let's start wiht this process of the development with this simpleMvc framework.

1. copy all files to you web server directory.
2. modify rewrite mode(.htaccess) if you are using other web server other than Apache.
3. Fill the values in config/AppSettings.php
4. set routing information in app/app_main.php, this is the starting point of your web application.
5. create controller file in app/controller folder. File name should be same as class name.
6. create html template in  app/views/default/templates and set path define in app/views/view_paths.php
7. start coding with the controller you created in step 5.
8. add some necessary codes in app/app_code.
9. update and fix the framework code code if you needed.
10. use the updated core code to next your web application project.



