<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">

    <title>Erro 404</title>

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
              <h1>Erro 404</h1>
              <a href="/" class="btn">&laquo; <?php echo $this->tradutor->getString('Go back') ?></a>
          </div>
      </main>

  </body>
</html>
