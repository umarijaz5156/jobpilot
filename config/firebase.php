<?php

declare(strict_types=1);

return [
    /*
     * ------------------------------------------------------------------------
     * Default Firebase project
     * ------------------------------------------------------------------------
     */

    'default' => env('FIREBASE_PROJECT', 'app'),

    /*
     * ------------------------------------------------------------------------
     * Firebase project configurations
     * ------------------------------------------------------------------------
     */

    'projects' => [
        'app' => [

            /*
             * ------------------------------------------------------------------------
             * Credentials / Service Account
             * ------------------------------------------------------------------------
             *
             * In order to access a Firebase project and its related services using a
             * server SDK, requests must be authenticated. For server-to-server
             * communication this is done with a Service Account.
             *
             * If you don't already have generated a Service Account, you can do so by
             * following the instructions from the official documentation pages at
             *
             * https://firebase.google.com/docs/admin/setup#initialize_the_sdk
             *
             * Once you have downloaded the Service Account JSON file, you can use it
             * to configure the package.
             *
             * If you don't provide credentials, the Firebase Admin SDK will try to
             * auto-discover them
             *
             * - by checking the environment variable FIREBASE_CREDENTIALS
             * - by checking the environment variable GOOGLE_APPLICATION_CREDENTIALS
             * - by trying to find Google's well known file
             * - by checking if the application is running on GCE/GCP
             *
             * If no credentials file can be found, an exception will be thrown the
             * first time you try to access a component of the Firebase Admin SDK.
             *
             */

            // 'credentials' => env('FIREBASE_CREDENTIALS', env('GOOGLE_APPLICATION_CREDENTIALS')),
            'credentials' => [
                'type' => 'service_account',
                'project_id' => 'jobpilot-mobile-app',
                'private_key_id' => 'feea06ac7831a2bff4f43cdc6c5240046594ff93',
                'private_key' => '-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDDg59plBS7Cl/+\ndX9Jmcb/sH9b/sTG1/lMsERlks7Bi3YEKnJP+w2dqNMtEJ1YzrAurTSNYhsoqzh7\n3EvJobPy5ybWIo/Tc6O6I1sf1M4Rw3FKZo2vTGvHDwqlHsSv82oBf4t7fi9A22Nn\nlQ+SQG5Nwb+DJ6ADAltUdlOWzNZojI/2gPkfDcm4ugV9rsVZ9PahulRlOI8nfMhW\nevswNwCYq0BwiR6FE1s3/mkD19vbu6DhFHmSYtbq/SXJUIVC8YUiCSVGTi7dOJRI\nNrEsZ2Vadm3rt0eiol/ILKB6GR2UYGBNcs5D7xLKq9SSQ7PVp7+y1mp9LTx1JLUO\nKJCx0yMHAgMBAAECggEAXi9DFSZL0krOMZPCrN8SmUwaHmQdwTh7lTD40gGl7mDT\ninA0P0lOptuUV4pAcm6nOuUfgth3AuFwxWI7dDecq3AlKlTd+lwjzGCJ3kyytKX1\nJoU+zeq/pNYfJ6op0CUvtOcPd38zHkhRm62YIbk+KeEi8/ibwbdZ7ddOrz+gLh5n\ndhiWDk9pFfMZ5zOixDxM1BZv6SGL2SMip8/aHfwyfbitt0O/wXa+67PNH5Hddikq\nnxrArt936zOUzYbdn98dJdGXCAN5ijN0GTJHYOprgFKldPLYZooLzSYX9xeSxGZO\nbGC+bJVMRiAOtrvBlK54Nij102odgdnvtO4SRoV9wQKBgQD80LjDON8D0+bHU+Ar\nN9qIjBdz+zxPs/2W+a+gFFlPU4DeVAcgtOnldvz8dwXZgpvQMXNkfe95ZEKEV379\n2b0QOEdWMaX9HMAYs9QjEPy6+dEhYHqxY8NgSDztYesCVIxYGYVQvySAm3Q2wWAd\n25s2xXxlSIVu/Li+VTB5n+r35wKBgQDF+h3Fu3RC7Vyz7HGvRg6EjweP/LuncJYB\nGZGt4kRtCTuUCYFb2y+L5z2JCHAMMSCFvC2wZah6o6P/y/D7POSYCFAT0DUSDrpS\nsLSkm3BF/Fb8pNuUYKHSvPSIDUs51/zP6i16TAId6QaNTu9flHqYYJMx5+xasSFk\nPWTqWHqX4QKBgQDPdihoa373ESjMYZQPYyI981g7oOt5LGbpQbRRVOGFKy0RRTsk\nJ9HYr7AjLTjrqTZbvnjG+mFN6Gx9VZ+siMWRTd2cadmgv7sTil6G+CWs+dwX26hT\ncV6e4Ci/VB8aJm+UzDyOaox1zRus4zsQxWm1pJHUO5Lj5RdleVryM70J+QKBgCof\nZWZE1B/JUQgXLrkUNtKNfBZut56QndnuDsjoc5afeEWvIA7jO+KQeM9HNE/jw/+w\nYig9+PLfDm3Gfqd19U1Dt4X/rssAxzQA1O6RA/pgDkIC8ZVIWiX0fjLUYUUVZ3z1\nXme+9FRY2EQIn3W+qbbyFV9w8SD6vxgM2APkf3EBAoGBAL/MgwquOVF7K6NQGV1h\nNskfEoj+3IUmmiFMuiGu8FCxQI8PlBkySemmhw2yMw2tHITs+ueLshdds9gXmJde\n4MUJTpuV7+NZj7ksTxHw2lpyg9YMUL7sejSNy4WzzuBu0xR0ptxeyciyM87yWPyc\nyVRafLkwg2BQmC8HDuqiMle9\n-----END PRIVATE KEY-----\n',
                'client_email' => 'firebase-adminsdk-kzrtq@jobpilot-mobile-app.iam.gserviceaccount.com',
                'client_id' => '113215739113590005497',
                'auth_uri'=> 'https://accounts.google.com/o/oauth2/auth',
                'token_uri'=> 'https://oauth2.googleapis.com/token',
                'auth_provider_x509_cert_url'=> 'https://www.googleapis.com/oauth2/v1/certs',
                'client_x509_cert_url'=> 'https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-kzrtq%40jobpilot-mobile-app.iam.gserviceaccount.com',
                'universe_domain'=> 'googleapis.com'
            ],

            /*
             * ------------------------------------------------------------------------
             * Firebase Auth Component
             * ------------------------------------------------------------------------
             */

            'auth' => [
                'tenant_id' => env('FIREBASE_AUTH_TENANT_ID'),
            ],

            /*
             * ------------------------------------------------------------------------
             * Firestore Component
             * ------------------------------------------------------------------------
             */

            'firestore' => [

                /*
                 * If you want to access a Firestore database other than the default database,
                 * enter its name here.
                 *
                 * By default, the Firestore client will connect to the `(default)` database.
                 *
                 * https://firebase.google.com/docs/firestore/manage-databases
                 */

                // 'database' => env('FIREBASE_FIRESTORE_DATABASE'),
            ],

            /*
             * ------------------------------------------------------------------------
             * Firebase Realtime Database
             * ------------------------------------------------------------------------
             */

            'database' => [

                /*
                 * In most of the cases the project ID defined in the credentials file
                 * determines the URL of your project's Realtime Database. If the
                 * connection to the Realtime Database fails, you can override
                 * its URL with the value you see at
                 *
                 * https://console.firebase.google.com/u/1/project/_/database
                 *
                 * Please make sure that you use a full URL like, for example,
                 * https://my-project-id.firebaseio.com
                 */

                'url' => env('FIREBASE_DATABASE_URL'),

                /*
                 * As a best practice, a service should have access to only the resources it needs.
                 * To get more fine-grained control over the resources a Firebase app instance can access,
                 * use a unique identifier in your Security Rules to represent your service.
                 *
                 * https://firebase.google.com/docs/database/admin/start#authenticate-with-limited-privileges
                 */

                // 'auth_variable_override' => [
                //     'uid' => 'my-service-worker'
                // ],

            ],

            'dynamic_links' => [

                /*
                 * Dynamic links can be built with any URL prefix registered on
                 *
                 * https://console.firebase.google.com/u/1/project/_/durablelinks/links/
                 *
                 * You can define one of those domains as the default for new Dynamic
                 * Links created within your project.
                 *
                 * The value must be a valid domain, for example,
                 * https://example.page.link
                 */

                'default_domain' => env('FIREBASE_DYNAMIC_LINKS_DEFAULT_DOMAIN'),
            ],

            /*
             * ------------------------------------------------------------------------
             * Firebase Cloud Storage
             * ------------------------------------------------------------------------
             */

            'storage' => [

                /*
                 * Your project's default storage bucket usually uses the project ID
                 * as its name. If you have multiple storage buckets and want to
                 * use another one as the default for your application, you can
                 * override it here.
                 */

                'default_bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET'),

            ],

            /*
             * ------------------------------------------------------------------------
             * Caching
             * ------------------------------------------------------------------------
             *
             * The Firebase Admin SDK can cache some data returned from the Firebase
             * API, for example Google's public keys used to verify ID tokens.
             *
             */

            'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),

            /*
             * ------------------------------------------------------------------------
             * Logging
             * ------------------------------------------------------------------------
             *
             * Enable logging of HTTP interaction for insights and/or debugging.
             *
             * Log channels are defined in config/logging.php
             *
             * Successful HTTP messages are logged with the log level 'info'.
             * Failed HTTP messages are logged with the log level 'notice'.
             *
             * Note: Using the same channel for simple and debug logs will result in
             * two entries per request and response.
             */

            'logging' => [
                'http_log_channel' => env('FIREBASE_HTTP_LOG_CHANNEL'),
                'http_debug_log_channel' => env('FIREBASE_HTTP_DEBUG_LOG_CHANNEL'),
            ],

            /*
             * ------------------------------------------------------------------------
             * HTTP Client Options
             * ------------------------------------------------------------------------
             *
             * Behavior of the HTTP Client performing the API requests
             */

            'http_client_options' => [

                /*
                 * Use a proxy that all API requests should be passed through.
                 * (default: none)
                 */

                'proxy' => env('FIREBASE_HTTP_CLIENT_PROXY'),

                /*
                 * Set the maximum amount of seconds (float) that can pass before
                 * a request is considered timed out
                 *
                 * The default time out can be reviewed at
                 * https://github.com/kreait/firebase-php/blob/6.x/src/Firebase/Http/HttpClientOptions.php
                 */

                'timeout' => env('FIREBASE_HTTP_CLIENT_TIMEOUT'),

                'guzzle_middlewares' => [],
            ],
        ],
    ],
];