## Streamlabs Twitch App

This is the repository of the assignment/project provided by Streamlabs.

###How to run this application in your local

To run this application in your local computer, you need below tools installed in your local computer:

* **[Docker](https://www.docker.com/get-started/)**
* **[Git](https://desktop.github.com)**


#####Below are the steps

* First you need to clone the repository.
```
$ git clone https://github.com/williamgomes/streamlabs-sf.git
```

* Get inside the repository and create a copy of `.env.dist` file as `.env`.
```
$ cd streamlabs-sf
$ cp .env.dist .env
```

* `.env` file should be pretty much self-explanatory itself. Please put proper value against each environment variable as defined inside the file.

* Please make a copy of this edited `.env` file inside `www` directory.
```
$ cp .env www/.env
```

* Now go to the root directory of the project where the `docker-compose.yml` file is located. Then run below command with will setup all containers as defined inside `.yml` file.
```
$ docker-compose up
```
No need to worry about setting up anything manually. Docker will install everything including PHP packages from `composer` as well.

* After running above command there will be log that docker is installing 4 separate containers. After installation is done please open another **terminal** and run below command.
```
$ docker ps
```
This will give a list of all containers that is installed in the computer. containers which name starts with `streamlabs-sf` should be the containers created by this application.

* After setting up `.env` files properly, its time to check if our application can connect with the database. Run below command to get shell access of the container that has all our codes, assuming that the name for php container is `streamlabs-sf_php_1`.
```
$ docker exec -it streamlabs-sf_php_1 /bin/bash
```

* After getting the shell access, please run below command.
```
$ php bin/console doctrine:migrations:status
```

* If everything was correctly setup & application can access the mysql database, then the output of the above command will be as below.
```
== Configuration

    >> Name:                                               Application Migrations
    >> Database Driver:                                    pdo_mysql
    >> Database Host:                                      <provided_host_name>
    >> Database Name:                                      <provided_database_name>
    >> Configuration Source:                               manually configured
    >> Version Table Name:                                 migration_versions
    >> Version Column Name:                                version
    >> Migrations Namespace:                               Streamlabs\Migrations
    >> Migrations Directory:                               /var/www/app/../src/Streamlabs/Migrations
    >> Previous Version:                                   ********
    >> Current Version:                                    ********
    >> Latest Version:                                     ********
    >> Executed Migrations:                                0
    >> Executed Unavailable Migrations:                    2
    >> Available Migrations:                               2
    >> New Migrations:                                     4
```

* Now in order to setup all necessary tables, run below command to execute all migrations. 
```
$ php bin/console doctrine:migrations:migrate
```

* Now you will be able to access the application. Please take a note of the value for `SERVER_PORT` inside `.env` file. Access the application as below.

**http://localhost:SERVER_PORT/twitch**


###Future improvements & TO-DO's

1. Need to implement unit testing which is mandatory to setup CI/CD properly.
2. Implement Twitch oAuth Client as a service.
3. Add more events for backend event listener.


###Deployment plan in AWS

This is a very small app currently from code point of view but i would like to design the architecture in such a way that my whole infrastructure will be ready to handle any situation.

![My proposed architecture](https://drive.google.com/open?id=1Vs2C9x1s53xtWfQeoFwYWpg2Tu5kLPMh)

So, at the beginning, when there will be 100 reqs/day, i will spin up two small (_t2.micro_ or _t2.small_) on-demand ec2 instance. I prefer 2 instances because there will be no single-point-of-failure plus ther will be no interruption during deployment process. As my request will increase day by day, i will create an auto-scaling group which will spin up new EC2 instances besides the existing ones in order to support access requests. I will be able to setup different parameter for this spin up process, e.g. during a fixed timeline when the application gets more traffic/requests.

So far, if we do not have any extra functionality like user want to upload something or the application needs to export any report, this architecture should be fine. But if we have such functionality planned for future then we need to use some kind of storage container (e.g. _S3 bucket_) to store these files.