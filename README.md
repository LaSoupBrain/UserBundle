UserBundle
========================
This is a symfony bundle for user bundle.

## Installation
========================

### Step 1: Add this code to your composer.json file.
````
"repositories": [
        {
            "type": "package",
            "package": {
                "name": "dtw/user",
                "version": "dev-master",
                "source": {
                    "type": "git",
                    "url": "https://bitbucket.org/dtw_apac_php/user_bundle.git",
                    "reference": "origin/master"
                }
            }
        }
    ],
````
### Step 2: Require the package using composer.
````
composer require dtw/user:dev-master
````

### Step 3: Add this code in psr-4 line of composer.json file.
````
"Dtw\\UserBundle\\": "vendor/dtw/user/"
````

### Step 4: Run composer update.
````
composer update
````
### Step 5: In app/config/config.yml add this code in doctrine orm part .
````
orm:
    mappings:
        User:
            type: annotation
            is_bundle: false
            dir: %kernel.root_dir%/../vendor/dtw/user/Entity
            prefix: Dtw\UserBundle\Entity
            alias: User
````

### Step 6: Update your database by using schema update.
````
php bin/console doctrine:schema:update --force
````

### Step 7: Register the bundle in app/AppKernel.php.
````
new Dtw\UserBundle\DtwUserBundle(),
````

### Step 8: Add the routes in app/config/routing.yml.
````
dtw_user:
    resource: "../vendor/dtw/user/Resources/config/routing.yml"
    prefix:   /user
````

### Step 9: Add this code in app/config/security.yml for the password part.
````
security:
    encoders:
            Dtw\UserBundle\Entity\User:
                algorithm: bcrypt
                cost: 12

    providers:
        database_users:
            entity:
                class: Dtw\UserBundle\Entity\User
                property: email

    firewalls:
        # disables authentication for assets and the profiler, adapt it according to your needs
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            anonymous: ~
            form_login:
                login_path: dtw_user_login
                check_path: dtw_user_login
                default_target_path: dtw_user_index
                always_use_default_target_path: true

            logout:
                path:   dtw_user_logout
                target: dtw_user_login

    access_control:
        - { path: ^/user/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/forgot-password, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/send-email, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/email-sent, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/resetpassword/, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/updatepassword/, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/register, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/registered/, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/, roles: ROLE_ADMIN }

    access_denied_url: login
````

### Step 10: Add this code in app/config/config.yml for the path of uploaded images.
````
user_directory: '%kernel.project_dir%/web/uploads/images/users'
user-hover_directory: '%kernel.project_dir%/web/uploads/images/users/hover'
````

### Step 11: Add this code in app/config/parameters.yml for the configuration for email sending.
````
mailer_transport: smtp
mailer_host: smtp.gmail.com
mailer_user: <email>
mailer_password: <password>
mailer_user_name: <Email sender alias name>
mailer_port: 587
mailer_encryption: tls
````
 
### Step 12: Add also in the security.yaml under access_control the ROLE_SUPER_ADMIN.
Add this ROLE if you are going to use the Command Terminal for creating Super Admin Role. See example below.
````
    access_control:
        - { path: ^/admin/, roles: [ROLE_ADMIN, ROLE_SUPER_ADMIN] }
````
 
========================

Now you can use the user bundle for add,show,update and delete a user. You can use also the login module.



