<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <title>Registration page</title>

    <style>
        #strava-logo-wrapper {
            width: 30%;
            margin: auto;
            display: block;
            border: 1px solid black;
        }

        #strava-logo {
            width: 100%;
        }
    </style>
</head>

<body>
    <a id="strava-logo-wrapper" href="https://www.strava.com/oauth/authorize?client_id={{ $client_id }}&redirect_uri={{ $redirect_uri }}&response_type=code&approval_prompt=force&scope=activity:read_all">
        <img id="strava-logo" src="img/strava_logo.png" alt="Strava external account sync"/>
    </a>
</body>

</html>
