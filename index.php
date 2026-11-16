<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description"
        content="TriSeva is a student-developed community service portal.">
  <title>TriSeva | Community Service Portal</title>
  <style>
    body {
      font-family: system-ui, sans-serif;
      max-width: 720px;
      margin: 70px auto;
      padding: 24px;
      color: #172033;
      background: #f4f7fc;
      line-height: 1.7;
    }
    main {
      background: white;
      padding: 32px;
      border-radius: 16px;
    }
    a {
      display: inline-block;
      margin: 12px 8px 0 0;
      padding: 12px 20px;
      background: #002147;
      color: white;
      border-radius: 8px;
      text-decoration: none;
    }
    .notice { color: #475569; font-size: 14px; }
  </style>
</head>
<body>
  <main>
    <h1>Welcome to TriSeva</h1>
    <p>
      TriSeva is a student-developed community service portal
      designed to support volunteer activities and coordination.
    </p>
    <p>
      Explore the portal to learn about its available features.
    </p>
    <a href="login.php">Volunteer Sign In</a>
    <a href="register.php">Create Account</a>
    <p class="notice">
      Student project. Not an official government website.
      Affiliation with any institution or organization should
      be stated only where authorized.
    </p>
    <p class="notice">
      Please review the privacy policy before submitting personal data.
    </p>
  </main>
</body>
</html>