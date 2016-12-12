# SimpleMvc
Simple PHP framework startup code

The purpose of this framework is to build your project with MVC model with simple design.
It’s simple and easy to understand.
So, feel free to use it to your project.

It is developed based on PHP v5.4.

Actually, we don’t need manual for this framework since its’ too simple.
But it will support powerful things like SQL injection and clean URL, restFul Api etc along with MVC model.

This is how to use the framework.

It is divided into three parts, app(your application), config(configuration), core(framework).
So, your all application code will be placed inside app folder.

Let's follow these steps for starting of the development with this simpleMvc framework.

1. copy all files to your web server directory.
2. modify the rewrite mode(.htaccess) if you are using other web server other than Apache.
3. Fill the values in config/AppSettings.php.
4. set routing information in app/app_main.php, this is the starting point of your web application.
5. create controller file in app/controller folder. File name should be same with the class name.
6. create html template in app/views/default/templates and define the file path in app/views/view_paths.php
7. start coding with the controller you created in step 5.
8. add some necessary codes in app/app_code rather than modifing the core.
9. update and fix the core if you need.
10. use the updated core for your next project.



