<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">

    <title>Melyssa Framework</title>

    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:200,300">
    <style type="text/css">
        html, body {
            height: 100%;
        }

        body {
            background-color: #f1f2f3;
            font-family: "Lato", Helvetica, Arial, sans-serif;
            color: rgba(0,0,0,.8);
            font-weight: 300;
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
        }

        .main-container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .container {
            text-align: center;
            display: inline-block;
        }

        h1 {
            font-weight: 200;
            font-size: 4em;
        }

        .btn {
            padding: 12px 16px;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            color: #000;
            border: 1px solid rgba(0,0,0,.2);
            transition: background-color .2s linear;
        }
        .btn:hover,
        .btn:focus {
            background-color: #fff;
        }
    </style>
  </head>
  <body>

      <main class="main-container">
          <div class="container">
              <h1><?php echo $this->tradutor->getString('Welcome to') ?> Melyssa Framework</h1>
              <a href="/doc" target="_blank" class="btn"><?php echo $this->tradutor->getString('Documentation') ?></a>
              <a href="https://github.com/jhouie/MelyssaFramework" target="_blank" class="btn">Github</a>
              <p><small>Currently v0.0.1</small></p>
          </div>
      </main>

  </body>
</html>
