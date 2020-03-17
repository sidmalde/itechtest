###iTech Technical Test Brief
**Hi there!**

Congratulations on progressing to the next stage of our interview process, and thank you for agreeing to take the time to complete our technical test.

You can either run the test in your existing local environment, or download a Docker container that's ready to go.

If you want to use Docker, please see the instructions further down the page - please note that Docker currently does not run on Windows 10 Home Edition.

**Local setup**

1. The test requires PHP 7.1 or higher and a MySQL server.
2. Configure your webserver document root to be `/WebsiteCode/Public`. In Apache you might add this line to your config:
`DocumentRoot /var/www/html/itechTest/WebsiteCode/public`.
3. The home page has the instructions for the test.


**Docker setup**

1. Start the container with the terminal command `docker run -p 80:80 -v [PATH TO THIS FOLDER]/WebsiteCode:/var/www/html -it roundrobintreegenerator/itech_developer_test`
2. You should now be 'inside' the Docker container. Run the following commands:
3. `cd /var/www/html/`
4. `./composer.phar install`
5. If something goes wrong, make sure your drive is shared with Docker:
    * Windows: Right-click Docker icon | Settings | Shared Drives
    * Mac: Right-click Docker icon | Preferences | File Sharing
6. Browse to `http://127.0.0.1` in your favorite browser and follow the instructions on the home page.


If anything goes wrong or you have any questions please don't hesitate to get in touch.

Best of luck!